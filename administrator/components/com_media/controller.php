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

	case 'upload' :
		MediaController::upload();
		break;

	case 'setftp' :
		MediaController::setftp();
		break;

	case 'uploadbatch' :
		MediaController::batchUpload();
		MediaController::showMedia();
		break;

	case 'createfolder' :
		MediaController::createFolder();
		$mainframe->redirect('index.php?option=com_media&task=list&tmpl=component&folder='.$folder);
		MediaController::showMedia();
		break;

	case 'delete' :
		MediaController::delete($folder);
		$mainframe->redirect('index.php?option=com_media&task=list&tmpl=component&folder='.$folder);
		//MediaController::listMedia();
		break;

	case 'list' :
		MediaController::listMedia();
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

	default :
		MediaController::showMedia();
		break;
}

/**
 * Media Manager Controller
 *
 * @static
 * @package		Joomla
 * @subpackage	Media
 * @since 1.5
 */
class MediaController
{

	function _buildFolderTree($list)
	{
		// id, parent, name, url, title, target
		$nodes = array();
		$i = 1;
		$nodes[''] = array ('id' => "0", 'pid' => -1, 'name' => basename(COM_MEDIA_BASE), 'url' => 'index.php?option=com_media&task=list&tmpl=component&folder=', 'title' => '/', 'target' => 'folderframe');

		if (is_array($list) && count($list))
		{
			foreach ($list as $item)
			{
				// Try to find parent
				$pivot = strrpos($item, '/');
				$parent = substr($item, 0, $pivot);
				if (isset($nodes[$parent])) {
					$pid = $nodes[$parent]['id'];
				} else {
					$pid = -1;
				}
				$nodes[$item] = array ('id' => $i, 'pid' => $pid, 'name' => basename($item), 'url' => 'index.php?option=com_media&task=list&tmpl=component&folder='.$item, 'title' => $item, 'target' => 'folderframe');
				$i++;
			}
		}
		return $nodes;
	}
}
?>