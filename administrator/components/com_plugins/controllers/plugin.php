<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Plugin controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @version		1.6
 */
class PluginsControllerPlugin extends JController
{
/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct(array $config = array())
	{
		parent::__construct($config);
		$this->registerTask('apply', 'save');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('edit', 'display');
		$this->registerTask('add', 'display');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');
	}
	
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_plugins', false));
	}
	
	/**
	 * Method to edit a plugin.
	 *
	 * @return	void
	 */
	public function edit()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();
		$model = &$this->getModel('Plugin', 'PluginsModel');
		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		// Get the plugin id.
		$id = (int) (count($cid) ? $cid[0] : JRequest::getInt('id'));

		// Attempt to check-out the plugin for editing and redirect.
		if (!$model->checkout($id)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_plugins&view=plugin&layout=edit&id='.$id, $message, 'error');
			return false;
		}
		else
		{
			// Check-out succeeded, push the plugin id into the session.
			$app->setUserState('com_plugins.edit.plugin.id', $pluginId);
			$app->setUserState('com_plugins.edit.plugin.data', null);
			$this->setRedirect('index.php?option=com_plugins&view=plugin&layout=edit');
			return true;
		}
	}
	
	/**
	 * Method to cancel an edit
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function cancel()
	{
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app = &JFactory::getApplication();
		$model = &$this->getModel('Plugin', 'PluginsModel');

		// Get the plugin id.
		$pluginId = (int) $app->getUserState('com_plugins.edit.plugin.id');

		// Attempt to check-in the plugin.
		if ($pluginId) {
			if (!$model->checkin($pluginId)) {
				// Check-in failed, go back to the plugion and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_plugins&view=plugin&layout=edit&hidemainmenu=1', $message, 'error');
				return false;
			}
		}

		// Clean the session data and redirect.
		$app->setUserState('com_plugins.edit.plugin.id', null);
		$app->setUserState('com_plugins.edit.plugin.data', null);
		$this->setRedirect(JRoute::_('index.php?option=com_plugins&view=plugins', false));
	}
}