<?php
/**
 * @version     $Id: controller.php 10786 2008-08-24 06:55:34Z plamendp $
 * @package     Joomla
 * @subpackage  Jodademo
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * Dataset Model
 *
 * @package     Joomla
 * @subpackage  Jodademo
 */
class JodademoModelDataset extends JModel
{

    function Test1() { // Just building a simple query
    	$dataset = JFactory::getDBSet();
        $qb = $dataset->getQueryBuilder();
        $qb->select("*")->from("#__content");
        $sql = $qb->getSQL();
    	return $sql;
    }

    function Test2() {  // Getting data from DB
        $dataset = JFactory::getDBSet();
        $qb = $dataset->getQueryBuilder();
        $qb->select("title")->from("#__content");
        $dataset->setSQL($qb->getSQL());
        $dataset->open();
        $data = $dataset->fetchAll();
        return $data;
    }


}