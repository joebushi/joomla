<?php
/**
 * @version		$Id: content.php 12274 2009-06-22 01:33:26Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.DS.'router.php';

$controller = JController::getInstance('Content');
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
