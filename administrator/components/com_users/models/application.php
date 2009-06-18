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
class UsersModelApplication extends JModelItem
{
	function getItems()
	{
		$db =& JFactory::getDBO();
		$xml = JFactory::getXMLParser('simple');
		$xml->loadFile(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'models'.DS.'access.xml');
		$root = $xml->document;
		$query = 'SELECT * FROM #__access_sections WHERE name != \'core\'';
		$db->setQuery($query);
		$sections = $db->loadObjectList();
		foreach($sections as $section)
		{
			$attribs = array();
			$attribs['value'] = $section->name.'.manage';
			$attribs['name'] = ucfirst($section->name.'_manage');
			$attribs['description'] = strtoupper('DESC_MANAGE_'.$section->name);
			$root->addchild('action', $attribs);
			$attribs = array();
			$attribs['value'] = $section->name.'.aclmanage';
			$attribs['name'] = ucfirst($section->name.'_aclmanage');
			$attribs['description'] = strtoupper('DESC_ACLMANAGE_'.$section->name);
			$root->addchild('action', $attribs);
		}
		return $root;
	}
	function getUsergroups()
	{
		$db =&JFactory::getDBO();
		$query = 'SELECT * FROM #__usergroups ORDER BY left_id';
		$db->setQuery($query);
		$groups = $db->loadObjectList();

		return $groups;
	}
}