<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Weblinks controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @version		1.6
 */
class ModulesControllerModules extends JController
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unpublish',	'publish');
		$this->registerTask('trash',		'publish');
		$this->registerTask('orderup',		'reorder');
		$this->registerTask('orderdown',	'reorder');
	}

	/**
	 * Method to delete item(s) from the database.
	 *
	 * @access	public
	 */
	public function delete()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('module');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($cid);

		// Attempt to delete the weblinks
		$return = $model->delete($cid);

		// Delete the weblinks
		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_modules&view=modules', $message, 'error');
			return false;
		}
		else {
			$message = JText::_('JSuccess_N_items_deleted');
			$this->setRedirect('index.php?option=com_modules&view=modules', $message);
			return true;
		}
	}

	/**
	 * Method to publish unpublished item(s).
	 *
	 * @return	void
	 */
	public function publish()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model	= &$this->getModel('modules');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		JArrayHelper::toInteger($cid);

		// Check for items.
		if (count($cid) < 1) {
			$message = JText::_('JError_No_item_selected');
			$this->setRedirect('index.php?option=com_modules&view=modules', $message, 'warning');
			return false;
		}

		// Attempt to publish the items.
		$task	= $this->getTask();
		if ($task == 'publish') {
			$value = 1;
		}
		else {
			$value = 0;
		}

		$return = $model->setStates($cid, $value);

		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_modules&view=modules', $message, 'error');
			return false;
		}
		else {
			$message = $value ? JText::_('JSuccess_N_items_published') : JText::_('JSuccess_N_items_unpublished');
			$this->setRedirect('index.php?option=com_modules&view=modules', $message);
			return true;
		}
	}

	/**
	 * Method to reorder weblinks.
	 *
	 * @return	bool	False on failure or error, true on success.
	 */
	public function reorder()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$model	= &$this->getModel('module');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		// Get the weblink id.
		$weblinkId = (int) $cid[0];

		// Attempt to move the row.
		$return = $model->reorder($weblinkId, $this->getTask() == 'orderup' ? -1 : 1);

		if ($return === false) {
			// Move failed, go back to the weblink and display a notice.
			$message = JText::sprintf('JError_Reorder_failed', $model->getError());
			$this->setRedirect('index.php?option=com_modules&view=modules', $message, 'error');
			return false;
		}
		else {
			// Move succeeded, go back to the weblink and display a message.
			$message = JText::_('JSuccess_Item_reordered');
			$this->setRedirect('index.php?option=com_modules&view=modules', $message);
			return true;
		}
	}


	/**
	 * Method to save the current ordering arrangement.
	 *
	 * @return	void
	 */
	public function saveorder()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get the input
		$cid	= JRequest::getVar('cid',	null,	'post',	'array');
		$order	= JRequest::getVar('order',	null,	'post',	'array');

		// Sanitize the input
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = &$this->getModel('modules');

		// Save the ordering
		$model->saveorder($cid, $order);

		$message = JText::_('JSuccess_Ordering_saved');
		$this->setRedirect('index.php?option=com_modules&view=modules', $message);
	}
}