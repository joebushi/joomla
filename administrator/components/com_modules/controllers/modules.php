<?php
/**
 * @version		$Id
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Modules controller class.
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
		$this->registerTask('unpublish',		'publish');
		$this->registerTask('orderup',			'reorder');
		$this->registerTask('orderdown',		'reorder');
		$this->registerTask('accesspublic', 	'access');
		$this->registerTask('accessregistered',	'access');
		$this->registerTask('accessspecial',	'access');
	}

	/**
	 * Copies the selected modules
	 *
	 * @return	void
	 */
	function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		// Sanitize the input.
		JArrayHelper::toInteger($cid);
		$n		= count($cid);
		
		if ($n == 0) {
			return JError::raiseWarning(500, JText::_('No items selected'));
		}

		$model	= $this->getModel('Module');

		if ($model->copy($cid)) {
			$msg = JText::sprintf('Items Copied', $n);
		} else {
			$msg = JText::_('Error Copying Module(s)');
		}

		$this->setRedirect('index.php?option=com_modules&client='. $client->id, $msg);
	}

	/**
	 * Deletes one or more modules
	 *
	 * Also deletes associated entries in the #__module_menu table.
	 *
	 * @return	void
	 */
 	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		$n		= count($cid);

		if ($n == 0) {
			return JError::raiseWarning(500, 'No items selected');
		}

		$model	= $this->getModel('Module');

		if ($model->delete($cid)) {
			$msg = JText::sprintf('Items removed', $n);
		} else {
			$msg = JText::_('Error Deleting');
		}

		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$this->setRedirect('index.php?option=com_modules&client='.$client->id, $msg);
	}

	/**
	 * Publishes or Unpublishes one or more modules
	 *
	 * @return	void
	 */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialize some variables
		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$task	= $this->getTask();
		$publish	= ($task == 'publish');

		$cache = & JFactory::getCache();
		$cache->clean('com_content');

		$cid 	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		if (empty($cid)) {
			return JError::raiseWarning(500, 'No items selected');
		}

		$model = $this->getModel('module');
		if (!$model->publish($cid, $publish)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_modules&client='.$client->id);
	}

	/**
	 * Moves the order of a record
	 *
	 * @return	void
	 */
	function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$cid 	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$task	= $this->getTask();
		$inc	= ($task == 'orderup' ? -1 : 1);

		if (empty($cid)) {
			return JError::raiseWarning(500, 'No items selected');
		}

		$model = $this->getModel('module');

		if (!$model->move($inc)) {
			$msg = $model->getError();
		}

		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$this->setRedirect('index.php?option=com_modules&client='.$client->id, $msg);
	}

	/**
	 * Changes the access level of a record
	 *
	 * @return	void
	 */
	function access()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialize some variables
		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		$cid 	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (empty($cid)) {
			return JError::raiseWarning(500, 'No items selected');
		}

		$task	= JRequest::getCmd('task');
		switch ($task)
		{
			case 'accesspublic':
				$access = 0;
				break;

			case 'accessregistered':
				$access = 1;
				break;

			case 'accessspecial':
				$access = 2;
				break;
		}

		$msg = '';
		$model = $this->getModel('module');
		if (!$model->setAccess($cid, $access)) {
			$msg = $model->getError();
		}

		$this->setRedirect('index.php?option=com_modules&client='.$client->id, $msg);
	}

	/**
	 * Saves the orders of the supplied list
	 */
	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialize some variables

		$cid 	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (empty($cid)) {
			return JError::raiseWarning(500, 'No items selected');
		}

		$order 		= JRequest::getVar('order', array(0), 'post', 'array');
		JArrayHelper::toInteger($order);

		$model = $this->getModel('module');
		$model->saveorder($cid, $order);

		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$msg = JText::_('New ordering saved');
		$this->setRedirect('index.php?option=com_modules&client='.$client->id, $msg);
	}

}