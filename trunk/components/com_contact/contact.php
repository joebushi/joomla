<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new ContactController();

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();