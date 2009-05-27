<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Installer
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

/*
 * Make sure the user is authorized to view this page
 */
$user = & JFactory::getUser();
if (!$user->authorize('core.installer.manage')) {
	JFactory::getApplication()->redirect('index.php', JText::_('ALERTNOTAUTH'));
}

require_once(JPATH_COMPONENT.DS.'controller.php');
JToolbarHelper::title('Update Manager');
$controller = new UpdateController();
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();