<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSearchContent extends JPlugin
{
	function onAfterSearch(&$results)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$ids = array();
		foreach($results as $result)
		{
			if($result->extension = 'com_content')
			{
				$ids[] = substr($result->content_id, 7);
			}
		}
		if(!count($ids))
		{
			return $results;
		}
		$query = 'SELECT * FROM #__content WHERE id IN ('.implode(',', $ids).') AND access IN ('.implode(',', $user->authorisedLevels()).')';
		$db->setQuery($query);
		$content = $db->loadObjectList('id');
		foreach($results as &$result)
		{
			if($result->extension = 'com_content')
			{
				$searchresult = new stdClass();
				$searchresult->title = $content[substr($result->content_id,7)]->title;
				$searchresult->body = $content[substr($result->content_id,7)]->introtext;
				$searchresult->subtitle = $content[substr($result->content_id,7)]->created_by;
				$searchresult->date = $content[substr($result->content_id,7)]->created;
				$searchresult->class_suffix = '';
				$searchresult->link = ContentHelperRoute::getArticleRoute($content[substr($result->content_id,7)]->id, $content[substr($result->content_id,7)]->catid);
				$result = $searchresult;
			}
		}
		return $results;	
	}
}