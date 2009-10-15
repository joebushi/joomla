<?php
/**
 * @version		$Id: login.php 13031 2009-10-02 21:54:22Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_login
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

// Require the base controller
jimport('joomla.application.component.controller');

// Might be cool at some point to cache this input URL and redirect there after successful login.
$task = JRequest::getCmd('task');
if ($task != 'login' && $task != 'logout')
{
	JRequest::setVar('task', '');
	$task = '';
}

$controller	= JController::getInstance('Login');
$controller->execute($task);
$controller->redirect();