<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Menu Item Model for Menus.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class MenusModelItem extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	 protected $_context		= 'menus.item';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
		// Initialize variables.
		$app = &JFactory::getApplication('administrator');

		// Load the group state.
		if (!$itemId = (int)$app->getUserState('com_menus.edit.item.id')) {
			$itemId = (int)JRequest::getInt('item_id');
		}
		$this->setState('item.id', $itemId);

		// Load the parameters.
		$params = &JComponentHelper::getParams('com_menus');
		$this->setState('params', $params);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @access	public
	 * @param	integer	The id of the menu item to get.
	 * @return	mixed	Menu item data object on success, false on failure.
	 * @since	1.0
	 */
	public function & getItem($itemId = null)
	{
		// Initialize variables.
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('item.id');
		$false	= false;

		// Get a menu item row instance.
		$table = &$this->getTable('Menu', 'JTable');

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->serError($table->getError());
			return $false;
		}

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$value = JArrayHelper::toObject($table->getProperties(1), 'JObject');
		return $value;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @return	mixed	JForm object on success, false on failure.
	 * @since	1.6
	 */
	public function & getForm()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$false	= false;

		// Get the form.
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		$form = &JForm::getInstance('jform', 'item', true, array('array' => true));

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_menus.edit.item.data', array());

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
		$itemId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('item.id');
		$isNew	= true;

		// Get a group row instance.
		$table = &$this->getTable('Menu', 'JTable');

		// Load the row if saving an existing item.
		if ($itemId > 0) {
			$table->load($itemId);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $table->id;
	}

	/**
	 * Method to delete groups.
	 *
	 * @param	array	An array of group ids.
	 * @return	boolean	Returns true on success, false on failure.
	 */
	public function delete($groupIds)
	{
		// Sanitize the ids.
		$groupIds = (array) $groupIds;
		JArrayHelper::toInteger($groupIds);

		// Get a group row instance.
		$table = &$this->getTable('Usergroup', 'JTable');

		// Iterate the items to delete each one.
		foreach ($groupIds as $groupId)
		{
			$table->delete($groupId);
		}

		// Rebuild the nested set tree.
		$table->rebuild();

		return true;
	}
}
