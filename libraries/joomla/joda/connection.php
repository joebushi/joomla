<?php
/**
 * @version     $Id: connection.php 253 2008-05-22 14:32:40Z plamendp $
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
class JConnection extends PDO
{
    /**
     * Database type (mysql, pgsql, mssql, etc.)
     *
     * @var string
     */
    var $dbtype                    = "";


    /**
     * PDO Dtatement class.
     *
     * It's an object holding the result of queries, prepared statement, etc.
     *
     * @var object PDOStatement
     */
    var $statement                 = null;

    /**
     * Database host name or IP address
     *
     * @var string
     */
    var $host                      = "";

    /**
     * Database host's port number
     *
     * @var string
     */
    var $port                      = "";

    /**
     * Database name
     *
     * @var string
     */
    var $database                  = "";

    /**
     * Database username
     *
     * @var string
     */
    var $user                      = "";

    /**
     * Database user's password
     *
     * @var string
     */
    var $password                  = "";

    /**
     * PDO Database Driver Options
     *
     * @var array An array of Key=>Value PDO options
     */
    var $driver_options            = array();

    /**
     * Database encoding
     *
     * @var string
     */
    var $encoding                  = "";

    /**
     * Transaction Isolation level used if and when transactions are involved.
     *
     * See {@link Joda} class for predefined constants.
     *
     * @var integer {@link Joda::READ_COMMITED}|{@link Joda::REPEATABLE_READ}|{@link Joda::READ_UNCOMMITTED}|{@link Joda::SERIALIZABLE}
     */
    var $transaction_isolevel      = Joda::READ_COMMITED;

    /**
     * Autocommit enabled or disabled.
     *
     * Defines if next query set will be executed in transaction block or not.
     *
     * @var bool <var>True</var>=No Transactions, <var>False</var>=Use Transactions
     */
    var $autocommit                = true;



    /**
     * Class constructor
     *
     * @param
     * @return object JConnection
     */
    function __construct()
    {
        $dsn = $this->dbtype.":port=".$this->port.";host=" . $this->host . ";dbname=" . $this->database;
        parent::__construct($dsn, $this->user, $this->password, $this->driver_options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array("JStatement"));
    }

    /**
     * Return an instance of JConnection's descendant class (database specific).
     *
     * @param array Options/Configuration
     * @return object JConnection Subclass
     */
     function &getInstance($options)
    {
        $dbtype = $options["dbtype"];

        $file = dirname(__FILE__) .DS. "connection" .DS. $dbtype . ".php";
        require_once($file);

        $class = "JConnection" . $dbtype;
        $instance = new $class($options);

        return $instance;
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
     function Commit()
    {
        parent::commit();
    }


    /**
     * Roll back transaction
     *
     * @param
     * @return
     */
     function Rollback()
    {
        parent::rollBack();
    }


    /**
     * Execute SQL queries
     *
     * @param
     * @return
     */
     function doQuery($sql, $parameters=array())
    {
        foreach ( $sql as $query )
        {
            $statement = $this->prepare($query);
            $statement->execute();
        }
        $this->statement = $statement;
        return $statement;
    }


    /**
     * Execute SQL queries, enclosing them in a transaction if Autocommit mode is off.
     *
     * @param array Arrays of sql queries
     * @return
     */
     function Query($sql)
    {
        if ( ! $this->autocommit )
        {
            $this->beginTransaction();
        }

        $result = $this->doQuery($sql);

        if  ( ! $this->autocommit )
        {
            $this->Commit();
        }
    }



    /**
     * Description
     *
     * @param
     * @return
     */
    function FetchDataAsTable($sql)
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
    function FetchDataAsObjects($sql)
    {
        $this->Query($sql);
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }



    function getFieldsMeta()
    {
        $count = $this->statement->columnCount();
        $i = 0;
        while ( $fieldmeta = $this->statement->getColumnMeta($i++) )
        {
            $result["'".$fieldmeta["name"]."'"] = $fieldmeta;
        }
        return $result;
    }


    function recordCount()
    {
        return $this->statement->rowCount();
    }

} //JConnection


?>
