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
     * Database type (mysql, pgsql, mssql, etc.)
     *
     * @var string
     */
    protected $driver                    = "";


    /**
     * PDO Dtatement class.
     *
     * It's an object holding the result of queries, prepared statement, etc.
     *
     * @var object JStatement
     */
    public $statement                 = null;

    /**
     * Database host name or IP address
     *
     * @var string
     */
    public $host                      = "";

    /**
     * Database host's port number
     *
     * @var string
     */
    public $port                      = "";

    /**
     * Database name
     *
     * @var string
     */
    public $database                  = "";

    /**
     * Database username
     *
     * @var string
     */
    public $user                      = "";

    /**
     * Database user's password
     *
     * @var string
     */
    public $password                  = "";

    /**
     * PDO Database Driver Options
     *
     * @var array An array of Key=>Value PDO options
     */
    public $driver_options            = array();

    /**
     * Database encoding
     *
     * @var string
     */
    public $encoding                  = "";

    /**
     * Transaction Isolation level used if and when transactions are involved.
     *
     * See {@link Joda} class for predefined constants.
     *
     * @var integer {@link Joda::READ_COMMITED}|{@link Joda::REPEATABLE_READ}|{@link Joda::READ_UNCOMMITTED}|{@link Joda::SERIALIZABLE}
     */
    public $transaction_isolevel      = Joda::READ_COMMITED;

    /**
     * Autocommit enabled or disabled.
     *
     * Defines if next query set will be executed in transaction block or not.
     *
     * @var bool <var>True</var>=No Transactions, <var>False</var>=Use Transactions
     */
    public $autocommit                = false;


    /**
     * The name of this connection as per the backend configuration
     *
     * @var string
     */
    public $name = "";


    /**
     * Class constructor
     *
     * @param
     * @return object JConnection
     */
    function __construct()
    {
        $dsn = $this->driver.":port=".$this->port.";host=" . $this->host . ";dbname=" . $this->database;
        parent::__construct($dsn, $this->user, $this->password, $this->driver_options);
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
            $instance->name = $connectionname;
            $instances[$signature] = & $instance;
        }



        return $instances[$signature];
    }


    /**
     * Set transaction isolation level
     *
     * Note: This method does NOT turn ON using transactions. Property {@link $autocommit} must be set to TRUE.
     *
     * @param integer {@link Joda::READ_COMMITED}|{@link Joda::REPEATABLE_READ}|{@link Joda::READ_UNCOMMITTED}|{@link Joda::SERIALIZABLE}
     * @return
     */
    function setTransactionIsoLevel($level)
    {
        $this->transaction_isolevel = $level;
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
        $this->statement = $statement;
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

        $result = false;
        if ( ! $this->autocommit ) {
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
        $this->Query($sql);
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Description
     *
     * @param
     * @return
     */
    function fetchDataAsObjects($sql)
    {
        $this->Query($sql);
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
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
        $count = $this->statement->columnCount();
        if ( $count <= 0 ) {
            return $result;
        }

        $i = 0;
        while ( $fieldmeta = $this->statement->getColumnMeta($i++) )
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
        return $this->statement->rowCount();
    }

} //JConnection


?>
