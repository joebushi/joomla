<?php
/**
 * @version		$Id: components.php 9764 2007-12-30 07:48:11Z ircmaxell $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Import library dependencies
require_once(dirname(__FILE__).DS.'extension.php');

/**
 * Installer Manage Model
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerModelManage extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'extension';

	/**
	 * Enable an extension
	 *
	 * @static
	 * @return boolean True on success
	 * @since 1.0
	 */
	function enable($eid=array())
	{
		// Initialize variables
		$result	= false;

		/*
		 * Ensure eid is an array of extension ids
		 * TODO: If it isn't an array do we want to set an error and fail?
		 */
		if (!is_array($eid)) {
			$eid = array ($eid);
		}

		// Get a database connector
		$db =& JFactory::getDBO();

		// Get a table object for the extension type
		$table = & JTable::getInstance($this->_type);

		// Enable the extension in the table and store it in the database
		foreach ($eid as $id)
		{
			$table->load($id);
			$table->enabled = '1';
			$result |= $table->store();
		}

		return $result;
	}

	/**
	 * Disable an extension
	 *
	 * @return boolean True on success
	 * @since 1.5
	 */
	function disable($eid=array())
	{
		// Initialize variables
		$result		= false;

		/*
		 * Ensure eid is an array of extension ids
		 * TODO: If it isn't an array do we want to set an error and fail?
		 */
		if (!is_array($eid)) {
			$eid = array ($eid);
		}

		// Get a database connector
		$db =& JFactory::getDBO();

		// Get a table object for the extension type
		$table = & JTable::getInstance($this->_type);

		// Disable the extension in the table and store it in the database
		foreach ($eid as $id)
		{
			$table->load($id);
			$table->enabled = '0';
			$result |= $table->store();
		}

		return $result;
	}

	function _loadItems()
	{
		global $mainframe, $option;

		jimport('joomla.filesystem.folder');

		/* Get a database connector */
		$db =& JFactory::getDBO();

		$query = 'SELECT *' .
				' FROM #__extensions' .
				' ORDER BY protected, name';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$numRows = count($rows);
		for($i=0;$i < $numRows; $i++)
		{
			$row =& $rows[$i];
			$data = unserialize($row->manifestcache);
			foreach($data as $key => $value) {
				$row->$key = $value;
			}
			$row->jname = JString::strtolower(str_replace(" ", "_", $row->name));
		}
		$this->setState('pagination.total', $numRows);
		if($this->_state->get('pagination.limit') > 0) {
			$this->_items = array_slice( $rows, $this->_state->get('pagination.offset'), $this->_state->get('pagination.limit') );
		} else {
			$this->_items = $rows;
		}
	}
	
	/**
	 * Remove (uninstall) an extension
	 *
	 * @static
	 * @param	array	An array of identifiers
	 * @return	boolean	True on success
	 * @since 1.0
	 */
	function remove($eid=array())
	{
		global $mainframe;

		// Initialize variables
		$failed = array ();

		/*
		 * Ensure eid is an array of extension ids in the form id => client_id
		 * TODO: If it isn't an array do we want to set an error and fail?
		 */
		if (!is_array($eid)) {
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db =& JFactory::getDBO();

		// Get an installer object for the extension type
		jimport('joomla.installer.installer');
		$installer = & JInstaller::getInstance();
		$row =& JTable::getInstance('extension');
		
		// Uninstall the chosen extensions
		foreach ($eid as $id)
		{
			$id		= trim( $id );
			$row->load($id);
			if($row->type) {
				$result	= $installer->uninstall($row->type, $id );
			

				// Build an array of extensions that failed to uninstall
				if ($result === false) {
					$failed[] = $id;
				}
			} else {
				$failed[] = $id;
			}
		}

		if (count($failed)) {
			// There was an error in uninstalling the package
			$msg = JText::sprintf('UNINSTALLEXT', JText::_($this->_type), JText::_('Error'));
			$result = false;
		} else {
			// Package uninstalled sucessfully
			$msg = JText::sprintf('UNINSTALLEXT', JText::_($this->_type), JText::_('Success'));
			$result = true;
		}

		$mainframe->enqueueMessage($msg);
		$this->setState('action', 'remove');
		$this->setState('name', $installer->get('name'));
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		return $result;
	}	
}