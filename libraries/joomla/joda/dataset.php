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

jimport("joomla.joda.querybuilder");

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
    public $sql = array();


    /**
     * Data, the result of the query
     *
     * @var array
     */
    public $data = array();

    /**
     * Result set Fields/Columns metadata
     *
     * For more information: {@link http://www.php.net/manual/en/pdostatement.getcolumnmeta.php}
     *
     * @var array
     */
    public $fields = array();


    /**
     * The nature of the JDataset->{@link $data} property
     *
     * @var integer {@link Joda::DATA_ASTABLE}: as table | {@link Joda::DATA_ASOBJECTS}: as an array of objects
     */
    public $datatype = Joda::DATA_ASTABLE;


    /**
     * Query Builder
     *
     * @var JQueryBuilder
     */
    public $querybuilder = null;


    /**
     * Description
     *
     * @param
     * @return
     */
    function __construct($connectionname="")
    {
        $this->connection = JFactory::getConnection($connectionname);
        $this->querybuilder = JQueryBuilder::getInstance($this->connection->getDriverName());
        $this->querybuilder->resetQuery();
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
        $this->fields = array();
        $this->data = array();
    }


    /**
     * Description
     *
     * @param
     * @return
     */
    function open()
    {
        $this->Close();
        switch ($this->datatype) {

            case Joda::DATA_ASTABLE:
                $this->OpenAsTable();
                break;

            case Joda::DATA_ASOBJECTS:
                $this->OpenAsObjects();
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
        $this->data = $this->connection->FetchDataAsTable($this->sql);
        $this->fields = $this->connection->getFieldsMeta();
    }


    /**
     * Description
     *
     * @param
     * @return
     */
    function openAsObjects()
    {
        $this->data = $this->connection->FetchDataAsObjects($this->sql);
        $this->fields = $this->connection->getFieldsMeta();
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




} //JDataset

?>
