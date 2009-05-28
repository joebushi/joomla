<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.database.query');

/**
 * Category model for Category.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @version		1.0
 */
class CategoryModelCategory extends JModelItem
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set	= false;

	/**
	 * Array of items for memory caching.
	 *
	 * @access	protected
	 * @var		array
	 */
	var $_items			= array();

	/**
	 * Overridden method to get model state variables.
	 *
	 * @access	public
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	function getState($property = null, $default = null)
	{
		if (!$this->__state_set)
		{
			// Get the application object.
			$app = &JFactory::getApplication();

			// Attempt to auto-load the category id.
			if (!$categoryId = (int)$app->getUserState('com_categories.edit.category.id')) {
				$categoryId = (int)JRequest::getInt('cat_id');
			}

			// Only set the category id if there is a value.
			if ($categoryId) {
				$this->setState('category.id', $categoryId);
			}

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * Method to get a category item.
	 *
	 * @access	public
	 * @param	integer	The id of the category to get.
	 * @return	mixed	Category data object on success, false on failure.
	 * @since	1.0
	 */
	function &getItem($categoryId = null)
	{
		// Initialize variables.
		$categoryId = (!empty($categoryId)) ? $categoryId : (int)$this->getState('category.id');
		$false	= false;

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Attempt to load the row.
		$return = $table->load($categoryId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
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
	 * Method to get the category form.
	 *
	 * @access	public
	 * @return	mixed	JForm object on success, false on failure.
	 * @since	1.0
	 */
	function &getForm()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$false	= false;

		// Get the form.
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		$form = &JForm::getInstance('JForm', 'category', true, array('array' => true));

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_categories.edit.category.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to publish categories.
	 *
	 * @access	public
	 * @param	array	The ids of the items to publish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function publish($categoryIds)
	{
		// Sanitize the ids.
		$categoryIds = (array) $categoryIds;
		JArrayHelper::toInteger($categoryIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Attempt to publish the items.
		if (!$table->publish($categoryIds, 1, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to unpublish categories.
	 *
	 * @access	public
	 * @param	array	The ids of the items to unpublish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function unpublish($categoryIds)
	{
		// Sanitize the ids.
		$categoryIds = (array) $categoryIds;
		JArrayHelper::toInteger($categoryIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Attempt to unpublish the items.
		if (!$table->publish($categoryIds, 0, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to put categories in the trash.
	 *
	 * @access	public
	 * @param	array	The ids of the items to trash.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function trash($categoryIds)
	{
		// Sanitize the ids.
		$categoryIds = (array) $categoryIds;
		JArrayHelper::toInteger($categoryIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Attempt to trash the items.
		if (!$table->publish($categoryIds, -2, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to delete categories.
	 *
	 * @access	public
	 * @param	array	An array of category ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function delete($categoryIds)
	{
		// Sanitize the ids.
		$categoryIds = (array) $categoryIds;
		JArrayHelper::toInteger($categoryIds);

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Iterate the categories to delete each one.
		foreach ($categoryIds as $categoryId)
		{
			$table->delete($categoryId);
		}

		// Rebuild the nested set tree.
		$table->rebuild();

		return true;
	}

	/**
	 * Adjust the category ordering.
	 *
	 * @access	public
	 * @param	integer	Primary key of the item to adjust.
	 * @param	integer	Increment, usually +1 or -1
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function ordering($id, $move = 0)
	{
		// Sanitize the id and adjustment.
		$id = (int) $id;
		$move = (int) $move;

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Attempt to adjust the row ordering.
		if (!$table->ordering($move, $id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check in a category.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check in.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkin($categoryId = null)
	{
		// Initialize variables.
		$categoryId	= (!empty($categoryId)) ? $categoryId : (int)$this->getState('category.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($categoryId)
		{
			// Get a category row instance.
			$table = &$this->getTable('Category', 'JTable');

			// Attempt to check the row in.
			if (!$table->checkin($categoryId)) {
				$this->setError($table->getError());
				$result	= false;
			}
		}

		return $result;
	}

	/**
	 * Method to check out a category.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check out.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkout($categoryId = null)
	{
		// Initialize variables.
		$categoryId	= (!empty($categoryId)) ? $categoryId : (int)$this->getState('category.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($categoryId)
		{
			// Get a category row instance.
			$table = &$this->getTable('Category', 'JTable');

			// Get the current user object.
			$user = &JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $categoryId)) {
				$this->setError($table->getError());
				$result	= false;
			}
		}

		return $result;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	mixed	Array of filtered data if valid, false otherwise.
	 * @since	1.0
	 */
	function validate($data)
	{
		// Get the form.
		$form = &$this->getForm();

		// Check for an error.
		if ($form === false) {
			return false;
		}

		// Filter and validate the form data.
		$data	= $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if (JError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function save($data)
	{
		$categoryId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('category.id');
		$isNew	= true;

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		// Load the row if saving an existing item.
		if ($categoryId > 0) {
			$table->load($categoryId);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('Category_BIND FAILED', $table->getError()));
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

		// Get the root category.
		$this->_db->setQuery(
			'SELECT `id`' .
			' FROM `#__categories`' .
			' WHERE `parent_id` = 0',
			0, 1
		);
		$rootId	= (int)$this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Rebuild the hierarchy.
		if (!$table->rebuild($rootId)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Build the category path.
		if (!$table->buildPath($table->id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $table->id;
	}

	/**
	 * Method to perform batch operations on a category or a set of categories.
	 *
	 * @access	public
	 * @param	array	An array of commands to perform.
	 * @param	array	An array of category ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function batch($commands, $categoryIds)
	{
		// Sanitize the ids.
		$categoryIds = (array) $categoryIds;
		JArrayHelper::toInteger($categoryIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'JTable');

		/*
		 * BUILD OUT BATCH OPERATIONS
		 */

		return true;
	}
}