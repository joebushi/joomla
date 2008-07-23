<?php
/**
* @version		$Id: view.html.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @subpackage	Users
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @static
 * @package		Joomla
 * @subpackage	Users
 * @since 1.0
 */
class UsersViewUsergroups extends JView
{
	function display($tpl = null)
	{
		JSubMenuHelper::addEntry(JText::_( 'Users' ), JRoute::_('index.php?option=com_users&view=users'));
		JSubMenuHelper::addEntry(JText::_( 'Usergroups' ), JRoute::_('index.php?option=com_users&view=usergroups'), true);

		$groups =& JAuthorizationUsergroup::getInstance();
		$groups->load('2');

		$this->assignRef('groups', $groups);

		parent::display($tpl);
	}
}