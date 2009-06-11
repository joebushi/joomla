<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.theartofjoomla.com
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class ContentControllerArticle extends JController
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->registerTask('save2copy',	'save');
		$this->registerTask('save2new',		'save');
		$this->registerTask('apply',		'save');
	}

	/**
	 * Display the view
	 */
	function display()
	{
	}

	/**
	 * Proxy for getModel
	 */
	function &getModel()
	{
		return parent::getModel('Article', '', array('ignore_request' => true));
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
		$app->setUserState('com_content.edit.article.id',	null);
		$app->setUserState('com_content.edit.article.data',	null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&layout=edit', false));
	}

	/**
	 * Method to edit a object
	 *
	 * Sets object ID in the session from the request, checks the item out, and then redirects to the edit page.
	 *
	 * @access	public
	 * @return	void
	 */
	function edit()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		// Get the id of the group to edit.
		$id =  (empty($ids) ? JRequest::getInt('article_id') : (int) array_pop($ids));

		// Get the previous row id (if any) and the current row id.
		$previousId	= (int) $app->getUserState('com_content.edit.article.id');
		$app->setUserState('com_content.edit.article.id', $id);

		// Get the menu item model.
		$model = &$this->getModel();

		// Check that this is not a new item.
		if ($id > 0)
		{
			$item = $model->getItem($id);

			// If not already checked out, do so.
			if ($item->checked_out == 0)
			{
				if (!$model->checkout($id))
				{
					// Check-out failed, go back to the list and display a notice.
					$message = JText::sprintf('JError_Checkout_failed', $model->getError());
					$this->setRedirect('index.php?option=com_menus&view=item&item_id='.$id, $message, 'error');
					return false;
				}
			}
		}

		// Check-out succeeded, push the new row id into the session.
		$app->setUserState('com_content.edit.article.id',	$id);
		$app->setUserState('com_content.edit.article.data',	null);

		$this->setRedirect('index.php?option=com_content&view=article&layout=edit');

		return true;
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item ID in the session to null, and then redirects to the list page.
	 *
	 * @access	public
	 * @return	void
	 */
	function cancel()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Get the previous menu item id (if any) and the current menu item id.
		$previousId	= (int) $app->getUserState('com_content.edit.article.id');

		// Get the menu item model.
		$model = &$this->getModel();

		// If rows ids do not match, checkin previous row.
		if (!$model->checkin($previousId))
		{
			// Check-in failed, go back to the menu item and display a notice.
			$message = JText::sprintf('JError_Checkin_failed', $model->getError());
			$this->setRedirect('index.php?option=com_content&view=article&layout=edit', $message, 'error');
			return false;
		}

		// Clear the menu item edit information from the session.
		$app->setUserState('com_content.edit.article.id',	null);
		$app->setUserState('com_content.edit.article.data',	null);

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_content&view=articles', false));
	}

	/**
	 * Save the record
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken();

		// Get posted form variables.
		$values		= JRequest::getVar('jform', array(), 'post', 'array');

		// Get the id of the item out of the session.
		$session	= &JFactory::getSession();
		$id			= (int) $session->get('com_content.edit.article.id');
		$values['id'] = $id;

		// Get the extensions model and set the post request in its state.
		$model	= &$this->getModel();
		$post = JRequest::get('post');
		$model->setState('request', $post);

		// Get the form model object and validate it.
		$form	= &$model->getForm('model');
		$form->filter($values);
		$result	= $form->validate($values);

		if (JError::isError($result)) {
			JError::raiseError(500, $result->message);
		}

		$result	= $model->save($values);
		$msg	= JError::isError($result) ? $result->message : 'Saved';

		if ($this->_task == 'apply')
		{
			$session->set('com_content.edit.article.id', $model->getState('article.id'));
			$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&layout=edit', false), JText::_($msg));
		}
		else if ($this->_task == 'save2new')
		{
			$session->set('com_content.edit.article.id', null);
			$model->checkin($id);
			$this->setRedirect(JRoute::_('index.php?option=com_content&view=article&layout=edit', false), JText::_($msg));
		}
		else
		{
			$session->set('com_content.edit.article.id', null);
			$model->checkin($id);
			$this->setRedirect(JRoute::_('index.php?option=com_content&view=articles', false), JText::_($msg));
		}
	}

	/**
	 * Removes an item
	 */
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('Select an item to delete'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_content&view=articles');
	}

	/**
	 * Method to publish a list of taxa
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('publish' => 1, 'unpublish' => 0, 'trash' => -2);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('Select an item to publish'));
		}
		else {
			// Get the model.
			$model	= $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_content&view=articles');
	}

	/**
	 * Changes the order of an item
	 */
	function ordering()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid	= JRequest::getVar('cid', null, 'post', 'array');
		$inc	= $this->getTask() == 'orderup' ? -1 : +1;

		$model = & $this->getModel();
		$model->ordering($cid, $inc);

		$this->setRedirect('index.php?option=com_content&view=articles');
	}
}