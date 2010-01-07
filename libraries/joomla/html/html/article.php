<?php
/**
 * @version		$Id: $
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Utility class working with article select lists
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class JHtmlArticle
{
	/**
	 * @var	array	Cached array of the articles.
	 */
	protected static $article = null;

	/**
	 * @var	array	Cached array of the article items.
	 */
	protected static $items = null;

	/**
	 * Get a list of the available articles.
	 *
	 * @return	string
	 * @since	1.6
	 */
	public static function article()
	{
		if (empty(self::$article))
		{
			$db = &JFactory::getDbo();
			$db->setQuery(
				'SELECT id AS value, title AS text' .
				' FROM #__content' .
				' ORDER BY title'
			);
			self::$article = $db->loadObjectList();
		}

		return self::$article;
	}

	/**
	 * Returns an array of articles items grouped by categories.
	 *
	 * @param	array	An array of configuration options.
	 *
	 * @return	array
	 */
	public static function articleitems($config = array())
	{
		if (empty(self::$items))
		{
			$db = &JFactory::getDbo();
			$db->setQuery(
				'SELECT id AS value, title AS text' .
				' FROM #__content' .
				' ORDER BY title'
			);
			$articles = $db->loadObjectList();

			print_r($articles);

			$query = new JQuery;
			$query->select('a.id AS value, a.title As text, a.catid');
			$query->from('#__content AS a');
			$query->where('a.state = 1 ');
			$query->order('a.catid');
			$query->order('a.ordering');

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
			self::$items = array();

			foreach ($menus as &$menu)
			{
				self::$items[] = JHtml::_('select.optgroup',	$menu->text);
				self::$items[] = JHtml::_('select.option', $menu->value.'.0', JText::_('Menus_Add_to_this_menu'));

				if (isset($lookup[$menu->value]))
				{
					foreach ($lookup[$menu->value] as &$item) {
						self::$items[] = JHtml::_('select.option', $menu->value.'.'.$item->value, $item->text);
					}
				}
			}
		}

		return self::$items;
	}

	/**
	 * Displays an HTML select list of menu items.
	 *
	 * @param	string	The name of the control.
	 * @param	string	The value of the selected option.
	 * @param	string	Attributes for the control.
	 * @param	array	An array of options for the control.
	 *
	 * @return	string
	 */
	public static function menuitemlist($name, $selected = null, $attribs = null, $config = array())
	{
		static $count;

		$options = self::menuitems($config);

		return JHtml::_(
			'select.genericlist',
			$options,
			$name,
			array(
				'id' =>				isset($config['id']) ? $config['id'] : 'assetgroups_'.++$count,
				'list.attr' =>		(is_null($attribs) ? 'class="inputbox" size="1"' : $attribs),
				'list.select' =>	(int) $selected,
				'list.translate' => false
			)
		);
	}


	/**
	 * Build the select list for Menu Ordering
	 */
	public static function ordering(&$row, $id)
	{
		$db = &JFactory::getDbo();

		if ($id)
		{
			$query = 'SELECT ordering AS value, title AS text'
			. ' FROM #__content'
			. ' WHERE catid = '.$db->Quote($row->catid)
			. ' AND state != -2'
			. ' ORDER BY ordering';
			$order = JHtml::_('list.genericordering',  $query);
			$ordering = JHtml::_(
				'select.genericlist',
				$order,
				'ordering',
				array('list.attr' => 'class="inputbox" size="1"', 'list.select' => intval($row->ordering))
			);
		}
		else
		{
			$ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. JText::_('DESCNEWITEMSLAST');
		}
		return $ordering;
	}

	/**
	 * Build the multiple select list for Menu Links/Pages
	 */
	public static function linkoptions($all=false, $unassigned=false)
	{
		$db = &JFactory::getDbo();

		// get a list of the menu items
		$query = 'SELECT a.id, a.title, c.id AS catid, c.title AS cattitle, c.parent_id'
		. ' FROM #__content AS a'
		. ' LEFT JOIN #__categories AS c ON c.id = a.catid'
		. ' WHERE a.state = 1'
		. ' ORDER BY catid, a.ordering'
		;
		$db->setQuery($query);

		$aitems = $db->loadObjectList();

		echo '<pre>';
		print_r($aitems);
		echo '</pre>';
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
		}

		if (!$aitems) {
			$aitems = array();
		}

		$aitems_temp = $aitems;

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($aitems as $v)
		{
			$id = $v->id;
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}

		echo '<pre>';
		print_r($children);
		echo '</pre>';
		// second pass - get an indent list of the items
		$list = JHtmlArticle::TreeRecurse(intval($aitems[0]->catid), '', array(), $children, 9999, 0, 0);

		$aitems = array();
		if ($all | $unassigned) {
			$aitems[] = JHtml::_('select.option',  '<OPTGROUP>', JText::_('Menus'));

			if ($all) {
				$aitems[] = JHtml::_('select.option',  0, JText::_('All'));
			}
			if ($unassigned) {
				$aitems[] = JHtml::_('select.option',  -1, JText::_('Unassigned'));
			}

			$aitems[] = JHtml::_('select.option',  '</OPTGROUP>');
		}

		$lastMenuType	= null;
		$tmpMenuType	= null;
		foreach ($list as $list_a)
		{
			if ($list_a->menutype != $lastMenuType)
			{
				if ($tmpMenuType) {
					$aitems[] = JHtml::_('select.option',  '</OPTGROUP>');
				}
				$aitems[] = JHtml::_('select.option',  '<OPTGROUP>', $list_a->menutype);
				$lastMenuType = $list_a->menutype;
				$tmpMenuType  = $list_a->menutype;
			}

			$aitems[] = JHtml::_('select.option',  $list_a->id, $list_a->treename);
		}
		if ($lastMenuType !== null) {
			$aitems[] = JHtml::_('select.option',  '</OPTGROUP>');
		}

		return $aitems;
	}

	public static function treerecurse($id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1)
	{
		echo '<pre>';
		print_r($id);
		echo '</pre>';
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;

				if ($type) {
					$pre 	= '<sup>|_</sup>&nbsp;';
					$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} else {
					$pre 	= '- ';
					$spacer = '&nbsp;&nbsp;';
				}

				if ($v->parent_id == 0) {
					$txt 	= $v->cattitle;
				} else {
					$txt 	= $pre . $v->cattitle;
				}
				$pt = $v->parent_id;
				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = JHtmlArticle::TreeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}
}