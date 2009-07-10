<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.database.query');

/**
 * About Page Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class ContentModelKeywords extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_content.keywords';
	static $_map_table = '#__content_keyword_article_map';
	static $_authorTag = 'authid::';
	static $_aliasTag = 'alias::';
	static $_categoryTag = 'catid::';
	static $_rowCount = 0;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @since	1.6
	 */
	protected function _populateState()
	{
		$app = &JFactory::getApplication();

		$search = $app->getUserStateFromRequest($this->_context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$tags = $app->getUserStateFromRequest($this->_context.'.tags', 'filter_tags', '');
		$this->setState('filter.tags', $tags);

		$published = $app->getUserStateFromRequest($this->_context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $app->getUserStateFromRequest($this->_context.'.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->getUserStateFromRequest($this->_context.'.limitstart', 'limitstart', 0);
		$this->setState('list.limitstart', $limitstart);

		$orderCol = $app->getUserStateFromRequest($this->_context.'.ordercol', 'filter_order', 'm.keyword');
		$this->setState('list.ordering', $orderCol);

		$orderDirn = $app->getUserStateFromRequest($this->_context.'.orderdirn', 'filter_order_Dir', 'asc');
		$this->setState('list.direction', $orderDirn);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');

		return md5($id);
	}

	/**
	 * @param	boolean	True to join selected foreign information
	 *
	 * @return	string
	 */
	function _getListQuery($resolveFKs = true)
	{
		// Create a new query object.
		$query = new JQuery;

		// Select the required fields from the table.
		$query->select(
		$this->getState(
				'list.select',
				'm.keyword, COUNT(m.article_id) as total_articles,' . 
				'SUM(CASE WHEN a.state = 1 THEN 1 ELSE 0 END) AS published_articles,' . 
				'SUM(CASE WHEN a.state = 0 THEN 1 ELSE 0 END) AS unpublished_articles,' . 
				'SUM(CASE WHEN a.state = -1 THEN 1 ELSE 0 END) AS archived_articles,' .
				'SUM(CASE WHEN a.state = -2 THEN 1 ELSE 0 END) AS trashed_articles')
		);
		$query->from('#__content_keyword_article_map AS m');

		// Join with article table
		$query->join('LEFT', '#__content AS a ON m.article_id = a.id');

		// add grouping
		$query->group('m.keyword');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}
		else if ($published === '') {
			$query->where('(a.state = 0 OR a.state = 1)');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = ' . (int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%');
			$query->where('m.keyword LIKE '.$search);
		}

		// Filter by tags or keywords
		$tags = $this->getState('filter.tags');
		if (!empty($tags)) {
			switch ($tags) {
				case 'keywords':
					$search = $this->_db->Quote(self::$_aliasTag . '%');
					$query->where('m.keyword NOT LIKE ' . $search);
					$search = $this->_db->Quote(self::$_authorTag . '%');
					$query->where('m.keyword NOT LIKE ' . $search);
					$search = $this->_db->Quote(self::$_categoryTag . '%');
					$query->where('m.keyword NOT LIKE ' . $search);
					break;
				case 'tags':
					$search1 = $this->_db->Quote(self::$_aliasTag . '%');
					$search2 = $this->_db->Quote(self::$_authorTag . '%');
					$search3 = $this->_db->Quote(self::$_categoryTag . '%');
					$query->where('(m.keyword LIKE ' . $search1 . 'OR m.keyword LIKE ' . $search2 . 
						' OR m.keyword LIKE ' . $search3 . ')');
					break;
				case 'all':
				default:
					break;
			}
		}

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering', 'm.keyword')).' '.$this->_db->getEscaped($this->getState('list.direction', 'ASC')));

		return $query;
	}
	/**
	 * function rebuild - rebuilds the jos_content_keyword_article_map table
	 * this table is used in the related items module to find articles with matching keywords, author, or category
	 * @return array(): element 0 = true if method successful; element 1 = count of articles; element 2 = count of keywords
	 */
	function rebuild() {
		global $mainframe;
		$result = true; // set return value
		$db	=& JFactory::getDBO();
		// clear the table
		$deleteQuery = 'DELETE FROM ' . self::$_map_table;
		$db->setQuery($deleteQuery);
		if (!$db->query()) {
			$result = false;
		}

		// now insert the rows for each article
		$query = 'SELECT id, metakey, catid, created_by, created_by_alias '.
				' FROM #__content ';
		$db->setQuery($query);
		$articleList = $db->loadObjectList();
		foreach ($articleList as $article)
		{
			if ($article->metakey) // process keywords if present
			{
				$keyArray = explode(',', $article->metakey);
				$keysInserted = array();
				foreach ($keyArray as $thisKey)
				{
					$thisKey = trim($thisKey);
					if (!in_array(strtoupper($thisKey), $keysInserted))
					{
						if (!self::_insertRow($db, $thisKey, $article->id))
						{
							$result = false;
						}
						$keysInserted[] = strtoupper($thisKey);
					}
				}
			}
			// process author, alias, and category
			$authorTag = self::$_authorTag . $article->created_by;
			if (!self::_insertRow($db, $authorTag, $article->id))
			{
				$result = false;
			}
			$categoryTag = self::$_categoryTag . $article->catid;
			if (!self::_insertRow($db, $categoryTag, $article->id))
			{
				$result = false;
			}
			if ($article->created_by_alias)
			{
				$aliasTag = self::$_aliasTag . $article->created_by_alias;
				if (!self::_insertRow($db, $aliasTag, $article->id))
				{
					$result = false;
				}
			}
		}
		$count = count($articleList);
		return array($result, $count, self::$_rowCount);
	}
	
	/**
	 * Function
	 */
	
	/**
	 * Utility method to insert rows into table
	 * @param $db - JDatabase object
	 * @param $keyword - keyword
	 * @param $id - article id
	 * @return - true if successfult
	 */
	protected function _insertRow($db, $keyword, $id) {
		$insertQuery = 'INSERT INTO ' . self::$_map_table .
			' VALUES (' . $db->Quote($keyword).','.$db->Quote($id).')';
		$db->setQuery($insertQuery);
		self::$_rowCount += 1;
		return $db->query();
	}
}