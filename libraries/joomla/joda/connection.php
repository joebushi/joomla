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


//FIXME: Start Try/Catch wrapping of queires
//TODO: Handle "In-Transaction" status
//TODO: If you need metadata - select * from table where 1=1  (?!?!)
//TODO: Prepared statements? Optional? Parameters?
//TODO: Multi-line SQL not allowed (I mean semicolons NOT allowed! /';'/!!!!
//TODO: Log queries in debug mode


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
     * PDO Statement object
     *
     * Holding the result set (if any) of the last executed query
     *
     * @var object PDOStatement
     */
    protected $_resultset                 = null;

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
     * Relation/Table name prefix
     *
     * @var string
     */
    protected $_relation_prefix = Joda::DEFAULT_RELATION_PREFIX;

    /**
     * Debug mode
     *
     * @var integer
     */
    protected $_debug = 0;

    /**
     * Query counter
     *
     * @var integer
     */
    protected $_ticker = 0;

    /**
     * Query log array
     *
     * @var array
     */
    protected $_log = 0;

    
    
    /**
     * Class constructor
     *
     * @param array Driver specific PDO driver options array, merged to parent's one
     * @return object JConnection
     */
    function __construct($driver_options=array())
    {
    	$dsn = $this->_drivername.":port=".$this->_port.";host=" . $this->_host . ";dbname=" . $this->_database;
        parent::__construct($dsn, $this->_user, $this->_password);

        // set global PDO driver options(apply to all connections)
        $this->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            $instance->_relation_prefix = $options["prefix"];
            $instances[$signature] = & $instance;
        }


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
     * @param array An array of strings (SQL queries); NO ending semicolons! (';')
     * @param array An array of Query Parameter.
     * @return boolean Result of the execution
     */
    protected function _doQueries( $sql )
    {
        $result = true;
        $statement = new PDOStatement();
        foreach ( $sql as $query ) {
            if ( is_a($statement, "PDOStatement") ) {
               $statement->closeCursor();
            }
            $statement = $this->query($query);
            if ( ! $statement ) {
                break;
            }
        }
        $this->_resultset = $statement;
        $result = is_a($statement, "PDOStatement");
        return $result;
    }


    /**
     * Execute SQL queries
     *
     * Enclosing the whole batch in a transaction block if Autocommit mode is off.
     *
     * NOTE: Currently Isolation Levels are not implemented! Using the server default one!
     *
     * @param array Arrays of sql queries
     * @return boolean
     */
    function execQueries($sql)
    {
        $result = false;

        // Start transaction if needed
        if ( ! $this->_autocommit ) {
        	echo "IN TRANSACTION<BR>";
            $this->beginTransaction();
        }

        // Execute all query strings, set exec_result
        $resultset = new PDOStatement();
        print_r($resultset);
        foreach ( $sql as $query ) {
        	// Close PDO cursor to prevent query failures. 
        	// According to PDO Documentation, if data has not been fetched (all), 
        	// cursor must be closed before executing next query
            if ( is_a($resultset, "PDOStatement") ) {
               $resultset->closeCursor();
               echo "HERE 1<br>";
            }
            
            $resultset = $this->query($query);
            echo "HERE 3 <BR>";
            print_r($resultset);
            
            // Break on failure
            if ( ! $resultset ) {
               echo "HERE 2<br>";
            	break;
            }
        }
        // Set the Dataset's result set to the last one in execution list.
        $this->_resultset = $resultset;
        
        // It is OK only if the result is a PDOStatement object
        $exec_result = is_a($this->_resultset, "PDOStatement");
		
        // So far ... 
        $result = $exec_result;
        
        // Finish transaction if needed
        if ( ! $this->_autocommit ) {
        	if ( $exec_result ) {
        		$result = $this->Commit();
        	}
        	else {
        		$result = $this->Rollback();
        	}
        }
        
        
        return $result;
    }

    
    /**
     * Fetches next data row from the set
     * 
     * @param
     * @return
     */
    function fetchNext($datastyle)
    {
    	$fetch_style = $this->toFetchStyle($datastyle);
    	return $this->_resultset->fetch($fetch_style);
    }
    
    
    /**
     * Fetches all data from result set
     * 
     * @param
     * @return
     */
    function fetchAllData($datastyle)
    {
    	$fetch_style = $this->toFetchStyle($datastyle);
        return $this->_resultset->fetchAll($fetch_style);
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
        $count = $this->_resultset->columnCount();
        if ( $count <= 0 ) {
            return $result;
        }

        $i = 0;
        while ( $fieldmeta = $this->_resultset->getColumnMeta($i++) )
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
        return $this->_resultset->rowCount();
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




    /**
     * Return this connection's relation prefix (usualy "jos_")
     *
     * @param
     * @return string
     */
    function getRelationPrefix()
    {
        return $this->_relation_prefix;
    }


    /**
     * Translate Joda DATASTYLE to PDO Fetch Style
     * 
     * @param integer Data Style (see Joda constants)
     */
    function toFetchStyle($datastyle)
    {
        switch ($datastyle) {
        	case ($datastyle == Joda::DATASTYLE_ASSOC)	 : $fetchstyle = PDO::FETCH_ASSOC;
        													break;
        	case ($datastyle == Joda::DATASTYLE_OBJECTS) : $fetchstyle = PDO::FETCH_OBJ;
        													break;
        	default : $fetchstyle = PDO::FETCH_ASSOC;
        }
    	return $fetchstyle;
    }
    

    


} //JConnection


?>
