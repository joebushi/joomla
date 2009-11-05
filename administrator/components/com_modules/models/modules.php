<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.database.query');

/**
 * Modules Component Module Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @since 1.5
 */
class ModulesModelModules extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_modules.modules';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState()
	{
		// Initialize variables.
		$app		= &JFactory::getApplication('administrator');
		$params		= JComponentHelper::getParams('com_modules');

		$client = JRequest::getInt('client_id');
		$this->setState('client.id', $client);
		$this->setState('client', JApplicationHelper::getClientInfo($client));

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->_context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $app->getUserStateFromRequest($this->_context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($this->_context.'.published', 'filter_state', '', 'word');
		$this->setState('filter.published', $published);

		$position = $app->getUserStateFromRequest($this->_context.'.position', 'filter_position', '', 'cmd');
		$this->setState('filter.position', $position);

		$type = $app->getUserStateFromRequest($this->_context.'.type', 'filter_type', '', 'cmd');
		$this->setState('filter.type', $type);

		$assigned = $app->getUserStateFromRequest($this->_context.'.assigned', 'filter_assigned', '', 'cmd');
		$this->setState('filter.assigned', $assigned);

		// List state information.
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$this->setState('list.limit', $limit);

		$limitstart = $app->getUserStateFromRequest($this->_context.'.limitstart', 'limitstart', 0, 'int');
		$this->setState('list.limitstart', $limitstart);

		$orderCol	= $app->getUserStateFromRequest($this->_context.'.ordercol', 'filter_order', 'a.position', 'cmd');
		$this->setState('list.ordering', $orderCol);

		$orderDirn	= $app->getUserStateFromRequest($this->_context.'.orderdirn', 'filter_order_Dir', 'asc', 'word');
		$this->setState('list.direction', $orderDirn);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function _getListQuery()
	{
		// Create a new query object.
		$query = new JQuery;

		// Select all fields from the users table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__modules` AS a');
		$query->where('a.client_id = '.(int) $this->getState('client.id'));

		// Join over the pages.
		$query->select('MIN(mm.menuid) AS pages');
		$query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = a.id');

		// Join over the users.
		$query->select('u.name AS editor');
		$query->join('LEFT', '#__users AS u ON u.id = a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by assigned
		if ($assigned = $this->getState('filter.assigned')) {
			$query->where('t.template_style_id = '.$this->_db->Quote($assigned));
			$query->join('LEFT', '#__menu AS t ON t.id = mm.menuid');
		}

		// Filter by position
		if ($position = $this->getState('filter.position')) {
			$query->where('a.position = '.$this->_db->Quote($position));
		}

		// Filter by type
		if ($type = $this->getState('filter.type')) {
			$query->where('a.module = '.$this->_db->Quote($type));
		}

		// Filter on the access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}
		else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%', false);
			$query->where('(a.title LIKE '.$search.')');
		}

		$query->group('a.id');

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering', 'a.ordering')).' '.$this->_db->getEscaped($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function _getStoreId($id = '')
	{
		$id	.= ':'.$this->getState('client.id');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.position');
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.assigned');

		return parent::_getStoreId($id);
	}

	public function setStates($cid, $state = 0)
	{
		$user = &JFactory::getUser();

		// Get a weblinks row instance.
		$table = JTable::getInstance('Module');

		// Update the state for each row
		foreach ($cid as $id) {
			// Load the row.
			$table->load($id);

			// Make sure the weblink isn't checked out by someone else.
			if ($table->checked_out != 0 && $table->checked_out != $user->id) {
				$this->setError(JText::sprintf('MODULES_MODULE_CHECKED_OUT', $id));
				return false;
			}

			// Check the current ordering.
			if ($table->state != $state) {
				// Set the new ordering.
				$table->state = $state;

				// Save the row.
				if (!$table->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}
}