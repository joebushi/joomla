<?php
/**
 * @version		$Id: weblinks.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.DS.'controller.php';

$controller	= JController::getInstance('Weblinks');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
