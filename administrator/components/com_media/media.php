<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Media
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_media')) {
	return JError::raiseWarning(404, JText::_('ALERTNOTAUTH'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$params = JComponentHelper::getParams('com_media');

// Set the path definitions
$view = JRequest::getCmd('view',null);
$popup_upload = JRequest::getCmd('pop_up',null);
$path = 'file_path';
if (substr(strtolower($view),0,6) == 'images' || $popup_upload == 1) {
	$path = 'image_path';
}
define('COM_MEDIA_BASE',    rtrim(JPath::clean(JPATH_ROOT.DS.$params->get($path, 'images'), '/'), '/'));
define('COM_MEDIA_BASEURL', JPath::clean(JURI::root().$params->get($path, 'images'), '/'));

// Execute the task.
$controller	= JController::getInstance('Media');
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
