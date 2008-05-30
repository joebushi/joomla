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
 * PgSQL Connection Class
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 * @author      Plamen Petkov <plamendp@zetcom.bg>
 *
 */
class JConnectionPgSQL extends JConnection
{
    protected $_drivername             = "pgsql";
    protected $_port                   = "5432";
    protected $_transaction_isolevel   = Joda::READ_COMMITED;
    protected $_driver_options         = array();

    /**
    * This driver Transaction Isolation Level Names
    *
    * @var array
    */
    protected $_isolevel_names = array(
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
        $this->_host = $options["host"];
        $this->_database = $options["database"];
        $this->_user = $options["user"];
        $this->_password = $options["password"];
        $this->_port = $options["port"];
        parent::__construct();
    }


} //JConnection


?>