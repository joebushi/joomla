<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the JModel class
jimport('joomla.application.component.model');

/**
 * Contacts Component Contacts Model
 *
 * @package		Joomla.Administrator
 * @subpackage	Contacts
 */
class ContactsModelContacts extends JModel
{
	/**
	 * Category data array
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
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get contact item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
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
	 * Method to get the total number of contact items
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
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
	 * Method to get a pagination object for the contact
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	protected function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = ' SELECT DISTINCT c.*, d.data AS email, u.name AS editor, v.name AS user, g.title AS groupname'
			. ' FROM #__contacts_contacts AS c '
			. ' LEFT JOIN #__users AS u ON u.id = c.checked_out '
			. ' LEFT JOIN #__users AS v ON v.id = c.user_id '
			. ' LEFT JOIN #__usergroups AS g ON g.id = c.access '
			. ' LEFT JOIN #__contacts_con_cat_map AS map ON map.contact_id = c.id '
			. ' LEFT JOIN #__categories AS cat ON cat.id = map.category_id '
			. ' LEFT JOIN #__contacts_details AS d ON d.contact_id = c.id '
			. $where
			. $orderby;

		return $query;
	}

	protected function _buildContentOrderBy()
	{
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_order = $mainframe->getUserStateFromRequest($option.'filter_order', 'filter_order', 'map.ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', '', 'word');

		if ($filter_order == 'c.ordering') {
			$orderby = ' ORDER BY map.ordering '.$filter_order_Dir.' , c.name ';
		} else {
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , map.ordering, c.name ';
		}

		return $orderby;
	}

	protected function _buildContentWhere()
	{
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$db = &JFactory::getDBO();
		$filter_state = $mainframe->getUserStateFromRequest($option.'filter_state',	'filter_state', '', 'word');
		$filter_catid = $mainframe->getUserStateFromRequest($option.'filter_catid', 'filter_catid', 0, 'int');			
		$filter_order = $mainframe->getUserStateFromRequest($option.'filter_order',	'filter_order', 'map.ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', '', 'word');
		$search	= $mainframe->getUserStateFromRequest($option.'search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$where = array();

		if ($search) {
			$where[] = 'LOWER(c.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		if ($filter_catid) {
			$where[] = 'map.category_id = '.(int) $filter_catid;
		}
		if ($filter_state) {
			if ( $filter_state == 'P' ) {
				$where[] = 'c.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'c.published = 0';
			}
		}

		$where = (count( $where) ? ' WHERE '. implode(' AND ', $where) : '');
		$where .= ' AND d.field_id = 1';
		return $where;
	}
}
