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
 * MySQL Connection Class
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 * @author      Plamen Petkov <plamendp@zetcom.bg>
 *
 */
class JConnectionMySQL extends JConnection
{
    var $dbtype                 = "mysql";
    var $port                   = "3306";
    //var $transaction_isolevel   = Joda::REPEATABLE_READ;
    var $transaction_isolevel   = Joda::SERIALIZABLE;
    var $driver_options         = array();

    /**
    * This driver Transaction Isolation Level Names
    *
    * @var array
    */
    var $isolevel_names = array(
        Joda::READ_COMMITED     => "READ COMMITED",
        Joda::REPEATABLE_READ   => "REPEATABLE READ",
        Joda::READ_UNCOMMITTED  => "READ UNCOMMITTED",
        Joda::SERIALIZABLE      => "SERIALIZABLE"
    );

    /**
     * Description
     *
     * @param
     * @return
     */
     function __construct($options)
    {
        $this->host = $options["host"];
        $this->database = $options["db"];
        $this->user = $options["user"];
        $this->password = $options["password"];
        $this->port = $options["port"];
        parent::__construct();
    }


    /**
     * Return SQL query string for setting transaction isolation level
     *
     * @param integer {@link Joda::READ_COMMITED}|{@link Joda::REPEATABLE_READ}|{@link Joda::READ_UNCOMMITTED}|{@link Joda::SERIALIZABLE}
     * @return
     */
     function sqlSetTransactionIsoLevel($level)
    {
        $levelname = $this->isolevel_names[$level];
        $sql = "SET TRANSACTION ISOLATION LEVEL " . $levelname;
        return $sql;
    }



} //JConnection


?>