<?php
/**
* @version		$Id: sef.php 9764 2007-12-30 07:48:11Z ircmaxell $
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

/**
* Joomla! SEF Plugin
*
* @package 		Joomla
* @subpackage	System
*/
class plgSystemjacl extends JPlugin
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
     * Converting the site URL to fit to the HTTP request
     */
	function onAfterInitialise()
	{
		$config =& JFactory::getConfig();
		$config->setValue('config.aclservice', 'jacl');
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
class JACLjaclManager
{
	var $_rights = array();

	var $_ugroups = array();

	var $_cgroups = array();

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
	function authorize( $extension, $action, $contentitem = null,  $user)
	{
		if(is_null($contentitem)) {
			if(isset($this->_rights[$user][$extension][$action])) {
				return true;
			} else {
				return false;
			}
		} else {
			if(!is_array($this->_rights[$user][$extension][$action])) {
 				$this->_getAllowedContent($extension);
			} else {
				if(isset($this->_rights[$user][$extension][$action][$contentitem])) {
					return true;
				} else {
					return false;
				}
			}
		}		
	}

	function getAllowedContent($extension, $action)
	{
		$content = array();
		$user =& JFactory::getUser();
		if(!is_array($this->_rights[$user->get('id')][$extension][$action])) {
			$this->_getAllowedContent($extension, $action);
		}
		foreach($this->_rights[$user->get('id')][$extension][$action] as $name => $value)
		{
			$content[] = name;
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
		if(!count($this->_ugroups[$user]))
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



	function _getAllowedActions()
	{
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();

		$groups = $this->getUserGroups($user->get('id'));

		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
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
			$this->_rights[$user->get('id')][$result->extension][$result->action] = true;
		}
	}

	function _getAllowedContent($extension, $action)
	{
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();

		$groups = $this->getUserGroups($user->get('id'));

		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
		}

		$query = 'SELECT aco_map.section_value as extension, aco_map.value as action, axo_map.value as contentitem'
			.' FROM #__core_acl_aco_map aco_map'
			.' LEFT JOIN #__core_acl_acl acl ON aco_map.acl_id = acl.id'
			.' LEFT JOIN #__core_acl_aro_groups_map aro_group ON acl.id = aro_group.acl_id'
			.' LEFT JOIN #__core_acl_axo_map axo ON axo.acl_id = acl.id'
			.' LEFT JOIN #__core_acl_axo_groups_map axo_group ON acl.id = axo_group.acl_id'
			.' WHERE (aro_group.group_id IN ('.$groups.')) && (acl.allow = 1) && (axo.section_value = '.$extension.') && (aco_map.value = '.$action.')';
		$db->setQuery($query);
		$results = $db->loadObjectList();

		foreach($results as $result)
		{
			$this->_rights[$user->get('id')][$result->extension][$result->action][$result->contentitem] = true;
		}
	}


	//OLD FUNCTIONS (old as in "stuff that Hannes worked one before")

	/**
	 * Grabs all groups mapped to a user
	 *
	 * @param integer root-group-ID to start from/group ID to get informations from
	 * @param string The user whose group to return. If not provided, current user is used [optional]
	 * @return array
	 */
	function getUsergroups2( $root_group = 0, $user = 0 )
	{
		$db = JFactory::getDBO();
		jimport('joomla.user.models.usergroup');

		if ($root_group == 0) 
		{
			$root_group = $this->getUsergroupID('USERS');
		}

		$query = "SELECT lft, rgt FROM #__core_acl_aro_groups WHERE id = ". $root_group;
		$db->setQuery($query);
		$result = $db->loadAssoc();

		$query = "SELECT g.id as id, g.parent_id as parent_id, g.name as name FROM #__core_acl_aro_groups g ";

		if( $user !== 0 ) {
			$query .= 'LEFT JOIN #__core_acl_groups_aro_map m ON g.id = m.group_id
				LEFT JOIN #__core_acl_aro a ON m.aro_id = a.id 
				WHERE (a.value = '. $user .') AND (lft > '. $result['lft'] .') AND (rgt < '. $result['rgt'] .')';
		} else {
			$query .= "WHERE (lft > ". $result['lft'] .") AND (rgt < ". $result['rgt'] .")";
		}

		$db->setQuery($query);
		$groupresult = $db->loadAssocList();

		if($user == 0)
		{
			foreach($groupresult as $group) {
				if ($group['parent_id'] == $root_group) {
					$parent_id = 0;
				} else {
					$parent_id = $group['parent_id'];
				}
				$groups[$parent_id][$group['id']] = $group['name'];
			}
			$groups = $this->_getUsergroupsHelper( $groups, 0 );
		} else {
			$groups = $groupresult;
		}
		return $groups;
	}

	function _getUsergroupsHelper( $groups, $root_group ) {
		$groupresults = array();
		$db = JFactory::getDBO();
		if( isset( $groups[$root_group] )) {
			foreach( $groups[$root_group] as $id => $group) {
				$jusergroup = new JUsergroup();
				$jusergroup->id = $id;
				$jusergroup->name = $group;
				$jusergroup->parent = $root_group;
				$db->setQuery('SELECT a.value FROM #__core_acl_aro a 
							LEFT JOIN #__core_acl_groups_aro_map m ON a.id = m.aro_id
							WHERE m.group_id = '. $id);
				$users = $db->loadResultArray();
				$userobjects = array();
				if( isset($users) ) {
					foreach( $users as $user) {
						$userobjects[] = JUser::getInstance(intval($user));
					}
				}
				$jusergroup->users = $userobjects;
				$jusergroup->childGroups = $this->_getUsergroupsHelper( $groups, $id );
				$groupresults[] = $jusergroup;
			}
		}
		return $groupresults;
	}

	/**
	 * Returns a group ID given the groupname
	 *
	 * @param string Groupname
	 * @return integer Group-ID
	 */
	function getUsergroupID( $group ) {
		return $this->get_group_id( $group, 'ARO');
	}

	/**
	 * Returns extensive informations about the group
	 *
	 * @param integer Group ID
	 * @return array Groupinformations
	 */
	function getUsergroupData( $group ) {
		$db = JFactory::getDBO();
		jimport('joomla.user.models.usergroup');

		$query = 'SELECT name, value as id
				FROM #__core_acl_aro a 
				LEFT JOIN #__core_acl_groups_aro_map g ON g.aro_id = a.id 
				WHERE g.group_id = '. $group;
		$db->setQuery($query);
		$group_data = new JUsergroup();
		$users = $db->loadAssocList();
		if( isset($users) ) {
			foreach($users as $user) {
				$userobjects[] = JUser::getInstance(intval($user['id']));
			}
		}
		$group_data->users = $userobjects;
		$groupinfos = $this->get_group_data( $group, 'ARO' );
		$group_data->id = $groupinfos[0];
		$group_data->name = $groupinfos[3];
		$group_data->parent = $groupinfos[1];
		$group_data->childGroups = $this->getUserGroups( $group_data->id );

		return $group_data;
	}

	/**
	 * Returns all XObject-groups starting with the given root-group [optional]
	 *
	 * @param integer root-group-ID to start from
	 * @param boolean if set to TRUE, all groups are returned with extensive data
	 * @return array 
	 */
	function getXGroups( $root_group = 0, $data = false )
	{
		$db      = JFactory::getDBO();

		if ($root_group == 0) 
		{
			$root_group = $this->getXGroupID('ROOT');
		}

		$query = "SELECT lft, rgt FROM #__core_acl_axo_groups WHERE id = ". $root_group;
		$db->setQuery($query);
		$result = $db->loadAssoc();

		$query = "SELECT id, parent_id, name FROM #__core_acl_axo_groups 
				WHERE (lft > ". $result['lft'] .") AND (rgt < ". $result['rgt'] .") 
				ORDER BY parent_id, name";
		$db->setQuery($query);
		$groupresult = $db->loadAssocList();

		$groups = array();

		foreach($groupresult as $group) {
			if ($group['parent_id'] == $root_group) {
				$parent_id = 0;
			} else {
				$parent_id = $group['parent_id'];
			}
			if ($data) {
				$groups[$parent_id][$group['id']] = $this->getxgroupData($group['id']);
			} else {
				$groups[$parent_id][$group['id']] = $group['name'];
			}
		}
		return $groups;
	}

	/**
	 * Returns an Xgroup ID given the groupname
	 *
	 * @param string Groupname
	 * @return integer Group-ID
	 */
	function getXGroupID( $group ) {
		return $this->get_group_id( $group, 'AXO');
	}

	/**
	 * Returns extensive informations about the group
	 *
	 * @param integer Group ID
	 * @return array Groupinformations
	 */
	function getXGroupData( $group ) {
		$db = JFactory::getDBO();

		$query = 'SELECT name, value as id
				FROM #__core_acl_axo a 
				LEFT JOIN #__core_acl_groups_axo_map g ON g.axo_id = a.id 
				WHERE g.group_id = '. $group;
		$db->setQuery($query);
		$group_data['xobjects'] = $db->loadAssocList();

		$groupinfos = $this->get_group_data( $group, 'AXO' );
		$group_data['id'] = $groupinfos[0];
		$group_data['name'] = $groupinfos[3];
		$group_data['parent'] = $groupinfos[1];

		return $group_data;
	}

	/**
	 * Get all content items assigned to a user inside a specific group
	 */
	function getContentByUser( $extension, $action, $xobject, $xobjectextension, $group, $user)
	{
	
	}


	/**
	 * Adds a user group
	 *
	 * @param string Groups name
	 * @param string Parentgroup
	 * @param Old group-ID. Use for editing groups
	 */
	function addUserGroup( $name, $parent, $id = null )
	{
		if ($id) {
			$this->edit_group( $id, $name, $name, $parent, 'ARO');
		} else {
			$this->add_group( $name, $name, $parent, 'ARO');
		}
	}

	/**
	 * Adds an user to a group
	 *
	 * @param integer User-ID
	 * @param integer group-ID
	 */
	function addUser2Group( $user, $group )
	{
		return $this->add_group_object( $group, 'users', $user, 'ARO' );
	}

	/**
	 * Removes a user group
	 *
	 * @param integer group-ID
	 * @param boolean If set to TRUE, child-groups are reassigned
	 */
	function delusergroup( $group, $reparent_children = TRUE )
	{
		return $this->del_group($group, $reparent_children, 'ARO');
	}

	/**
	 * Removes a user from a group
	 *
	 * @param integer user-ID to be removed from the group
	 * @param integer group-ID
	 */
	function deluser2group( $user, $group )
	{
		return $this->del_group_object($group, 'users', $user, 'ARO');
	}

	/**
	 * Allows an object access for a user
	 *
	 * @param string user-ID
	 * @param string extension of the action
	 * @param string action
	 * @param string xobject [optional]
	 * @param string extension of the xobject If not provided, the extension of the action is used. [optional]
	 * @param string xobject group [optional]
	 */
	function allowuser( $user, $extension, $action, $xobject = null, $xobject_extension = null, $xobjectgroup = null )
	{
		$actions[$extension][] = $action;
		$users['users'][] = $user;
		if (!$xobject_extension) {
			$xobject_extension = $extension;
		}
		if ($xobject) {
			$xobjects[$xobject_extension][] = $xobject;
		}
		$xobjectgroups[] = $xobjectgroup;
		return $this->add_acl($actions, $users, NULL, $xobjects, $xobjectgroups, 1, 1, NULL, NULL, 'rules');
	}

	/**
	 * Denys an object for a user
	 *
	 * @param string user-ID
	 * @param string extension of the action
	 * @param string action
	 * @param string xobject [optional]
	 * @param string extension of the xobject If not provided, the extension of the action is used. [optional]
	 * @param string xobject group-ID [optional]
	 */
	function denyuser( $user, $extension, $action, $xobject = null, $xobject_extension = null, $xobjectgroup = null )
	{
		$actions[$extension][] = $action;
		$users['users'][] = $user;
		if (!$xobject_extension) {
			$xobject_extension = $extension;
		}
		if ($xobject) {
			$xobjects[$xobject_extension][] = $xobject;
		}
		$xobjectgroups[] = $xobjectgroup;
		return $this->add_acl($actions, $users, NULL, $xobjects, $xobjectgroups, 0, 1, NULL, NULL, 'rules');
	}

	/**
	 * Allows an object access for a group
	 *
	 * @param integer group-ID
	 * @param string extension of the action
	 * @param string action
	 * @param string xobject [optional]
	 * @param string extension of the xobject If not provided, the extension of the action is used. [optional]
	 * @param integer xobject group-ID [optional]
	 */
	function allowgroup( $group, $extension, $action, $xobject = null, $xobject_extension = null, $xobjectgroup = null )
	{
		$actions[$extension][] = $action;
		$groups[] = $group;
		if (!$xobject_extension) {
			$xobject_extension = $extension;
		}
		if ($xobject) {
			$xobjects[$xobject_extension][] = $xobject;
		}
		$xobjectgroups[] = $xobjectgroup;
		return $this->add_acl($actions, NULL, $groups, $xobjects, $xobjectgroups, 1, 1, NULL, NULL, 'rules');

	}

	/**
	 * Denys an object for a group
	 *
	 * @param integer group-ID
	 * @param string extension of the action
	 * @param string action
	 * @param string xobject [optional]
	 * @param string extension of the xobject If not provided, the extension of the action is used. [optional]
	 * @param integer xobject group-ID [optional]
	 */
	function denygroup( $group, $extension, $action, $xobject = null, $xobject_extension = null, $xobjectgroup = null )
	{
		$actions[$extension][] = $action;
		$groups[] = $group;
		if (!$xobject_extension) {
			$xobject_extension = $extension;
		}
		if ($xobject) {
			$xobjects[$xobject_extension][] = $xobject;
		}
		$xobjectgroups[] = $xobjectgroup;
		return $this->add_acl($actions, NULL, $groups, $xobjects, $xobjectgroups, 0, 1, NULL, NULL, 'rules');		
	}

	/**
	 * XObject Groups
	 */
	/**
	 * Adds an XObject group
	 *
	 * @param string Groups name
	 * @param integer Parentgroup-ID
	 * @param integer Old group-ID. Use for editing groups
	 */
	function addxgroup( $name, $parent, $oldgroup = null )
	{
		if ($oldname) {
			return $this->edit_group( $oldgroup, $name, $name, $parent, 'AXO');
		} else {
			return $this->add_group( $name, $name, $parent, 'AXO');
		}
	}

	/**
	 * Removes an xobject group
	 *
	 * @param integer group-ID
	 */
	function delxgroup( $group, $reparent_children = TRUE )
	{
		return $this->del_group($group, $reparent_children, 'AXO');
	}

	/**
	 * Adds an xobject to a group
	 *
	 * @param string extension of the action
	 * @param string xobject internal value
	 * @param integer group-ID
	 */
	function addxobject2group( $extension, $xobject, $group )
	{
		$this->add_group_object( $group, $extension, $xobject, 'AXO' );
	}

	/**
	 * Removes an xobject from a group
	 *
	 * @param string extension of the action
	 * @param string xobject internal value
	 * @param integer group-ID
	 */
	function delxobject2group( $extension, $xobject, $group )
	{
		$this->del_group_object($group, $extension, $xobject, 'AXO');
	}

	/**
	 * Users
	 */
	/**
	 * Returns user objects
	 *
	 * @param integer Group-ID the users should be member of [optional]
	 * @return array IDs and usernames of users
	 */
	function getUsers( $group = NULL)
	{
		$db = JFactory::getDBO();

		$query = "SELECT value FROM #__core_acl_aro";
		if( $group ) {
			$query .= ' a LEFT JOIN #__core_acl_groups_aro_map g 
					ON a.id = g.aro_id WHERE (g.group_id = '. $group .')';
		}
		$db->setQuery($query);
		$results = $db->loadResultArray();

		if( $results ) {	
			foreach( $results as $result ) {
				$users[] = JUser::getInstance(intval($result));
			}
		}
		return $users;
	}

	/**
	 * Returns users that are not assigned to a group
	 *
	 * @return array Username and user-ID
	 */
	function getUsersUngrouped()
	{
		$db = JFactory::getDBO();
		$query = "SELECT a.value FROM #__core_acl_aro a
				LEFT JOIN #__core_acl_groups_aro_map g ON a.id = g.aro_id
				WHERE g.group_id IS NULL";

		$db->setQuery($query);
		$results = $db->loadResultArray();
		if( $results ) {	
			foreach( $results as $result ) {
				$users[] = JUser::getInstance(intval($result));
			}
		}
		return $users;
	}

	/**
	 * Adds a user
	 * 
	 * @param string User-ID
	 * @param string user name
	 * @param string Old user-ID. Use for editing users
	 */
	function adduser( $userid, $name, $edit = false)
	{
		if ($edit == true) {
			$this->edit_object($this->get_object_id('users', $userid, 'ARO'), 'users', $name, $userid, 0, 0, 'ARO');
		} else {
			$this->add_object('users', $name, $userid, 0,0, 'ARO'); 
		}
	}

	/**
	 * Removes a user
	 *
	 * @param integer user-ID
	 */
	function deluser( $user )
	{
		$this->del_object($this->get_object_id('users', $user, 'ARO'),'ARO');
	}

	/**
	 * Actions
	 */
	/**
	 * Returns all actions
	 *
	 * @param string Extension the actions are part of. [optional]
	 * @return array Associated array of values
	 */
	function getactions( $extension = null )
	{
		$db = JFactory::getDBO();
		$query = 'SELECT section_value as extension, value, name
				FROM #__core_acl_aco';
		if($extension) {
			$query .= ' WHERE (section_value = '. $extension .')';
		} 
		$db->setQuery($query);
		return $db->loadAssocList();
	}

	/**
	 * Adds an action
	 *
	 * @param string actions internal name
	 * @param string action name
	 * @param string Extension the action should be added to
	 * @param string Old name. Use for editing actions
	 */
	function addaction( $value, $name, $extension, $oldname = null )
	{
		if ($oldname) {
			$this->edit_object($this->get_object_id($extension, $oldname, 'ACO'), $extension, $name, $value, 0, 0, 'ACO');
		} else {
			$this->add_object($extension, $name, $value, 0,0, 'ACO'); 
		}
	}

	/**
	 * Removes an action
	 *
	 * @param string Extension of the action
	 * @param string internal value of an action
	 */
	function delaction( $extension, $value )
	{
		return $this->del_object($this->get_object_id($extension, $value, 'ACO'),'ACO');
	}

	/**
	 * XObjects
	 */
	/**
	 * Returns XObjects
	 * 
	 * @param string Extension that the objects belong to.
	 * @param integer Group-ID the xobject belongs to.
	 * @return array Associated array of values
	 */
	function getxobjects( $extension = null, $group = NULL )
	{
		$db = JFactory::getDBO();

		$query = "SELECT section_value as extension, name, value FROM #__core_acl_axo";
		if( $group ) {
			$query .= ' a LEFT JOIN #__core_acl_groups_aro_map g 
					ON a.id = g.aro_id WHERE (g.group_id = '. $group .')';
		}
		if(( $extension ) AND ( $group )) {
			$query .= ' AND (section_value = '. $extension .')';
		} elseif ( $extension ) {
			$query .= ' WHERE (section_value = '. $extension .')';
		}
		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Adds an XObject
	 *
	 * @param string XObject internal name
	 * @param string xobject name
	 * @param string Extension the xobject should be added to
	 * @param string Old name. Use for editing xobjects
	 */
	function addxobject( $name, $value, $extension, $oldname = null )
	{
		if ($oldname) {
			$this->edit_object($this->get_object_id($extension, $oldname, 'AXO'), $extension, $name, $value, 0, 0, 'AXO');
		} else {
			$this->add_object($extension, $name, $value, 0,0, 'AXO'); 
		}
	}

	/**
	 * Removes an xobject
	 *
	 * @param string Extension of the xobject
	 * @param string Internal value of an xobject
	 */
	function delxobject( $extension, $value )
	{
		return $this->del_object($this->get_object_id($extension, $value, 'AXO'),'AXO');
	}

	/**
	 * Extensions
	 */
	/**
	 * Returns all usable extensions in an associated array
	 *
	 * @param string Internal name of an extension
	 * @return array
	 */
	function getextensions( $extension = '' )
	{
		$db = JFactory::getDBO();
		$query = 'SELECT value,name FROM #__core_acl_aco_sections';
		
		if( $extension ) {
			$query .= ' WHERE value = ' . $db->quote($extension);
		}
		$db->setQuery($query);
		$acos = $db->loadAssocList();

		$query = 'SELECT value,name FROM #__core_acl_axo_sections';
		
		if( $extension ) {
			$query .= ' WHERE value = ' . $db->quote($extension);
		}
		$db->setQuery($query);
		$axos = $db->loadAssocList();

		$result = array();
		$xobject = 0;

		foreach ($acos as $aco) {
			foreach ($axos as $axo) {
				if( $aco['value'] == $axo['value'] ) {
					$xobject = 1;
				}
			}
			$result[] = array( 'name'=>$aco['name'], 'value'=>$aco['value'], 'xobjects'=>$xobject);
			$xobject = 0;
		}

		return $result;
	}

	/**
	 * Adds an extension to the system. In phpGACL = sections
	 * 
	 * @param string Extensions internal name
	 * @param string Extension name
	 * @param boolean Uses xobjects?
	 * @param string Old internal value. Use for editing extensions
	 * @return boolean
	 */
	function addextension( $name, $value, $xobjects = 0, $oldname = null )
	{
		if ($oldname) {
			$this->edit_object_section($this->get_object_section_section_id(null, $oldname, 'ACO'), $name, $value, 0, 0, 'ACO');
			if ($this->get_object_section_section_id(null, $oldname, 'AXO')) {
				$this->edit_object_section($this->get_object_section_section_id(null, $oldname, 'AXO'), $name, $value, 0, 0, 'AXO');
			}
		} else {
			$this->add_object_section($name, $value, 0, 0, 'ACO'); 
			if ($xobjects) {
				$this->add_object_section($name, $value, 0,0, 'AXO'); 
			}
		}
		return true;
	}

	/**
	 * Removes an extension
	 * also removes all objects that belong to the extension
	 *
	 * @param string Name of the extension
	 * @param string internal value of the extension
	 */
	function delextension( $name, $value )
	{
		if ($this->get_object_section_section_id($name, $value, 'AXO')) {
			$this->del_object_section($this->get_object_section_section_id($name, $value, 'AXO'), 'AXO', TRUE );
		}
		return $this->del_object_section($this->get_object_section_section_id($name, $value, 'ACO'), 'ACO', TRUE );
	}

	//Function to override basic phpgacl function. This function caches a lot of queries and reduces their amount.
/**	function acl_check($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL, $root_aro_group=NULL, $root_axo_group=NULL)
	{
		static $rights;
		if(!isset($rights[$aco_section_value][$aco_value][$aro_section_value][$aro_value][$axo_section_value][$axo_value][$root_aro_group][$root_axo_group]))
		{
			$rights[$aco_section_value][$aco_value][$aro_section_value][$aro_value][$axo_section_value][$axo_value][$root_aro_group][$root_axo_group] = parent::acl_check($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value, $axo_value, $root_aro_group, $root_axo_group);
		}
var_dump($rights);
		return $rights[$aco_section_value][$aco_value][$aro_section_value][$aro_value][$axo_section_value][$axo_value][$root_aro_group][$root_axo_group];
	}
**/
	function acl_get_groups($section_value, $value, $root_group=NULL, $group_type='ARO') {
	{
		static $groups;
		if(!isset($groups[$group_type][$section_value][$value]))
			$groups[$group_type][$section_value][$value] = parent::acl_get_groups($section_value, $value, $root_group, $group_type);
		}

		return $groups[$group_type][$section_value][$value];
	}

	function getAllowedActions($extension = 0, $action = 0, $user = 0)
	{
		$db =& JFactory::getDBO();
		if(!$extension && !$action)
		{
			return false;
		}

		if($user == 0)
		{
			$user = JFactory::getUser();
			$user = $user->get('id');
		}

		$groups = $this->acl_get_groups('users', $user);
		if (is_array($groups) AND !empty($groups)) {
			$groups = implode(',', $groups);
		}

		$query = 'SELECT aco_map.section_value as extension, aco_map.value as action FROM #__core_acl_aco_map aco_map'
				.' LEFT JOIN #__core_acl_acl acl ON aco_map.acl_id = acl.id'
				.' LEFT JOIN #__core_acl_aro_groups_map aro_group ON acl.id = aro_group.acl_id'
				.' WHERE (aro_group.group_id IN ('.$groups.')) && (acl.allow = 1)';

		if($extension)
		{
			$query .= ' && (aco_map.section_value = '.$db->Quote($extension).')';
		}

		if($action)
		{
			$query .= ' && (aco_map.value = '.$db->Quote($action).')';
		}

		$db->setQuery($query);
		$results = $db->loadObjectList('extension');
	
		foreach($results as $result)
		{
			$this->_rights[$user][$result->extension][$result->action] = true;
		}

		return $results;
	}


}