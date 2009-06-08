<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.database.query');

/**
 * Menu Item List Model for Menus.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class MenusModelItems extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_menus.items';

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function _getListQuery()
	{
		// Create a new query object.
		$query = new JQuery;

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__menu` AS a');

		// Join over the users.
		$query->select('co.name AS editor');
		$query->join('LEFT', '`#__users` AS co ON co.id = a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__access_assetgroups AS ag ON ag.id = a.access');

		// Self join to find the level in the tree.
		$query->select('COUNT(DISTINCT p.id) AS level');
		$query->join('LEFT OUTER', '`#__menu` AS p ON a.left_id > p.left_id AND a.right_id < p.right_id');
		$query->group('a.id');

		// Exclude the root category.
		$query->where('a.id > 1');

		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}

		// Filter by search in title, alias or id
		if ($search = trim($this->getState('filter.search')))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%');
				$query->where('a.title LIKE '.$search.' OR a.alias LIKE '.$search);
			}
		}

		// Filter the items over the parent id if set.
		$parentId = $this->getState('filter.parent_id');
		if (!empty($parentId)) {
			$query->where('p.id = '.(int)$parentId);
		}

		// Filter the items over the menu id if set.
		$menuId = $this->getState('filter.menu_id');
		if (!empty($menuId)) {
			$query->where('a.menu_id = '.(int) $menuId);
		}

		// Filter the items over the menu id if set.
		$menuType = $this->getState('filter.menutype');
		if (!empty($menuType)) {
			$query->where('a.menutype = '.$this->_db->quote($menuType));
		}

		// Filter on the access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('a.level <= '.(int) $level);
		}

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering', 'a.left_id')).' '.$this->_db->getEscaped($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('check.state');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.parent_id');
		$id	.= ':'.$this->getState('filter.menu_id');

		return md5($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication('administrator');
		$params	= &JComponentHelper::getParams('com_menus');

		// Load the filter state.
		$this->setState('filter.search',	$app->getUserStateFromRequest($this->_context.'.filter.search', 'filter_search', ''));
		$this->setState('filter.published',	$app->getUserStateFromRequest($this->_context.'.filter.published', 'filter_published', 1, 'string'));
		$this->setState('filter.parent_id',	$app->getUserStateFromRequest($this->_context.'.filter.parent_id', 'filter_parent_id', 0, 'int'));
		$this->setState('filter.access',	$app->getUserStateFromRequest($this->_context.'.filter.access', 'filter_access', 0, 'int'));
		$this->setState('filter.level',		$app->getUserStateFromRequest($this->_context.'.filter.level', 'filter_level', 0, 'int'));
		$this->setState('filter.menutype',	$app->getUserStateFromRequest($this->_context.'.filter.menutype', 'menutype', 'mainmenu'));

		// Load the list state.
		$this->setState('list.start',		$app->getUserStateFromRequest($this->_context.'.list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit',		$app->getUserStateFromRequest($this->_context.'.list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering',	$app->getUserStateFromRequest($this->_context.'.list.ordering', 'filter_order', 'a.left_id', 'cmd'));
		$this->setState('list.direction',	$app->getUserStateFromRequest($this->_context.'.list.direction', 'filter_order_Dir', 'ASC', 'word'));

		$this->setState('params', $params);

		// Load the user parameters.
		$user = & JFactory::getUser();
		$this->setState('user',	$user);
		$this->setState('user.id', (int) $user->id);
	}
}