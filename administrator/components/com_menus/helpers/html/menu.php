<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.database.query');

/**
 * HTML Helper Class for Menus
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 */
abstract class JHtmlMenu
{
	/**
	 * Get a list of the available menu types
	 */
	public static function type($name, $selected = null, $attribs = null)
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

	public static function menus($name, $selected = null, $attribs = null, $config = array())
	{
		static $count, $cache;

		$count++;

		if ($cache == null)
		{
			$db = &JFactory::getDbo();
			$db->setQuery(
				'SELECT menutype As value, title As text' .
				' FROM #__menu_types' .
				' ORDER BY title'
			);
			$menus = $db->loadObjectList();

			$query = new JQuery;
			$query->select('a.id AS value, a.title As text, a.level, a.menutype');
			$query->from('#__menu AS a');
			$query->where('a.parent_id > 0');
			$query->where('a.type <> '.$db->quote('url'));

			// Filter on the published state
			if (isset($config['published'])) {
				$query->where('a.published = '.(int) $config['published']);
			}

			$query->order('a.left_id');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			// Collate menu items based on menutype
			$lookup = array();
			foreach ($items as &$item)
			{
				if (!isset($lookup[$item->menutype])) {
					$lookup[$item->menutype] = array();
				}
				$lookup[$item->menutype][] = &$item;

				$item->text = str_repeat('- ',$item->level).$item->text;
			}
			$cache = array();

			foreach ($menus as &$menu)
			{
				$cache[] = JHtml::_('select.optgroup',	$menu->text);
				$cache[] = JHtml::_('select.option', $menu->value.'.0', JText::_('Menus_Add_to_this_menu'));

				if (isset($lookup[$menu->value]))
				{
					foreach ($lookup[$menu->value] as &$item) {
						$cache[] = JHtml::_('select.option', $menu->value.'.'.$item->value, $item->text);
					}
				}
			}
		}

		$options = $cache;
		if (isset($config['title'])) {
			array_unshift($options, JHtml::_('select.option', '', $config['title']));
		}

		return JHtml::_(
			'select.genericlist',
			$options,
			$name,
			array(
				'id' =>				isset($config['id']) ? $config['id'] : 'assetgroups_'.$count,
				'list.attr' =>		(is_null($attribs) ? 'class="inputbox" size="1"' : $attribs),
				'list.select' =>	(int) $selected,
				'list.translate' => false
			)
		);
	}
}
