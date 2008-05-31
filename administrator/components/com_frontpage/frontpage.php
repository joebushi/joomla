<?php
/**
* @version		$Id: admin.frontpage.php 10094 2008-03-02 04:35:10Z instance $
* @package		Joomla
* @subpackage	Content
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure the user is authorized to view this page
$user = & JFactory::getUser();
if (!$user->authorize( 'com_frontpage', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT.DS.DS.'tables');

// Allow us to use content.legend
JHTML::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'classes' );

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Set the helper directory
JHTML::addIncludePath( JPATH_COMPONENT.DS.'classes' );

$controller	= new FrontpageController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();