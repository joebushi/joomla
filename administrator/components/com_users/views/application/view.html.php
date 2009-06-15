<?php
/**
 * @version		$Id: view.html.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Users application view.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @version		1.0
 */
class UsersViewApplication extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		// Render the layout.
		parent::display($tpl);
	}
}