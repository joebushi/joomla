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
     * Data, the result of the query
     *
     * @var array
     */
    public $_data = array();

    /**
     * Result set Fields/Columns metadata
     *
     * For more information: {@link http://www.php.net/manual/en/pdostatement.getcolumnmeta.php}
     *
     * @var array
     */
    public $_fields = array();


    /**
     * The nature of the JDataset->{@link $data} property
     *
     * @var integer {@link Joda::DATA_ASTABLE}: as table | {@link Joda::DATA_ASOBJECTS}: as an array of objects
     */
    public $datatype = Joda::DATA_ASTABLE;




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
    function close()
    {
        $this->_fields = array();
        $this->_data = array();
    }


    /**
     * Description
     *
     * @param
     * @return
     */
    function open()
    {
        $this->close();

        /*
        // Replace Prefixes
        $tmp = array();
        foreach ( $this->_sql as $sql ) {
            $tmp[] = $this->_querybuilder->replaceString($sql, $this->_prefix, $this->connection->getRelationPrefix());
        }
        $this->_sql = $tmp;
        */

        switch ($this->datatype) {

            case Joda::DATA_ASTABLE:
                $this->openAsTable();
                break;

            case Joda::DATA_ASOBJECTS:
                $this->openAsObjects();
                break;
        } // switch

    }

    /**
     * Description
     *
     * @param
     * @return
     */
    function openAsTable()
    {
        $this->_data = $this->connection->FetchDataAsTable($this->_sql);
        $this->_fields = $this->connection->getFieldsMeta();
    }


    /**
     * Description
     *
     * @param
     * @return
     */
    function openAsObjects()
    {
        $this->_data = $this->connection->FetchDataAsObjects($this->_sql);
        $this->_fields = $this->connection->getFieldsMeta();
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



} //JDataset

?>
