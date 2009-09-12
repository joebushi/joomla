<?php
/**
 * @version		$Id: modules.php 12276M 2009-09-12 12:29:41Z (local) $
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$user = & JFactory::getUser();
if (!$user->authorise('core.modules.manage')) {
	JFactory::getApplication()->redirect('index.php', JText::_('ALERTNOTAUTH'));
}

// Helper classes
JHtml::addIncludePath(JPATH_COMPONENT.DS.'classes');

jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Modules');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();