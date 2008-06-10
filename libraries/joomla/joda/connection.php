<?php
/**
 * @version     $Id$
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 *
 * @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 */

/**
 * Check to ensure this file is within the rest of the framework
 */
defined( 'JPATH_BASE' ) or die();


/**
 * Database Connection Class
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 * @author      Plamen Petkov <plamendp@zetcom.bg>
 *
 */
abstract class JConnection extends PDO
{

    /**
     * PDO Dtatement class.
     *
     * It's an object holding the result of queries, prepared statement, etc.
     *
     * @var object JStatement
     */
    protected $_statement                 = null;

    /**
     * Database host name or IP address
     *
     * @var string
     */
    protected $_host                      = "localhost";

    /**
     * Database host's port number
     *
     * @var string
     */
    protected $_port                      = "";

    /**
     * Database name
     *
     * @var string
     */
    protected $_database                  = "";

    /**
     * Database username
     *
     * @var string
     */
    protected $_user                      = "";

    /**
     * Database user's password
     *
     * @var string
     */
    protected $_password                  = "";

    /**
     * PDO Database Driver Options
     *
     * @var array An array of Key=>Value PDO options
     */
    protected $_driver_options            = array();


    /**
     * Transaction Isolation level used if and when transactions are involved.
     *
     * See {@link Joda} class for predefined constants.
     *
     * @var integer {@link Joda::READ_COMMITED}|{@link Joda::REPEATABLE_READ}|{@link Joda::READ_UNCOMMITTED}|{@link Joda::SERIALIZABLE}
     */
    protected $_transaction_isolevel      = Joda::READ_COMMITED;

    /**
     * Autocommit enabled or disabled.
     *
     * Defines if next query set will be executed in transaction block or not.
     *
     * @var bool <var>True</var>=No Transactions, <var>False</var>=Use Transactions
     */
    protected $_autocommit                = true;


    /**
     * The name of this connection as per the backend configuration
     *
     * @var string
     */
    protected $_name = "";


    /**
     * Table name prefix - all tables are prefixed with this string, usually "jos_".
     * Placeholder "#__" used in SQL queries must be replaced by this very prefix.
     *
     * @var string
     */
    protected $_table_prefix = "jos_";



    /**
     * Class constructor
     *
     * @param
     * @return object JConnection
     */
    function __construct()
    {
        $dsn = $this->_drivername.":port=".$this->_port.";host=" . $this->_host . ";dbname=" . $this->_database;
        parent::__construct($dsn, $this->_user, $this->_password, $this->_driver_options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array("JStatement"));
    }

    /**
     * Return an instance of JConnection's descendant class (singleton)
     *
     * @param array Options/Configuration
     * @return object JConnection
     */
    function &getInstance($options, $connectionname)
    {
        static $instances;

        if (!isset( $instances )) {
            $instances = array();
        }

        $signature = serialize( array_merge($options, array($connectionname)) );

        if (empty($instances[$signature])) {
            $driver = $options["driver"];

            $file = dirname(__FILE__) .DS. "connection" .DS. $driver . ".php";
            require_once($file);

            $class = "JConnection" . $driver;
            $instance = new $class($options);
            $instance->_name = $connectionname;
            $instances[$signature] = & $instance;
        }

        echo "<I>$connectionname</I><BR>";

        return $instances[$signature];
    }


    /**
     * Set transaction isolation level
     *
     * Note: This method does NOT turn ON using transactions. Property {@link $_autocommit} must be set to TRUE.
     *
     * @param integer {@link Joda::READ_COMMITED}|{@link Joda::REPEATABLE_READ}|{@link Joda::READ_UNCOMMITTED}|{@link Joda::SERIALIZABLE}
     * @return
     */
    function setTransactionIsoLevel($level)
    {
        $this->_transaction_isolevel = $level;
    }


    /**
     * Start a transaction session
     *
     * @param
     * @return
     */
    function beginTransaction()
    {
        parent::beginTransaction();
    }

    /**
     * Commit transaction
     *
     * @param
     * @return
     */
    function commit()
    {
        parent::commit();
    }


    /**
     * Roll back transaction
     *
     * @param
     * @return
     */
    function rollback()
    {
        parent::rollBack();
    }


    /**
     * Execute SQL queries.
     *
     * @param array
     * @return object JStatement
     */
    function doQuery($sql, $parameters=array())
    {
        $result = false;
        foreach ( $sql as $query )
        {
            $statement = $this->prepare($query);
            $result = $statement->execute();
        }

        $this->_statement = $statement;

        return $result;
    }


    /**
     * Execute SQL queries, enclosing them in a transaction if Autocommit mode is off.
     *
     * NOTE:Currently Isolation Levels not implemented yet! Using the server default one!
     *
     * @param array Arrays of sql queries
     * @return
     */
    function query($sql)
    {
        $this->_statement = null;

        $result = false;
        
        if ( ! $this->_autocommit ) {
            $this->beginTransaction();
            if ( $this->doQuery($sql) ) {
                $result = $this->Commit();
            }
            else
            {
                $result = $this->Rollback();
            }
        }
        else
        {
            $result = $this->doQuery($sql);
        }
        
        return $result;
    }



    /**
     * Description
     *
     * @param
     * @return
     */
    function fetchDataAsTable($sql)
    {
        $this->query($sql);
        return $this->_statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Description
     *
     * @param
     * @return
     */
    function fetchDataAsObjects($sql)
    {
        $this->query($sql);
        return $this->_statement->fetchAll(PDO::FETCH_OBJ);
    }



    /**
     * Description
     *
     * @param
     * @return array
     */
    function getFieldsMeta()
    {
        $result = array();
        $count = $this->_statement->columnCount();
        if ( $count <= 0 ) {
            return $result;
        }

        $i = 0;
        while ( $fieldmeta = $this->_statement->getColumnMeta($i++) )
        {
            $result["'".$fieldmeta["name"]."'"] = $fieldmeta;
        }
        return $result;
    }


    /**
     * Description
     *
     * @param
     * @return array
     */
    function recordCount()
    {
        return $this->_statement->rowCount();
    }


    /**
     * Description
     *
     * @param
     * @return array
     */
    function enableTransactions()
    {
        $this->_autocommit = false;
    }


    /**
     * Description
     *
     * @param
     * @return array
     */
    function disableTransactions()
    {
        $this->_autocommit = true;
    }




    /**
     * Return this connection's driver name
     *
     * @param
     * @return string
     */
    function getDriverName()
    {
        return $this->_drivername;
    }









} //JConnection


?>
