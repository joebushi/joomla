<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.controller' );

/**
 * The Menu Item Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class MenusControllerItem extends JController
{
	/**
	 * Constructor
	 */
	public public function __construct()
	{
		parent::__construct();

		// Register proxy tasks.
		$this->registerTask('save2new',	'save');
		$this->registerTask('apply',	'save');
	}

	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_menus', false));
	}

	/**
	 * Method to add a new menu item.
	 *
	 * @return	void
	 */
	public function add()
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
	 * @return	void
	 */
	public function edit()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		// Get the id of the group to edit.
		$id =  (empty($ids) ? JRequest::getInt('item_id') : (int) array_pop($ids));

		// Get the previous menu item id (if any) and the current menu item id.
		$previousId	= (int) $app->getUserState('com_menus.edit.item.id');
		$app->setUserState('com_menus.edit.item.id', $id);

		// Get the menu item model.
		$model = &$this->getModel('Item');

		// If rows ids do not match, checkin previous row.
		if (($previousId > 0) && ($id != $previousId))
		{
			if (!$model->checkin($previousId))
			{
				// Check-in failed, go back to the weblink and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_menus&view=item&layout=edit', $message, 'error');
				return false;
			}
		}

		// Attempt to check-out the new weblink for editing and redirect.
		if (!$model->checkout($id))
		{
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_menus&view=item&item_id='.$id, $message, 'error');
			return false;
		}
		else
		{
			// Check-out succeeded, push the new row id into the session.
			$app->setUserState('com_menus.edit.weblink.id',	$id);
			$app->setUserState('com_menus.edit.weblink.data', null);
			$this->setRedirect('index.php?option=com_menus&view=item&layout=edit');
			return true;
		}
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item ID in the session to null, and then redirects to the list page.
	 *
	 * @return	void
	 */
	public function cancel()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Get the previous menu item id (if any) and the current menu item id.
		$previousId	= (int) $app->getUserState('com_menus.edit.item.id');

		// Get the menu item model.
		$model = &$this->getModel('Item');

		// If rows ids do not match, checkin previous row.
		if (!$model->checkin($previousId))
		{
			// Check-in failed, go back to the menu item and display a notice.
			$message = JText::sprintf('JError_Checkin_failed', $model->getError());
			$this->setRedirect('index.php?option=com_menus&view=item&layout=edit', $message, 'error');
			return false;
		}

		// Clear the menu item edit information from the session.
		$app->setUserState('com_menus.edit.item.id', null);
		$app->setUserState('com_menus.edit.item.data', null);

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=items', false));
	}

	/**
	 * Method to save a menu item.
	 *
	 * @return	void
	 */
	public function save()
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
		$model	= &$this->getModel('Item');
		$form	= &$model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data	= $model->validate($form, $data);

		// Check for validation errors.
		if ($data === false)
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
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JError_Save_failed', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
			return false;
		}

		// Save succeeded, check-in the row.
		if (!$model->checkin())
		{
			// Check-in failed, go back to the weblink and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_menus&view=item&layout=edit', $message, 'error');
			return false;
		}

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task)
		{
			case 'apply':
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
				break;

			case 'save2new':
				// Clear the menu item id and data from the session.
				$app->setUserState('com_menus.edit.item.id', null);
				$app->setUserState('com_menus.edit.item.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
				break;

			default:
				// Clear the menu item id and data from the session.
				$app->setUserState('com_menus.edit.item.id', null);
				$app->setUserState('com_menus.edit.item.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_menus&view=items', false));
				break;
		}
	}
}
