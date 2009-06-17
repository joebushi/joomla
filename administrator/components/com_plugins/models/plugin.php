<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Plugins Component Plugin Model.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @since		1.5
 */
class PluginsModelPlugin extends JModelItem
{	
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState()
	{
		$app = &JFactory::getApplication('administrator');
		$params = &JComponentHelper::getParams('com_plugins');

		// Load the User state.
		if (JRequest::getWord('layout') === 'edit') {
			$pluginId = (int) $app->getUserState('com_plugins.edit.plugin.id');
			$this->setState('plugin.id', $pluginId);
		}
		else
		{
			$pluginId = (int) JRequest::getInt('plugin_id');
			$this->setState('plugin.id', $pluginId);
		}

		// Load the parameters.
		$this->setState('params', $params);
	}
	
	/**
	 * Method to checkin a row.
	 *
	 * @param	int		$pluginId	The numeric id of a row
	 * @return	bool	True on success/false on failure
	 * @since	1.6
	 */
	public function checkin((int) $pluginId = null)
	{
		// Initialize variables.
		$user = &JFactory::getUser();
		$userId = (int) $user->get('id');
		
		//$table = &$this->getTable();

		// Attempt to check-in the row.
		$return = $table->checkin($userId, $pluginId);

		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}
	
	/**
	 * Method to check-out a plugin for editing.
	 *
	 * @param	int		$pluginId	The numeric id of the plugin to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout((int) $pluginId)
	{
		// Initialize variables.
		$user = (int) &JFactory::getUser()->get('id');
		//$userId = (int) $user->get('id');

		$table = &$this->getTable();

		// Attempt to check-out the row.
		$return = $table->checkout($userId, $pluginId);

		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		// Check if the row is checked-out by someone else.
		if ($return === null) {
			$this->setError(JText::_('JCommon_Item_is_checked_out'));
			return false;
		}

		return true;
	}
}