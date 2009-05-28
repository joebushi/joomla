<?php
/**
 * @version		$Id: weblinks.php 11845 2009-05-27 23:28:59Z robs $
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

//$user = & JFactory::getUser();
//if (!$user->authorize('com_categories.manage')) {
//	JFactory::getApplication()->redirect('index.php', JText::_('ALERTNOTAUTH'));
//}

jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Category');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();