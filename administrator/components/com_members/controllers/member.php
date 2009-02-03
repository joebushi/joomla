<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die('Invalid Request.');

jimport( 'joomla.application.component.controller' );

/**
 * The Members Member Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_members
 * @version		1.6
 */
class MembersControllerMember extends JController
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
		$this->setRedirect(JRoute::_('index.php?option=com_members', false));
	}

	/**
	 * Method to add a new member.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function add()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the level edit information from the session.
		$app->setUserState('com_members.edit.member.id', null);
		$app->setUserState('com_members.edit.member.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_members&view=member&layout=edit&hidemainmenu=1', false));
	}

	/**
	 * Method to edit an existing member.
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

		// Get the id of the member to edit.
		$memberId = (int) (count($cid) ? $cid[0] : JRequest::getInt('member_id'));

		// Set the id for the member to edit in the session.
		$app->setUserState('com_members.edit.member.id', $memberId);
		$app->setUserState('com_members.edit.member.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_members&view=member&layout=edit&hidemainmenu=1', false));
	}

	/**
	 * Method to cancel an edit
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function cancel()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the member edit information from the session.
		$app->setUserState('com_members.edit.member.id', null);
		$app->setUserState('com_members.edit.member.data', null);

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_members&view=members', false));
	}

	/**
	 * Method to save a member.
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
		$data['id'] = (int) $app->getUserState('com_members.edit.member.id');

		// Get the model and attempt to validate the posted data.
		$model = &$this->getModel('Member');
		$return	= $model->validate($data);

		// Get and sanitize the group data.
		$data['groups'] = JRequest::getVar('groups', array(), 'post', 'array');
		$data['groups'] = array_unique($data['groups']);
		JArrayHelper::toInteger($data['groups']);

		// Remove any values of zero.
		if (array_search(0, $data['groups'], true)) {
			unset($data['groups'][array_search(0, $data['groups'], true)]);
		}

		// Check for validation errors.
		if ($return === false)
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
			$app->setUserState('com_members.edit.member.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_members&view=member&layout=edit&hidemainmenu=1', false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_members.edit.member.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('MEMBERS_MEMBER_SAVE_FAILED', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_members&view=member&layout=edit&hidemainmenu=1', false));
			return false;
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task)
		{
			case 'apply':
				// Redirect back to the edit screen.
				$this->setMessage(JText::_('MEMBERS_MEMBER_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_members&view=member&layout=edit&hidemainmenu=1', false));
				break;

			case 'save2new':
				// Clear the member id and data from the session.
				$app->setUserState('com_members.edit.member.id', null);
				$app->setUserState('com_members.edit.member.data', null);

				// Redirect back to the edit screen.
				$this->setMessage(JText::_('MEMBERS_MEMBER_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_members&view=member&layout=edit&hidemainmenu=1', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_members.edit.member.id', null);
				$app->setUserState('com_members.edit.member.data', null);

				// Redirect to the list screen.
				$this->setMessage(JText::_('MEMBERS_MEMBER_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_members&view=members', false));
				break;
		}
	}

	/**
	 * Method to run batch opterations.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function batch()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Member');
		$vars	= JRequest::getVar('batch', array(), 'post', 'array');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize member ids.
		$cid = array_unique($cid);
		JArrayHelper::toInteger($cid);

		// Remove any values of zero.
		if (array_search(0, $cid, true)) {
			unset($cid[array_search(0, $cid, true)]);
		}

		// Attempt to run the batch operation.
		if (!$model->batch($vars, $cid))
		{
			// Batch operation failed, go back to the members list and display a notice.
			$message = JText::sprintf('MEMBERS_MEMBERS_BATCH_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_members&view=members', $message, 'error');
			return false;
		}

		$message = JText::_('MEMBERS_MEMBERS_BATCH_SUCCESS');
		$this->setRedirect('index.php?option=com_members&view=members', $message);
		return true;
	}

	/**
	 * Method to delete members.
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
		$model = &$this->getModel('Member');

		// Attempt to delete the item(s).
		if (!$model->delete($cid)) {
			$this->setMessage(JText::sprintf('MEMBERS_MEMBER_DELETE_FAILED', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::sprintf('MEMBERS_MEMBER_DELETE_SUCCESS', count($cid)));
		}

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_members&view=members', false));
	}
}