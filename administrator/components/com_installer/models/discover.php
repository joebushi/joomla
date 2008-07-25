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
jimport('joomla.installer.installer');

/**
 * Installer Manage Model
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerModelDiscover extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'discover';
	
	/**
	 * Current extension list
	 */

	function _loadItems()
	{
		global $mainframe, $option;

		jimport('joomla.filesystem.folder');

		/* Get a database connector */
		$db =& JFactory::getDBO();

		$query = 'SELECT *' .
				' FROM #__extensions' .
				' WHERE state = -1' .
				' ORDER BY type, name';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$numRows = count($rows);
		for($i=0;$i < $numRows; $i++)
		{
			$row =& $rows[$i];
			if(strlen($row->manifestcache)) {
				$data = unserialize($row->manifestcache);
				if($data) {
					foreach($data as $key => $value) {
						$row->$key = $value;
					}	
				}
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
	
	function discover() {
		$installer =& JInstaller::getInstance();
		$results = $installer->discover();
		foreach($results as $result) {
		//	$result->store(); // put it into the table
		}
		print_r($results);
	}
}