<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

 /* Weblinks Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since 1.6
 */


class WeblinksRoute
{
	/**
	 * @var	array	A cache of the menu items pertaining to com_weblinks
	 */
	protected static $lookup = null;

	/**
	 * @param	int $id			The id of the weblink.
	 * @param	int	$categoryId	An optional category id.
	 *
	 * @return	string	The routed link.
	 */
	public static function article($id, $categoryId = null)
	{
		$needles = array(
			'weblink'	=> (int) $id,
			'category' => (int) $categoryId
		);

		//Create the link
		$link = 'index.php?option=com_weblinks&view=iteme&id='. $id;

		if ($categoryId) {
			$link .= '&catid='.$categoryId;
		}

		if ($itemId = self::_findItemId($needles)) {
			$link .= '&Itemid='.$itemId;
		};

		return $link;
	}

	/**
	 * @param	int $id			The id of the article.
	 * @param	int	$categoryId	An optional category id.
	 *
	 * @return	string	The routed link.
	 */
	public static function category($catid, $parentId = null)
	{
		$needles = array(
			'category' => (int) $catid
		);

		//Create the link
		$link = 'index.php?option=com_weblinks&view=category&id='.$catid;

		if ($itemId = self::_findItemId($needles)) {
			// TODO: The following should work automatically??
			//if (isset($item->query['layout'])) {
			//	$link .= '&layout='.$item->query['layout'];
			//}
			$link .= '&Itemid='.$itemId;
		};

		return $link;
	}

	protected static function _findItemId($needles)
	{
		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= &JComponentHelper::getComponent('com_weblinks');
			$menus		= &JApplication::getMenu('site', array());
			$items		= $menus->getItems('component_id', $component->id);

			foreach ($items as &$item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		$match = null;

		foreach ($needles as $view => $id)
		{
			if (isset(self::$lookup[$view]))
			{
				if (isset(self::$lookup[$view][$id])) {
					return self::$lookup[$view][$id];
				}
			}
		}

		return null;
	}
}


function WeblinksBuildRoute(&$query){
	static $items;

	$segments	= array();
	// get a menu item based on Itemid or currently active
	$menu = &JSite::getMenu();

	if (empty($query['Itemid'])) {
		$menuItem = &$menu->getActive();
	}
	else {
		$menuItem = &$menu->getItem($query['Itemid']);
	}
	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
	$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	if (isset($query['view']))
	{
		$view = $query['view'];
		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}
		unset($query['view']);
	};


	if (isset($view) and $view == 'category') {
		if ($mId != intval($query['id']) || $mView != $view) {
			$segments[] = $query['id'];
		}
		unset($query['id']);
	}


	if (isset($query['id']))
	{
		if (empty($query['Itemid'])) {
			$segments[] = $query['id'];
		}
		else
		{
			if (isset($menuItem->query['id']))
			{
				if ($query['id'] != $mId) {
					$segments[] = $query['id'];
				}
			}
			else {
				$segments[] = $query['id'];
			}
		}
		unset($query['id']);
	};

	if (isset($query['year']))
	{
		if (!empty($query['Itemid'])) {
			$segments[] = $query['year'];
			unset($query['year']);
		}
	};

	if (isset($query['month']))
	{
		if (!empty($query['Itemid'])) {
			$segments[] = $query['month'];
			unset($query['month']);
		}
	};

	if (isset($query['layout']))
	{
		if (!empty($query['Itemid']) && isset($menuItem->query['layout']))
		{
			if ($query['layout'] == $menuItem->query['layout']) {

				unset($query['layout']);
			}
		}
		else
		{
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	};

	return $segments;
}

function WeblinksParseRoute($segments)
{
	$vars	= array();

	// Get the active menu item.
	$menu	= &JSite::getMenu();
	$item	= &$menu->getActive();

	// Check if we have a valid menu item.
	if (is_object($item))
	{
		if ($item->query['view'] == 'category')
		{
			$categorytree = JCategories::getInstance('com_weblinks');
			$category = $categorytree->get($item->query['id']);
			foreach($segments as $segment)
			{
				$found = 0;
				foreach($category->getChildren() as $child)
				{
					if ($segment == $child->slug)
					{
						$found = 1;
						$category = $child;
						break;
					}
				}
				if ($found == 0)
				{
					$vars['id'] = $segment;
					$vars['catid'] = $category->slug;
					$vars['view'] = 'weblink';
				} else {
					$vars['id'] = $category->slug;
					$vars['view'] = 'category';
				}
			}
		}
	}
	else
	{
		// Count route segments
		$count = count($segments);

		// Check if there are any route segments to handle.
		if ($count)
		{
			if (count($segments[0]) == 2)
			{
				// We are viewing a newsfeed.
				$vars['view']	= 'newsfeed';
				$vars['id']		= $segments[$count-2];
				$vars['catid']	= $segments[$count-1];

			}
			else
			{
				// We are viewing a category.
				$vars['view']	= 'category';
				$vars['catid']	= $segments[$count-1];
			}
		}
	}

	return $vars;
}

