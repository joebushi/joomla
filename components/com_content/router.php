<?php
/**
* @version		$Id$
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License, see LICENSE.php
*/

function ContentBuildRoute(&$query)
{
	$segments = array();

	// get a menu item based on Itemid or currently active
	$menu = &JSite::getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = &$menu->getActive();
	} else {
		$menuItem = &$menu->getItem($query['Itemid']);
	}
	$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
	$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	if(isset($query['view']))
	{
		$view = $query['view'];
		if(empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}
		unset($query['view']);
	};

	// are we dealing with an article that is attached to a menu item?
	if (($mView == 'article') and (isset($query['id'])) and ($mId == intval($query['id']))) {
		unset($query['view']);
		unset($query['catid']);
		unset($query['id']);
	}

	if (isset($view) and $view == 'category') {
		if ($mId != intval($query['id']) || $mView != $view) {
			$segments[] = $query['path'];
		}
		unset($query['id']);
		unset($query['path']);
	}
	
	if (isset($query['catid'])) {
		// if we are routing an article or category where the category id matches the menu catid, don't include the category segment
		if ((($view == 'article') and ($mView != 'category') and ($mView != 'article') and ($mCatid != intval($query['catid'])))) {
			//$segments[] = $query['catid'];
			
		}
		$segments[] = $query['path'];
		
		unset($query['catid']);
		unset($query['path']);
	};

	if(isset($query['id'])) {
		if (empty($query['Itemid'])) {
			$segments[] = $query['id'];
		} else {
			if (isset($menuItem->query['id'])) {
				if($query['id'] != $mId) {
					$segments[] = $query['id'];
				}
			} else {
				$segments[] = $query['id'];
			}
		}
		unset($query['id']);
	};

	if(isset($query['year'])) {

		if(!empty($query['Itemid'])) {
			$segments[] = $query['year'];
			unset($query['year']);
		}
	};

	if(isset($query['month'])) {

		if(!empty($query['Itemid'])) {
			$segments[] = $query['month'];
			unset($query['month']);
		}
	};

	if(isset($query['layout']))
	{
		if(!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {

				unset($query['layout']);
			}
		} else {
			if($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	};

	return $segments;
}

function ContentParseRoute($segments)
{
	$vars = array();

	//Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);

	//Standard routing for articles
	if(!isset($item))
	{
		$vars['view']  = $segments[0];
		$vars['id']	= $segments[$count - 1];
		return $vars;
	}

	//Handle View and Identifier
	switch($item->query['view'])
	{
		case 'category'   :
		{
			$db = JFactory::getDBO();
			$query = 'SELECT c.*, '.
					'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug '.
					'FROM #__categories AS c,'.
					' (SELECT c.id, MIN(c.lft) as lft, MAX(c.rgt) as rgt'.
					' FROM #__categories AS c, #__categories AS cp'.
					' WHERE cp.id = '.$item->query['id'].' AND c.lft BETWEEN cp.lft '.
					' AND cp.rgt AND c.level > 0 AND c.extension = \'com_content\') AS cp'.
					' WHERE c.lft BETWEEN cp.lft AND cp.rgt AND c.extension = \'com_content\''.
					' AND c.published = 1 AND c.access <= 0 GROUP BY c.id ORDER BY c.lft';
			$db->setQuery($query);
			$categories = $db->loadObjectList();
			foreach($segments as $segment)
			{
				$vars['id'] = '';
				foreach($categories as $category)
				{
					if($category->slug == $segment)
					{
						$vars['id'] = $segment;
						$vars['view'] = 'category';
						$vars['path'][] = $segment;
						continue;
					}
				}
				if($vars['id'] == '')
				{
					$vars['id'] = $segment;
					$vars['view'] = 'article';
				}
			}

		} break;

		case 'frontpage'   :
		{
			$vars['id']   = $segments[$count-1];
			$vars['view'] = 'article';

		} break;

		case 'article' :
		{
			$vars['id']		= $segments[$count-1];
			$vars['view']	= 'article';
		} break;

		case 'archive' :
		{
			if($count != 1)
			{
				$vars['year']	= $count >= 2 ? $segments[$count-2] : null;
				$vars['month']	= $segments[$count-1];
				$vars['view']	= 'archive';
			} else {
				$vars['id']		= $segments[$count-1];
				$vars['view']	= 'article';
			}
		}
	}

	return $vars;
}
