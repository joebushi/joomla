<?php
/**
* @version		$Id$
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');
jimport( 'joomla.user.authorization');
JLoader::register('JAuthorization', JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'user'.DS.'authorization.php');

/**
* Joomla! SEF Plugin
*
* @package 		Joomla
* @subpackage	System
*/
class plgSystemJACL extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object		$subject The object to observe
	  * @param 	array  		$config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemJacl(&$subject, $config)  {
		parent::__construct($subject, $config);
	}

	/**
	* Registering the active ACL solution
	*/
	function onAfterInitialise()
	{
		$config =& JFactory::getConfig();
		$config->setValue('config.aclservice', 'JACL');
		return true;
	}
}

/**
 * Class that handles all access authorization
 *
 * @package 	Joomla.Framework
 * @subpackage	Application
 * @since		1.5
 */
class JAuthorizationJACL extends JAuthorization
{
	var $_rights = array();

	var $_ugroups = array();

	/**
	 * Constructor
	 * @param array An arry of options to oeverride the class defaults
	 */
	function __construct( $options = NULL ) 
	{
		if(!count($this->_rights))
		{
			$this->_getAllowedActions();
		}
	}

	/**
	 * Hands the access query over to the actual ACL engine
	 *
	 * This is the function that is used for the access check
	 * @param string The extension the action belongs to [optional]
	 * @param string The action to check for [optional]
	 * @param string The extension that the XObject belongs to [optional]
	 * @param string The XObject [optional]
	 * @param string The user to check for. If not provided, the current user is used [optional]
	 * @return boolean
	 */
	function authorize( $extension, $action, $contentitem = null, $user = null)
	{
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}

		if(is_null($contentitem)) {
			if(isset($this->_rights[$user][$extension][$action])) {
				return true;
			} else {
				return false;
			}
		} else {
			if(!is_array($this->_rights[$user][$extension][$action])) {
 				$this->_getAllowedContent($extension, $action);
			} else {
				if(isset($this->_rights[$user][$extension][$action][$contentitem])) {
					return true;
				} else {
					return false;
				}
			}
		}		
	}

	function getAllowedContent($extension, $action, $user = null)
	{
		$content = array();
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}
		if(!is_array($this->_rights[$user][$extension][$action])) {
			$this->_getAllowedContent($extension, $action, $user);
		}
		if(count($this->_rights[$user][$extension][$action])) {
			foreach($this->_rights[$user][$extension][$action] as $name => $value)
			{
				$content[] = $value;
			}
		}
		return $content;
	}

	/**
	 * Grabs all groups mapped to a user
	 *
	 * @param integer root-group-ID to start from/group ID to get informations from
	 * @param string The user whose group to return. If not provided, current user is used [optional]
	 * @return array
	 */
	function getUserGroups( $user )
	{
		if(is_object($user)) {
			$user = $user->get('id');
		}

		if(!isset($this->_ugroups[$user]))
		{
			$db = JFactory::getDBO();
			$query = 'SELECT DISTINCT g2.id'
					.' FROM #__core_acl_aro o,'
					.' #__core_acl_groups_aro_map gm,'
					.' #__core_acl_aro_groups g1,'
					.' #__core_acl_aro_groups g2'
					.' WHERE (o.section_value=\'users\' AND o.value=\''.$user.'\')'
					.' AND gm.aro_id=o.id AND g1.id=gm.group_id AND (g2.lft <= g1.lft AND g2.rgt >= g1.rgt)';
			$db->setQuery($query);
			$this->_ugroups[$user] = $db->loadResultArray();
		}

		return $this->_ugroups[$user];
	}



	function _getAllowedActions($user = null)
	{
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}
		$db =& JFactory::getDBO();
		$groups = $this->getUserGroups($user);
	
		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
		} else {
			$groups = array('2');
		}

		$query = 'SELECT aco_map.section_value as extension, aco_map.value as action FROM #__core_acl_aco_map aco_map'
				.' LEFT JOIN #__core_acl_acl acl ON aco_map.acl_id = acl.id'
				.' LEFT JOIN #__core_acl_aro_groups_map aro_group ON acl.id = aro_group.acl_id'
				.' LEFT JOIN #__core_acl_axo_map axo ON axo.acl_id = acl.id'
				.' LEFT JOIN #__core_acl_axo_groups_map axo_group ON acl.id = axo_group.acl_id'
				.' WHERE (aro_group.group_id IN ('.$groups.')) && (acl.allow = 1) && (axo.section_value IS NULL AND axo.value IS NULL)';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		foreach($results as $result)
		{
			$this->_rights[$user][$result->extension][$result->action] = true;
		}
	}

	function _getAllowedContent($extension, $action, $user = null)
	{
		if($user == null) {
			$user = JFactory::getUser();
			$user = $user->get('id');
		}
		$db =& JFactory::getDBO();
		$groups = $this->getUserGroups($user);

		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
		} else {
			$groups = array('2');
		}

		$query = 'SELECT aco_map.section_value as extension, aco_map.value as action, axo.value as contentitem'
			.' FROM #__core_acl_aco_map aco_map'
			.' LEFT JOIN #__core_acl_acl acl ON aco_map.acl_id = acl.id'
			.' LEFT JOIN #__core_acl_aro_groups_map aro_group ON acl.id = aro_group.acl_id'
			.' LEFT JOIN #__core_acl_axo_map axo ON axo.acl_id = acl.id'
			.' LEFT JOIN #__core_acl_axo_groups_map axo_group ON acl.id = axo_group.acl_id'
			.' WHERE (aro_group.group_id IN ('.$groups.')) && (acl.allow = 1) && (axo.section_value = \''.$extension.'\') && (aco_map.value = \''.$action.'\')';
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if(count($results))
		{
			foreach($results as $result)
			{
				$this->_rights[$user][$result->extension][$result->action][$result->contentitem] = true;
			}
		} else {
			$this->_rights[$user][$extension][$action] = array();
		}
	}
}

class JAuthorizationJACLUsergroup
{
	function __construct()
	{

	}

	function getParent()
	{

	}

	function getChildren()
	{

	}

	function addChild()
	{

	}

	function setName()
	{

	}

	function getName()
	{

	}

	function getID()
	{

	}

	function setID()
	{

	}

	function removeChild()
	{

	}

	function load($group = null)
	{
		if(!count($this->_groups))
		{
			$db =&JFactory::getDBO();
			$query = 'SELECT g.id, g.parent_id as parent, g.name, COALESCE(gm.users, 0) AS userscount'
					.' FROM #__core_acl_aro_groups AS g'
					.' LEFT JOIN (SELECT group_id, COUNT(*) AS users'
					.' FROM #__core_acl_groups_aro_map'
					.' GROUP BY group_id) AS gm ON g.id = gm.group_id'
					.' ORDER BY g.parent_id, g.name;';
			$db->setQuery($query);
			$this->_groups = $db->loadObjectList('id');

			foreach($this->_groups as &$group)
			{
				$this->_groups[$group->parent_id]->children[] = &$group;
			}
		}

		if(is_int($group))
		{
			$this->_id = &$this->_groups[$group]->id;
			$this->_parent = &$this->_groups[$group]->parent;
			$this->_name = &$this->_groups[$group]->name;
			$this->_userscount = &$this->_groups[$group]->userscount;
			$this->_children = &$this->_groups[$group]->children;
		}
	}

	function store()
	{
		if($this->_id != 0)
		{
			$db =& JFactory::getDBO();
			$query = 'UPDATE #__core_acl_aro_groups'
					.' SET parent_id = '.$this->_parent.','
					.' name = \''.$this->_name.'\','
					.' value = \''.$this->_name.'\''
					.' WHERE id = '.$this->_id;
			$db->setQuery($query);
			$db->Query();
		} else {
			$db =& JFactory::getDBO();
			$query = 'INSERT INTO #__core_acl_aro_groups'
					.' (parent_id, name, value)'
					.' VALUES ('.$this->_parent.',\''.$this->_name.'\',\''.$this->_name.'\');';
			$db->setQuery($query);
			$db->Query();
		}
		return true;
	}

	function remove()
	{

	}

	function getUsers()
	{

	}

	function addUser()
	{

	}

	function removeUser()
	{

	}

	function getUsergroups()
	{

	}
}

class JAuthorizationJACLRule
{
	function __construct()
	{

	}

	function load($group = null)
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getRules()
	{

	}

	function getGroups()
	{
		
	}

	function addGroup()
	{

	}

	function removeGroup()
	{

	}

	function getActions()
	{
		
	}

	function addAction()
	{

	}

	function removeAction()
	{

	}

	function getContentItems()
	{

	}

	function addContentItem()
	{

	}

	function removeContentItem()
	{

	}


	function getID()
	{

	}

	function setID()
	{

	}

	function allow()
	{

	}
}

class JAuthorizationJACLAction
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getExtension()
	{
		
	}

	function setExtension()
	{

	}

	function getName()
	{

	}

	function setName()
	{

	}

	function getValue()
	{

	}

	function setValue()
	{

	}

	function getActions()
	{

	}
}

class JAuthorizationJACLContentItem
{
	function __construct()
	{

	}

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getExtension()
	{
		
	}

	function setExtension()
	{

	}

	function getName()
	{

	}

	function setName()
	{

	}

	function getValue()
	{

	}

	function setValue()
	{

	}

	function getContentItems()
	{

	}
}

class JAuthorizationJACLUser
{
	function __construct()
	{

	}

	function load($id = null)
	{
		if(!is_array($this->_users))
		{
			$db =& JFactory::getDBO();
			$query = 'SELECT id, value, name FROM #__core_acl_aro';
			$db->setQuery($query);
			$this->_users = $db->loadObjectList('value');
		}

		if(is_int($id))
		{
			$this->_id = $this->_users[$id]->id;
			$this->_value = $this->_users[$id]->value;
			$this->_name = $this->_users[$id]->name;
			return true;
		}
	}

	function store()
	{
		if(!is_null($this->_id))
		{
			$db =& JFactory::getDBO();
			$query = 'UPDATE #__core_acl_aro'
					.' SET name = \''.$this->_name.'\','
					.' value = \''.$this->_value.'\''
					.' WHERE id = '.$this->_id;
			$db->setQuery($query);
			$db->Query();
		} else {
			$db =& JFactory::getDBO();
			$query = 'INSERT INTO #__core_acl_aco'
					.' (name, value)'
					.' VALUES (\''.$this->_name.'\',\''.$this->_value.'\');';
			$db->setQuery($query);
			$db->Query();
			$this->_id = $db->inserid();
		}
		return true;		
	}

	function remove()
	{
		$db =& JFactory::getDBO();
		$query = 'DELETE FROM #__core_acl_aro WHERE id = '.$this->_id;
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_groups_aro_map WHERE aro_id = '.$this->_id;
		$db->setQuery($query);
		$db->Query();
	}

	function getName()
	{
		return $this->_name;
	}

	function setName($name)
	{
		$this->_name = $name;
		return true;
	}

	function getUserID()
	{
		return $this->_value;
	}

	function setUserID($id)
	{
		$this->_value = $id;
		return true;
	}

	function getUsers()
	{
		$this->_load();
		return $this->_users;
	}
}

class JAuthorizationJACLExtension
{
	function __construct()
	{

	}

	function load($extension)
	{
		if(is_null($this->_extensions))
		{
			$db =& JFactory::getDBO();
			$query = 'SELECT id, value, name FROM #__core_acl_aco_sections';
			$db->setQuery($query);
			$this->_extensions = $db->loadObjectList('value');
		}
		if(isset($this->_extensions[$extension]))
		{
			$this->_id = $this->_extensions[$extension]->id;
			$this->_name = $this->_extensions[$extension]->name;
			$this->_value = $this->_extensions[$extension]->value;
			return true;
		}
		return false;
	}

	function store()
	{
		if(!is_null($this->_id))
		{
			$db =& JFactory::getDBO();
			$query = 'UPDATE #__core_acl_aco_sections'
					.' SET name = \''.$this->_name.'\','
					.' value = \''.$this->_value.'\''
					.' WHERE id = '.$this->_id;
			$db->setQuery($query);
			$db->Query();
			$query = 'UPDATE #__core_acl_axo_sections'
					.' SET name = \''.$this->_name.'\','
					.' value = \''.$this->_value.'\''
					.' WHERE id = '.$this->_id;
			$db->setQuery($query);
			$db->Query();
		} else {
			$db =& JFactory::getDBO();
			$query = 'INSERT INTO #__core_acl_aco_sections'
					.' (name, value)'
					.' VALUES (\''.$this->_name.'\',\''.$this->_value.'\');';
			$db->setQuery($query);
			$db->Query();
			$db =& JFactory::getDBO();
			$query = 'INSERT INTO #__core_acl_axo_sections'
					.' (name, value)'
					.' VALUES (\''.$this->_name.'\',\''.$this->_value.'\');';
			$db->setQuery($query);
			$db->Query();
		}
		return true;		
	}

	function remove()
	{
		$db =&JFactory::getDBO();
		$query = 'DELETE FROM #__core_acl_aco_sections WHERE id = '.$this->_id;
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_axo_sections WHERE id = '.$this->_id;
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_aco WHERE section_value = \''.$this->_value.'\'';
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_axo WHERE section_value = \''.$this->_value.'\'';
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_aco_map WHERE section_value = \''.$this->_value.'\'';
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_axo_map WHERE section_value = \''.$this->_value.'\'';
		$db->setQuery($query);
		$db->Query();
		return true;
	}

	function getName()
	{
		return $this->_name;
	}

	function setName($name)
	{
		this->_name = $name;
		return true;
	}

	function getValue()
	{
		return $this->_value;
	}

	function setValue($value)
	{
		$this->_value = $value;
		return true;
	}

	function getExtensions()
	{
		$this->load();
		return $this->_extensions;
	}
}