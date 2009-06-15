<?php
/**
 * @version		$Id: group.php 11952 2009-06-01 03:21:19Z robs $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * User Application model for Users.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class UsersModelAccess extends JModelItem
{
	function getItems()
	{
		$xml = JFactory::getXMLParser('simple');
		
		
		$xml->loadFile(JPATH_ADMINISTRATOR.DS.'components'.DS.JRequest::getVar('component').DS.'access.xml');
		return $xml->document;
	}	
}