<?php
/**
 * @version	 $Id$
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT.DS.'controller.php');

// Execute the task.
$controller	= &JController::getInstance('Categories');
$controller->execute(JRequest::getVar('task'));
$controller->redirect();