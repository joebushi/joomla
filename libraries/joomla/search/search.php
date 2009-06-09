<?php
/**
 * @version		$Id: search.php $
 * @package		Joomla.Framework
 * @subpackage	Search
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access
defined('JPATH_BASE') or die();

/**
 * Utility class for Joomla! Search
 * 
 * @package Joomla.Framework
 * @subpackage Search
 * @since 1.6
 */
class JSearch
{	
	public static function &getInstance($options = array())
	{

	}
	
	public function index($extension, $content_id, $language, $title = null, $body = null, $tags = null, $params = null, $modifier = null)
	{
		$db =& JFactory::getDBO();
		JPluginHelper::importPlugin('search');
		
		$lang = strtoupper(str_replace('-','_',$language));
		if(function_exists('JSearchSplitWords'.$lang))
		{
			$splitter = 'JSearchSplitWords'.$lang;
		} else {
			$splitter = 'JSearchSplitWords';
		}
		
		if(function_exists('JSearchStemmWords'.$lang))
		{
			$stemmer = 'JSearchStemmWords'.$lang;
		} else {
			$stemmer = 'JSearchStemmWords';
		}
		
		if(function_exists('JSearchStopWords'.$lang))
		{
			$stopper = 'JSearchStopWords'.$lang;
			$stopwords = $stopper();
		} else {
			$stopwords = array();
		}
		
		if($modifier == null)
		{
			$modifier = 1;
		}

		$newindex = array();
		for($i=0; $i<3; $i++)
		{
			switch($i) {
				case 0:
					$string = $title;
					$mod = $modifier*2;
					break;
				case 1:
					$string = $body;
					$mod = $modifier;
					break;
				case 2:
					$string = $tags;
					$mod = $modifier*3;
					break; 
			}
			if($string != null)
			{
				$words = $splitter($string);
				foreach($words as $word)
				{
					if(in_array($word, $stopwords))
					{
						continue;
					}
					$word = $stemmer($word);
					if(in_array($word, $stopwords))
					{
						continue;
					}
					if(isset($newindex[$word]))
					{
						$newindex[$word] += $mod;
					} else {
						$newindex[$word] = $mod;
					}
				}
			}
		}
		$query = 'SELECT id FROM #__search WHERE content_id = '.$db->Quote($content_id).' AND extension = '.$db->Quote($extension).' AND lang = '.$db->Quote($lang);
		$db->setQuery($query);
		$id = $db->loadResult();
		if($id > 0)
		{
			$query = 'DELETE FROM #__search_word WHERE content_id = '.$id;
			$db->setQuery($query);
			$db->Query();
			
			$query = 'UPDATE #__search SET params = '.$db->Quote($params).' WHERE content_id = '.$db->Quote($content_id).' AND extension = '.$db->Quote($extension).' AND lang = '.$db->Quote($lang);
			$db->setQuery($query);
			$db->Query();			
		} else {
			$query = 'INSERT INTO #__search (extension, content_id, lang, params) VALUES ('.
				$db->Quote($extension).', '
				.$db->Quote($content_id).', '
				.$db->Quote($lang).', '
				.$db->Quote(serialize($params)).');';
			$db->setQuery($query);
			$db->Query();
			$id = $db->insertid();
		}
		
		$query = 'INSERT INTO #__search_word (content_id, word, score) VALUES ';
		$queryarray = array();
		foreach($newindex as $word => $score)
		{
			$queryarray[] = '('.$id.', '.$db->Quote($word).', '.$score.')';
		}
		$query .= implode(',', $queryarray);
		$db->setQuery($query);
		$db->Query();	
		return true;
	}
	
	public function delete($extension, $contentID = null)
	{
		
	}
	
	public function find($search, $extension = null, $language = 'en-GB', $limit = 20, $limitstart = 0)
	{
		$db =& JFactory::getDBO();
		JPluginHelper::importPlugin('search');
		$searchresults = array();
		$lang = strtoupper(str_replace('-','_',$language));
		if(function_exists('JSearchSplitWords'.$lang))
		{
			$splitter = 'JSearchSplitWords'.$lang;
		} else {
			$splitter = 'JSearchSplitWords';
		}
		
		if(function_exists('JSearchStemmWords'.$lang))
		{
			$stemmer = 'JSearchStemmWords'.$lang;
		} else {
			$stemmer = 'JSearchStemmWords';
		}
		
		if(function_exists('JSearchStopWords'.$lang))
		{
			$stopper = 'JSearchStopWords'.$lang;
			$stopwords = $stopper();
		} else {
			$stopwords = array();
		}
		
		if($search != null)
		{
			$words = $splitter($search);
			foreach($words as $word)
			{
				if(in_array($word, $stopwords))
				{
					continue;
				} else {
					$word = $stemmer($word);
					if(in_array($word, $stopwords))
					{
						continue;
					}
					$result[] = $word;
				}
			}
		}
		
		if(count($result) > 0)
		{
			$query = 'SELECT DISTINCT content_id, COUNT(*) AS nb, SUM(score) AS total_score'
				.' FROM #__search_word'
				.' WHERE ';
			foreach($result as &$word)
			{
				$word = 'word = '.$db->Quote($word);
			}
			$query .= '('.implode(' OR ', $result).')';
			$query .= ' GROUP BY content_id'
				.' HAVING nb = '.count($result)
				.' ORDER BY nb DESC, total_score DESC';
			$db->setQuery($query);
			$results = $db->loadObjectList();
			if($results)
			{
				foreach($results as $content_id)
				{
					$content_ids[] = $content_id->content_id;
				}
				$query = 'SELECT content_id, extension, lang, params'
					.' FROM #__search'
					.' WHERE id IN ('.implode(',', $content_ids).')';
				$db->setQuery($query);
				$searchresults = $db->loadObjectList();
			}
		}
		$dispatcher = &JDispatcher::getInstance();
		$results = $dispatcher->trigger(
			'onAfterSearch', array (&$searchresults)
		);
		
		return $searchresults;
	}
	
	public function reindex()
	{
		
	}
}

function JSearchStemmWords($word)
{
	return $word;
}
	
function JSearchSplitWords($string)
{
	$string = strtolower(html_entity_decode($string));
	$string = preg_replace('/&#?\w+;/', ' ', $string);
	$string = preg_replace('/\s+/', ' ', $string);
	
	$string = strip_tags($string);
	$string = preg_replace('/\W+/', ' ', $string);
	$words = preg_split('/\s+/', trim($string));
	foreach($words as &$word)
	{
		if(strlen($word) < 2 | is_numeric($word))
		{
			continue;
		}
		$results[] = $word;
	}
	$words = $results;
	return $words;
}

