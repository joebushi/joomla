<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Content Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
abstract class ContentHelperRoute
{
	/**
	 * @param	int	The route of the content item
	 */
	public static function getArticleRoute($id, $catid = 0, $sectionid = 0)
	{
		$needles = array(
			'article'  => (int) $id,
			'category' => (int) $catid,
			'section'  => (int) $sectionid,
		);

		//Create the link
		$link = 'index.php?option=com_content&view=article&id='. $id;

		if($catid) {
			$link .= '&catid='.$catid;
		}

		if($item = ContentHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	public static function getSectionRoute($sectionid)
	{
		$needles = array(
			'section' => (int) $sectionid
		);

		//Create the link
		$link = 'index.php?option=com_content&view=section&id='.$sectionid;

		if($item = ContentHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	public static function getCategoryRoute($catid, $sectionid)
	{
		$needles = array(
			'category' => (int) $catid,
			'section'  => (int) $sectionid
		);

		//Create the link
		$link = 'index.php?option=com_content&view=category&id='.$catid;

		if($item = ContentHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	protected static function _findItem($needles)
	{
		$component =& JComponentHelper::getComponent('com_content');
		$app = JFactory::getApplication();
		$menus	= & $app->getMenu();
		$items	= $menus->getItems('componentid', $component->id);

		$match = null;

		foreach($needles as $needle => $id)
		{
			foreach($items as $item)
			{
				if ((@$item->query['view'] == $needle) && (@$item->query['id'] == $id)) {
					$match = $item;
					break;
				}
			}

			if(isset($match)) {
				break;
			}
		}

		return $match;
	}
}
?>
