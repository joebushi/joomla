<?php
/**
 * @version		$Id: admin.media.php 8031 2007-07-17 23:14:23Z jinx $
 * @package		Joomla
 * @subpackage	Media
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT.DS.'controller.php');
$folder			= JRequest::getVar('folder', '', '', 'path');
$folderCheck	= JRequest::getVar('folder', null, '', 'string', JREQUEST_ALLOWRAW);
if (($folderCheck !== null) && ($folder !== $folderCheck)) {
	JError::raiseWarning(403, JText::_('WARNDIRNAME'));
}


switch (JRequest::getCmd('task')) {

	case 'setftp' :
		MediaController::setftp();
		break;

	// popup directory creation interface for use by components
	case 'popupDirectory' :
		MediaController::showFolder();
		break;

	// popup upload interface for use by components
	case 'popupUpload' :
		MediaController::showUpload();
		break;

	// popup upload interface for use by components
	case 'imgManager' :
		MediaController::imgManager(COM_MEDIA_BASE);
		break;

	case 'imgManagerList' :
		MediaController::imgManagerList($folder);
		break;
}
