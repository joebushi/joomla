<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die('Invalid Request.');

jimport( 'joomla.application.component.controller' );

/**
 * The Menu Item Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class MenusControllerItem extends JController
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function __construct()
	{
		parent::__construct();

		// Map the save tasks.
		$this->registerTask('save2new',		'save');
		$this->registerTask('apply',		'save');
	}

	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_menus', false));
	}

	/**
	 * Method to add a new menu item.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function add()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the menu item edit information from the session.
		$app->setUserState('com_menus.edit.item.id', null);
		$app->setUserState('com_menus.edit.item.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
	}

	/**
	 * Method to edit an existing menu item.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function edit()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		// Get the id of the group to edit.
		$itemId = (int) (count($cid) ? $cid[0] : JRequest::getInt('item_id'));

		// Set the menu item id for the item to edit in the session.
		$app->setUserState('com_menus.edit.item.id', $itemId);
		$app->setUserState('com_menus.edit.item.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item ID in the session to null, and then redirects to the list page.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function cancel()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the menu item edit information from the session.
		$app->setUserState('com_menus.edit.item.id', null);
		$app->setUserState('com_menus.edit.item.data', null);

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=items', false));
	}

	/**
	 * Method to save a menu item.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app = &JFactory::getApplication();

		// Get the posted values from the request.
		$data = JRequest::getVar('jform', array(), 'post', 'array');

		// Populate the row id from the session.
		$data['id'] = (int) $app->getUserState('com_menus.edit.item.id');

		// Get the model and attempt to validate the posted data.
		$model = &$this->getModel('Item');
		$validated = $model->validate($data);

		// Check for validation errors.
		if ($validated === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($validated);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Menus_Item_Save_Failed', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
			return false;
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task)
		{
			case 'apply':
				// Redirect back to the edit screen.
				$this->setMessage(JText::_('Menus_Item_Save_Success'));
				$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
				break;

			case 'save2new':
				// Clear the menu item id and data from the session.
				$app->setUserState('com_menus.edit.item.id', null);
				$app->setUserState('com_menus.edit.item.data', null);

				// Redirect back to the edit screen.
				$this->setMessage(JText::_('Menus_Item_Save_Success'));
				$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
				break;

			default:
				// Clear the menu item id and data from the session.
				$app->setUserState('com_menus.edit.item.id', null);
				$app->setUserState('com_menus.edit.item.data', null);

				// Redirect to the list screen.
				$this->setMessage(JText::_('Menus_Item_Save_Success'));
				$this->setRedirect(JRoute::_('index.php?option=com_menus&view=items', false));
				break;
		}
	}

	/**
	 * Method to delete menu items.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function delete()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get and sanitize the items to delete.
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		// Get the model.
		$model = &$this->getModel('Item');

		// Attempt to delete the item(s).
		if (!$model->delete($cid)) {
			$this->setMessage(JText::sprintf('Menus_Item_Delete_Failed', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::sprintf('Menus_Item_Delete_Success', count($cid)));
		}

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=items', false));
	}
}
