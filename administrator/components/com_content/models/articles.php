<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Content Component Articles Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class ContentModelArticles extends JModel
{
	/**
	 * Category ata array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Filter object
	 *
	 * @var object
	 */
	var $_filter = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
		$app =& JFactory::getApplication();

		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( 'com_content.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$context			= 'com_content.viewcontent';
		$filter = new stdClass();
		$filter->order		= $app->getUserStateFromRequest( $context.'.filter_order',		'filter_order',		'section_name',	'cmd' );
		$filter->order_Dir	= $app->getUserStateFromRequest( $context.'.filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$filter->state		= $app->getUserStateFromRequest( $context.'.filter_state',		'filter_state',		'',				'word' );
		$filter->catid		= $app->getUserStateFromRequest( $context.'.filter_catid',		'filter_catid',		0,				'int' );
		$filter->search		= $app->getUserStateFromRequest( $context.'.search',			'search',			'',				'string' );
		$filter->authorid	= $app->getUserStateFromRequest( $context.'.filter_authorid',	'filter_authorid',	0,	'int' );
		$filter->typeid		= $app->getUserStateFromRequest( $context.'.filter_typeid',		'filter_typeid',	0,	'int' );
		$filter->section 	= JRequest::getCmd( 'section', 'com_content' );
		$filter->sectionid	= $app->getUserStateFromRequest( $context.'.filter_sectionid',	'filter_sectionid',	-1,	'int' );
		$this->_filter = $filter;
	}

	/**
	 * Method to get Content item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to get the total number of section items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the Content
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to get filter object for the articles
	 *
	 * @access public
	 * @return object
	 */
	function getFilter()
	{
		return $this->_filter;
	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere($this->_filter->section);
		$orderby	= $this->_buildContentOrderBy($this->_filter->section);

		$query = 'SELECT c.*, g.name AS groupname, t.name AS typename, cc.title AS name, u.name AS editor, f.content_id AS frontpage, s.title AS section_name, v.name AS author' .
				' FROM #__content AS c' .
				' LEFT JOIN #__categories AS cc ON cc.id = c.catid' .
				' LEFT JOIN #__sections AS s ON s.id = c.sectionid' .
				' LEFT JOIN #__core_acl_axo_groups AS g ON g.value = c.access' .
				' LEFT JOIN #__users AS u ON u.id = c.checked_out' .
				' LEFT JOIN #__users AS v ON v.id = c.created_by' .
				' LEFT JOIN #__content_frontpage AS f ON f.content_id = c.id' .
				' LEFT JOIN #__content_types AS t ON t.id = c.type' .
				$where .
				$orderby;

		return $query;
	}

	function _buildContentOrderBy($section)
	{
		$orderby = ' ORDER BY '. $this->_filter->order .' '. $this->_filter->order_Dir .', section_name, cc.title, c.ordering';

		return $orderby;
	}

	function _buildContentWhere($section)
	{
		$db					=& JFactory::getDBO();
		$search				= JString::strtolower( $this->_filter->search );

		$where[] = 'c.state != -2';

		/*
		 * Add the filter specific information to the where clause
		 */
		// Type filter
		if ($this->_filter->typeid > 0) {
			$where[] = 'c.type = ' . (int) $this->_filter->typeid;
		}
		// Section filter
		if ($this->_filter->sectionid >= 0) {
			$where[] = 'c.sectionid = ' . (int) $this->_filter->sectionid;
		}
		// Category filter
		if ($this->_filter->catid > 0) {
			$where[] = 'c.catid = ' . (int) $this->_filter->catid;
		}
		// Author filter
		if ($this->_filter->authorid > 0) {
			$where[] = 'c.created_by = ' . (int) $this->_filter->authorid;
		}
		// Content state filter
		if ($this->_filter->state) {
			if ($this->_filter->state == 'P') {
				$where[] = 'c.state = 1';
			} else {
				if ($this->_filter->state == 'U') {
					$where[] = 'c.state = 0';
				} else if ($this->_filter->state == 'A') {
					$where[] = 'c.state = -1';
				} else {
					$where[] = 'c.state != -2';
				}
			}
		}
		// Keyword filter
		if ($search) {
			$where[] = '(LOWER( c.title ) LIKE ' . $db->Quote( "%$search%" ) .
				' OR c.id = ' . (int) $search . ')';
		}

		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}
}