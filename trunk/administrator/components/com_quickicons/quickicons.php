<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

$user = & JFactory::getUser();
if (!$user->authorize('core.quickicons.manage')) {
//	JFactory::getApplication()->redirect('index.php', JText::_('ALERTNOTAUTH'));
}

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

$controller	= JController::getInstance('QuickIcons');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
