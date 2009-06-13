<?php
/**
 * @version		$Id: admin.config.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Administrator
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Make sure the user is authorized to view this page.
if (!JFactory::getUser()->authorize('core.config.manage')) {
	$mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
}

// Tell the browser not to cache this page.
JResponse::setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

jimport('joomla.application.component.controller');

// Execute the controller.
$controller = JController::getInstance('Config');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();