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
 * Dataset Class
 *
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 * @author      Plamen Petkov <plamendp@zetcom.bg>
 *
 */
class JDataset extends JObject
{

    /**
     * Connection Object
     *
     * @var object JConnection
     */
    public $connection = null;

    /**
     * Array of SQL queries to be executed
     *
     * @var array
     */
    protected $_sql = array();


    /**
     * Fetch  Style to use if not specified
     *
     * @var integer
     */
    protected $_fetchstyle = PDO::FETCH_ASSOC;


    /**
     * Query Builder
     *
     * @var object JQueryBuilder
     */
    protected $_querybuilder = null;


    /**
     * Description
     *
     * @param
     * @return
     */
    function __construct($connectionname="")
    {
        $this->connection = JFactory::getDBConnection($connectionname);
        $this->_querybuilder = $this->getNewQueryBuilder();
        $this->connection->setQueryBuilder($this->_querybuilder);
        $this->Close();
    }

    /**
     * Description
     *
     * @param
     * @return
     */
    function recordCount()
    {
        $count =  $this->connection->recordCount();
        return $count;
    }



    /**
     * Set the array of SQL queries to be executed later
     *
     * @param array|string Array of SQL queries or just a single query string
     * @return
     */
    function setSQL($sql)
    {
    	$tmp = array();
    	if ( is_string($sql) ) {
    		$tmp = array($sql);
    	}
    	else if ( is_array($sql) ) {
    		$tmp = $sql;
    	}
        $this->_sql = $tmp;
    }


    /**
     * Add SQL query or array of queries to be executed later
     *
     * @param array|string Array of SQL queries or just a single query string
     */
    function addSQL($sql)
    {
    	$tmp = array();
    	if ( is_string($sql) ) {
    		$tmp = array($sql);
    	}
    	else if ( is_array($sql) ) {
    		$tmp = $sql;
    	}
        $this->_sql = array_merge($this->_sql, $tmp);
    }

    /**
     * Return the dataset's SQL queries
     *
     * @return array
     */
    function getSQL()
    {
        return $this->_sql;
    }


    /**
     * Return this object own QueryBuilder
     *
     * @param string Prefix placeholder (#__)
     * @return object JQueryBuilder
     */
    function getQueryBuilder($prefix=Joda::DEFAULT_PREFIX)
    {
        return JFactory::getQueryBuilder($this->connection->getDriverName(), $prefix, $this->connection->getRelationPrefix());
    }


    /**
     * Return this object own QueryBuilder
     *
     * @param string Prefix placeholder (#__)
     * @return object JQueryBuilder
     */
    function getNewQueryBuilder($prefix=Joda::DEFAULT_PREFIX)
    {
        return JFactory::getQueryBuilder($this->connection->getDriverName(), $prefix, $this->connection->getRelationPrefix());
    }


    /**
     * Open dataset
     *
     * Execute dataset's query strings
     *
     * @return boolean Success/Failure
     */
    function open()
    {
    	$result = $this->connection->execQueries($this->_sql);
    	return $result;
    }


    /**
     * Close dataset
     */
    function close()
    {
    }

    /**
     * Get next row data from dataset
     */
    function next()
    {
    	$result = $this->connection->fetchNext($this->_fetchstyle);
    	return $result;
    }


    /**
     * Get all data from dataset using the default fetch style
     */
    function fetchAll()
    {
    	$result = $this->connection->fetchAllData($this->_fetchstyle);
        return $result;
    }


    /**
     * Description
     *
     * @param
     * @return
     */
    function fetchAllAssoc()
    {
    	$result = $this->connection->fetchAllData(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Description
     *
     * @param
     * @return
     */
    function fetchAllObjects()
    {
    	$result = $this->connection->fetchAllData(PDO::FETCH_OBJ);
    	return $result;
    }


    /**
     * Set default fetch style
     *
     * @param integer
     */
    function setFetchStyle($fetchstyle)
    {
    	$this->_fetchstyle = $fetchstyle;
    }





} //JDataset

?>
