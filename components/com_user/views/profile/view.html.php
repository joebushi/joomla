<?php
/**
* @version		$Id: view.html.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @subpackage	Weblinks
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @static
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.0
 */
class UserViewProfile extends JView
{
	function display( $tpl = null)
	{
		global $mainframe;

		$layout	= $this->getLayout();
		if( $layout == 'edit') {
			$this->_displayForm($tpl);
			return;
		}

		$user =& JFactory::getUser(JRequest::getInt('id', ''));
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();

		$infos = $user->getInfos(true);

		// Set pathway information
		$this->assignRef('user'   , $user);
		$this->assignRef('menu', $menu);
		$this->assignRef('userinfos', $infos);

		parent::display($tpl);
	}

	function _displayForm($tpl = null)
	{
		global $mainframe;

		$user     =& JFactory::getUser();

		// check to see if Frontend User Params have been enabled
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$check = $usersConfig->get('frontend_userparams');

		if ($check == '1' || $check == 1 || $check == NULL)
		{
			if($user->authorize( 'com_user', 'edit' )) {
				$params		= $user->getParameters(true);
			}
		}

		$this->assignRef('user'  , $user);
		$this->assignRef('params', $params);

		parent::display($tpl);
	}
}
