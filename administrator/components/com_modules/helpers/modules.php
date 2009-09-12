<?php
/**
 * @version		$Id
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Modules helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @since		1.6
 */
class ModulesHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($client)
	{
		JSubMenuHelper::addEntry(
			JText::_('Modules_Submenu_Site'),
			'index.php?option=com_modules&client=0',
			$client == 0
		);
		JSubMenuHelper::addEntry(
			JText::_('Modules_Submenu_Administrator'),
			'index.php?option=com_modules&client=1',
			$client == 1
		);
	}
}