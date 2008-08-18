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
	 * @param int Extension Identifier; if zero use all sites
	 * @return boolean If there are updates or not
	 */
	function findUpdates($eid=0) {
		$dbo =& $this->getDBO();
		$retval = false;
		// push it into an array
		if(!is_array($eid)) {
			$query = 'SELECT DISTINCT updatesiteid, type, location FROM #__update_sites WHERE enabled = 1';
		} else {
			$query = 'SELECT DISTINCT updatesiteid, type, location FROM #__update_sites WHERE updatesiteid IN (SELECT updatesiteid FROM #__update_sites_extensions WHERE extensionid IN ('. implode(',', $eid) .'))';
		}
		$dbo->setQuery($query);
		$results = $dbo->loadAssocList();
		$result_count = count($results);
		for($i = 0; $i < $result_count; $i++) {
			$result =& $results[$i];
			$this->setAdapter($result['type']);
			$update_result = $this->_adapters[$result['type']]->findUpdate($result);
			if(is_array($update_result)) {
				$results = $this->arrayUnique(array_merge($results, $update_result));
				$result_count = count($results);
				$update_result = true;
			} else if ($retval) {
				$update_result = true;
			}
		}
		return $retval;
	}
	
	/**
	 * Multidimensional array safe unique test
	 * Borrowed from PHP.net
	 * @url http://au2.php.net/manual/en/function.array-unique.php
	 */
	function arrayUnique($myArray)
	{
	    if(!is_array($myArray))
	           return $myArray;
	
	    foreach ($myArray as &$myvalue){
	        $myvalue=serialize($myvalue);
	    }
	
	    $myArray=array_unique($myArray);
	
	    foreach ($myArray as &$myvalue){
	        $myvalue=unserialize($myvalue);
	    }
	
	    return $myArray;
	
	} 	
}