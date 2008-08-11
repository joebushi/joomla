<?php
/**
 * Joomla! Update System
 *
 * @version		$Id: installer.php 10609 2008-08-01 06:26:01Z pasamio $
 * @package		Joomla.Framework
 * @subpackage	Updater
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');
jimport('joomla.base.adapter');
 
/**
 * Updater Class
 * @since 1.6
 */
class JUpdater extends JAdapter {
	
	/**
	 * Constructor
	 */
	function __construct() {
		// adapter base path, class prefix
		parent::__construct(dirname(__FILE__),'JUpdater');
	}
	
	/**
	 * Returns a reference to the global Installer object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @return	object	An installer object
	 */
	function &getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new JUpdater();
		}
		return $instance;
	}	
	
	/**
	 * Finds an update for an extension
	 * @param int Extension Identifier; if zero use "global" site not an extension one
	 * @return boolean If there are updates or not
	 */
	function findUpdates($eid=0) {
		$dbo =& $this->getDBO();
		$result = false;
		// push it into an array
		if(!is_array($eid)) {
			$query = 'SELECT type,location FROM #__update_sites WHERE enabled = 1';
		} else {
			$query = 'SELECT type,location FROM #__update_sites WHERE updatesiteid IN (SELECT updatesiteid FROM #__update_sites_extensions WHERE extensionid IN ('. implode(',', $eid) .'))';
		}
		$dbo->setQuery($query);
		$results = $dbo->loadAssocList();
		foreach($results as $result) {
			$this->setAdapter($result['type']);
			$result = $this->_adapters[$result['type']]->findUpdate($result['location']) ? true : $result;
		}
		return $result;
	}
	
}