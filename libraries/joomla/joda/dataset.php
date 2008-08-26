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
     * Description
     *
     * @param
     * @return
     */
    function __construct($connectionname="")
    {
        $this->connection = JFactory::getDBConnection($connectionname);
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
     * @param array Array of SQL queries
     * @return
     */
    function setSQL($sql)
    {
        $this->_sql = $sql;
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
     * Open dataset
     *
     * Execute dataset's query strings
     *
     */
    function open()
    {
    	$this->connection->doQueries($this->_sql);
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
    }


    /**
     * Get next row data from dataset
     */
    function fetchAll()
    {
        $result = $this->connection->fetchAllAsTable();
        return $result;
    }


} //JDataset

?>
