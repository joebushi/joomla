<?php
/**
 * @version		$Id: contact.php 12812 2009-09-22 03:58:25Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.DS.'router.php';

$controller = JController::getInstance('Contact');
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
