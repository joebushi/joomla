<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML Helper Class for Menus
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 */
class JHtmlMenu
{
	/**
	 * Get a list of the available menu types
	 */
	public function type($name, $selected = null, $attribs = null)
	{
		static $cache;

		if ($cache == null)
		{
			$db = &JFactory::getDbo();
			$db->setQuery(
				'SELECT menutype As value, title As text' .
				' FROM #__menu_types' .
				' ORDER BY title'
			);
			$cache = $db->loadObjectList();
		}

		if ($attribs == null) {
			$attribs = 'class="inputbox"';
		}

		return JHTML::_('select.genericlist', $cache, $name, $attribs, 'value', 'text', $selected);
	}

}
