<?php
/**
 * @version		$Id: content.php 12175 2009-06-19 23:52:21Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 */
abstract class QuickIconsHelper
{
	/**
	 * @return	array array of quickicons section
	 */
	function &getPublishedSections()
	{
		$query = new JQuery();
		$query->select('*');
		$query->from('`#__quickicons_sections`');
		$query->where('`published` = 1');
		$query->order('`ordering` ASC');
		$db = &JFactory::getDBO();
		$db->setQuery($query->toString());
		$sections = $db->loadObjectList();
		return $sections;
	}
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('QuickIcons_Icons'),
			'index.php?option=com_quickicons&view=quickicons',
			$vName == 'quickicons');
		JSubMenuHelper::addEntry(
			JText::_('QuickIcons_Sections'),
			'index.php?option=com_quickicons&view=sections',
			$vName == 'sections'
		);
	}
}
