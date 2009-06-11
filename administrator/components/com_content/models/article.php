<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.database.query');

/**
 * Menu Item Model for Menus.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @version		1.6
 */
class ContentModelArticle extends JModelForm
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	 protected $_context		= 'com_content.item';

	/**
	 * Returns a reference to the a Table object, always creating it
	 *
	 * @param	type 	$type 	 The table type to instantiate
	 * @param	string 	$prefix	 A prefix for the table class name. Optional.
	 * @param	array	$options Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function &getTable($type = 'Content', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
		$app	= &JFactory::getApplication('administrator');

		// Load the User state.
		if (!($id = (int) $app->getUserState('com_content.edit.article.id'))) {
			$id = (int) JRequest::getInt('item_id');
		}
		$this->setState('article.id',			$id);

		// Load the parameters.
		$params	= &JComponentHelper::getParams('com_content');
		$this->setState('params', $params);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param	integer	The id of the menu item to get.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null)
	{
		// Initialize variables.
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('article.id');
		$false	= false;

		// Get a row instance.
		$table = &$this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		// Prime required properties.
		if (empty($table->id))
		{
			//$table->parent_id	= $this->getState('item.parent_id');
			//$table->menutype	= $this->getState('item.menutype');
			//$table->type		= $this->getState('item.type');
		}

		// Convert the params field to an array.
		$registry = new JRegistry();
		$registry->loadJSON($table->params);
		$table->params = $registry->toArray();

		$value = JArrayHelper::toObject($table->getProperties(1), 'JObject');

		return $value;
	}

	/**
	 * Method to get the row form.
	 *
	 * @return	mixed	JForm object on success, false on failure.
	 * @since	1.6
	 */
	public function &getForm()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$false	= false;

		// Get the form.
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		$form = &JForm::getInstance('jform', 'article', true, array('array' => true));

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_content.edit.item.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($data)
	{
		$id	= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('article.id');
		$isNew	= true;

		// Get a row instance.
		$table = &$this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('JTable_Error_Bind_failed', $table->getError()));
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the hierarchy.
		if (!$table->rebuildTree()) {
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the tree path.
		if (!$table->rebuildPath($table->id)) {
			$this->setError($table->getError());
			return false;
		}

		$this->setState('article.id', $table->id);

		return true;
	}

	/**
	 * Method to delete rows.
	 *
	 * @param	array	An array of item ids.
	 *
	 * @return	boolean	Returns true on success, false on failure.
	 */
	public function delete($itemIds)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);

		// Get a row instance.
		$table = &$this->getTable();

		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId)
		{
			if (!$table->delete($itemId))
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to publish categories.
	 *
	 * @param	array	The ids of the items to publish.
	 * @param	int		The value of the published state
	 *
	 * @return	boolean	True on success.
	 */
	function publish($itemIds, $value = 1)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable();

		// Attempt to publish the items.
		if (!$table->publish($itemIds, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to toggle the frontpage setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 *
	 * @return	boolean	True on success.
	 */
	function frontpage($itemIds)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;

		$table = $this->getTable('Frontpage', 'ContentTable');

		foreach ($itemIds as $id)
		{
			// Toggles go to first place.
			if ($table->load((int) $id))
			{
				if (!$table->delete($id)) {
					$this->setError($table->getError());
					return false;
				}
				$table->ordering = 0;
			}
			else
			{
				// Because this is a mapping table, we cannot use JTable::store
				$this->_db->setQuery(
					'INSERT INTO #__content_frontpage' .
					' SET content_id = '.(int) $id.
					', ordering = 0'
				);
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			$table->reorder();
		}

		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		return true;
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param	int		The numeric id of the row to move.
	 * @param	integer	Increment, usually +1 or -1
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function ordering($id, $direction = 0)
	{
		// Sanitize the id and adjustment.
		$id	= (!empty($id)) ? $id : (int) $this->getState('article.id');

		// Get a row instance.
		$table = &$this->getTable();

		// Attempt to adjust the row ordering.
		if (!$table->ordering((int) $direction, $id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param	integer	$id The numeric id of a row
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function checkin($id = null)
	{
		// Initialize variables.
		$id	= (!empty($id)) ? $id : (int) $this->getState('article.id');

		// Only attempt to check the row in if it exists.
		if ($id)
		{
			$user	= &JFactory::getUser();

			// Get an instance of the row to checkin.
			$table = &$this->getTable();
			if (!$table->load($id)) {
				$this->setError($table->getError());
				return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id')) {
				$this->setError(JText::_('JError_Checkin_user_mismatch'));
				return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($id)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check-out a row for editing.
	 *
	 * @param	int		$id	The numeric id of the row to check-out.
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function checkout($id = null)
	{
		// Initialize variables.
		$id		= (!empty($id)) ? $id : (int) $this->getState('article.id');

		// Only attempt to check the row in if it exists.
		if ($id)
		{
			// Get a row instance.
			$table = &$this->getTable();

			// Get the current user object.
			$user = &JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $id)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to perform batch operations on a category or a set of categories.
	 *
	 * @param	array	An array of commands to perform.
	 * @param	array	An array of category ids.
	 *
	 * @return	boolean	Returns true on success, false on failure.
	 */
	function batch($commands, $itemIds)
	{
		// Sanitize user ids.
		$itemIds = array_unique($itemIds);
		JArrayHelper::toInteger($itemIds);

		// Remove any values of zero.
		if (array_search(0, $itemIds, true)) {
			unset($itemIds[array_search(0, $itemIds, true)]);
		}

		if (empty($itemIds)) {
			$this->setError(JText::_('JError_No_items_selected'));
			return false;
		}

		$done = false;

		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->_batchAccess($commands['assetgroup_id'], $itemIds)) {
				return false;
			}
			$done = true;
		}

		if (!empty($commands['menu_id']))
		{
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c' && !$this->_batchCopy($commands['menu_id'], $itemIds)) {
				return false;
			}
			else if ($cmd == 'm' && !$this->_batchMove($commands['menu_id'], $itemIds)) {
				return false;
			}
			$done = true;
		}

		if (!$done)
		{
			$this->setError('Menus_Error_Insufficient_batch_information');
			return false;
		}

		return true;
	}

	/**
	 * Batch access level changes for a group of rows.
	 *
	 * @param	int		The new value matching an Asset Group ID.
	 * @param	array	An array of row IDs.
	 *
	 * @return	booelan	True if successful, false otherwise and internal error is set.
	 */
	protected function _batchAccess($value, $itemIds)
	{
		$table = &$this->getTable();
		foreach ($itemIds as $id)
		{
			$table->reset();
			$table->load($id);
			$table->access = (int) $value;
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Batch move menu items to a new menu or parent.
	 *
	 * @param	int		The new menu or sub-item.
	 * @param	array	An array of row IDs.
	 *
	 * @return	booelan	True if successful, false otherwise and internal error is set.
	 */
	protected function _batchMove($value, $itemIds)
	{
		// $value comes as {menutype}.{parent_id}
		$parts		= explode('.', $value);
		$menuType	= $parts[0];
		$parentId	= (int) JArrayHelper::getValue($parts, 1, 0);

		$table	= &$this->getTable();
		$db		= &$this->getDbo();

		// Check that the parent exists
		if ($parentId)
		{
			if (!$table->load($parentId))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Non-fatal error
					$this->setError(JText::_('Menus_Batch_Move_parent_not_found'));
					$parentId = 0;
				}
			}
		}

		// If the parent is 0, set it to the ID of the root item in the tree
		if (empty($parentId))
		{
			if (!$parentId = $table->getRootId()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// We are going to store all the children and just moved the menutype
		$children = array();

		// Parent exists so we let's proceed
		foreach ($itemIds as $id)
		{
			$table->reset();

			// Check that the row actually exists
			if (!$table->load($id))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('Menus_Batch_Move_row_not_found', $id));
					continue;
				}
			}

			// Check if we are moving to a different menu
			if ($menuType != $table->menutype)
			{
				// Find any children to this row.
				$db->setQuery(
					'SELECT id' .
					' FROM #__menu' .
					' WHERE left_id > '.(int) $table->left_id.' AND right_id < '.(int) $table->right_id
				);
				$childIds = $db->loadResultArray();

				// Add child ID's to the array only if they aren't already there.
				foreach ($childIds as $childId)
				{
					if (!in_array($childId, $itemIds)) {
						$children[] = $childId;
					}
				}
			}

			$table->parent_id	= $parentId;
			$table->menutype	= $menuType;
			$table->ordering	= 1;
			$table->level		= null;
			$table->left_id		= null;
			$table->right_id	= null;

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Process the child rows
		if (!empty($children))
		{
			$db->setQuery(
				'UPDATE #__menu' .
				' SET menutype = '.$db->quote($menuType).
				' WHERE id IN ('.implode(',', $children).')'
			);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		// Rebuild the hierarchy.
		if (!$table->rebuildTree()) {
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the tree path.
		if (!$table->rebuildPath($table->id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Batch copy menu items to a new menu or parent.
	 *
	 * @param	int		The new menu or sub-item.
	 * @param	array	An array of row IDs.
	 *
	 * @return	booelan	True if successful, false otherwise and internal error is set.
	 */
	protected function _batchCopy($value, $itemIds)
	{
		// $value comes as {menutype}.{parent_id}
		$parts		= explode('.', $value);
		$menuType	= $parts[0];
		$parentId	= (int) JArrayHelper::getValue($parts, 1, 0);

		$table	= &$this->getTable();
		$db		= &$this->getDbo();

		// Check that the parent exists
		if ($parentId)
		{
			if (!$table->load($parentId))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Non-fatal error
					$this->setError(JText::_('Menus_Batch_Move_parent_not_found'));
					$parentId = 0;
				}
			}
		}

		// If the parent is 0, set it to the ID of the root item in the tree
		if (empty($parentId))
		{
			if (!$parentId = $table->getRootId()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// We need to log the parent ID
		$parents = array();

		// Calculate the emergency stop count as a precaution against a runaway loop bug
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM #__menu'
		);
		$count = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			$this->setError($error);
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($itemIds) && $count > 0)
		{
			// Pop the first id off the stack
			$id = array_shift($itemIds);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($id))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('Menus_Batch_Move_row_not_found', $id));
					continue;
				}
			}

			// Copy is a bit tricky, because we also need to copy the children
			$db->setQuery(
				'SELECT id' .
				' FROM #__menu' .
				' WHERE left_id > '.(int) $table->left_id.' AND right_id < '.(int) $table->right_id
			);
			$childIds = $db->loadResultArray();

			// Add child ID's to the array only if they aren't already there.
			foreach ($childIds as $childId)
			{
				if (!in_array($childId, $itemIds)) {
					array_push($itemIds, $childId);
				}
			}

			// Make a copy of the old ID and Parent ID
			$oldId				= $table->id;
			$oldParentId		= $table->parent_id;

			// Reset the id because we are making a copy.
			$table->id			= 0;

			// If we a copying children, the Old ID will turn up in the parents list
			// otherwise it's a new top level item
			$table->parent_id	= isset($parents[$oldParentId]) ? $parents[$oldParentId] : $parentId;
			$table->menutype	= $menuType;
			// TODO: Deal with ordering?
			//$table->ordering	= 1;
			$table->level		= null;
			$table->left_id		= null;
			$table->right_id	= null;

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			// Now we log the old 'parent' to the new 'parent'
			$parents[$oldId] = $table->id;
			$count--;
		}

		// Rebuild the hierarchy.
		if (!$table->rebuildTree()) {
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the tree path.
		if (!$table->rebuildPath($table->id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}
}
