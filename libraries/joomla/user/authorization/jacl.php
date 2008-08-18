<?php
/**
* @version		$Id: jacl.php 10545 2008-07-13 14:37:09Z hackwar $
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

jimport( 'joomla.user.authorization');

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
			if($user == 0)
			{
				$user = 1;
			}
		}

		if(is_null($contentitem)) {
			if(isset($this->_rights[$user][$extension][$action])) {
				return true;
			} else {
				return false;
			}
		} else {
			if(!isset($this->_rights[$user]) || isset($this->_rights[$user][$extension][$action]) && !is_array($this->_rights[$user][$extension][$action])) {
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
			if($user == 0)
			{
				$user = 1;
			}
		}
		if(!isset($this->_rights[$user][$extension][$action]) || !is_array($this->_rights[$user][$extension][$action])) {
			$this->_getAllowedContent($extension, $action, $user);
		}
		if(isset($this->_rights[$user]) && count($this->_rights[$user][$extension][$action])) {
			foreach($this->_rights[$user][$extension][$action] as $name => $value)
			{
				$content[] = $name;
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
		if($user == 0)
		{
			$user = 1;
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

class JAuthorizationJACLUsergroupHelper extends JAuthorizationUsergroupHelper
{
	var $_groups = array();
	
	var $_root = 0;
		
	function __construct()
	{
		if(!count($this->_groups))
		{
			$db =&JFactory::getDBO();
			$query = 'SELECT g.id, g.parent_id as parent, g.name'
					.' FROM #__core_acl_aro_groups AS g'
					.' LEFT JOIN (SELECT group_id, COUNT(*) AS users'
					.' FROM #__core_acl_groups_aro_map'
					.' GROUP BY group_id) AS gm ON g.id = gm.group_id'
					.' ORDER BY g.parent_id, g.name;';
			$db->setQuery($query);
			$this->_groups = $db->loadObjectList('id');

			foreach($this->_groups as &$group)
			{
				$group->users = array();
				if(!isset($group->children))
				{
					$group->children = array();
				}
				if($group->parent != 0)
				{
					$this->_groups[$group->parent]->children[] = $group->id;
				} else {
					$this->_root = $group->id;
				}
			}
			$query = 'SELECT * FROM #__core_acl_groups_aro_map';
			$db->setQuery($query);
			$results = $db->loadObjectList();
			foreach($results as $result)
			{
				$this->_groups[$result->group_id]->users[] = $result->aro_id;
			}
		}		
	}
		
	function getGroup($id)
	{
		if($id > 0)
		{
			if(isset($this->_groups[$id]))
			{
				return $this->_groups[$id];
			} else {
				return false;
			}
		} else {
			return $this->_groups[$this->_root];
		}
	}

	function addGroup($group)
	{
		if(is_a($group, 'JAuthorizationUsergroup'))
		{
			if($group->getId() != 0)
			{
				$db =& JFactory::getDBO();
				$parent = $group->getParent();
				$query = 'UPDATE #__core_acl_aro_groups'
					.' SET (parent_id = '.(int) $parent->getId().','
					.' name = \''.$db->Quote($group->getName()).'\','
					.' value = \''.$db->Quote($group->getName()).'\')'
					.' WHERE id = '.(int)$group->getId();
				$db->setQuery($query);
				$db->Query();
			} else {
				$db =& JFactory::getDBO();
				$parent = $group->getParent();
				$query = 'INSERT INTO #__core_acl_aro_groups'
					.' (parent_id, name, value)'
					.' VALUES ('.(int) $parent->getId().',\''.$db->Quote($group->getName()).'\',\''.$db->Quote($group->getName).'\');';
				$db->setQuery($query);
				$db->Query();
				$group->setId($db->insertid());
				if(count($group->getUser()))
				{
					foreach($group->getUser() as $user)
					{
						$this->addUser($group->getId(), $user);
					}
				}
				
				if(count($group->getChildren()))
				{
					foreach($group->getChildren() as $group)
					{
						$this->addGroup($group);
					}
				}
			}
		} else {
			return false;
		}
		return true;
	}

	function removeGroup($group)
	{
		if(is_a($group, 'JAuthorizationUsergroup'))
		{
			$db =& JFactory::getDBO();
			if(count($group->getChildren()))
			{
				$parent = $group->getParent();
				$query = 'UPDATE #__core_acl_aro_groups SET parent_id = '.$parent->getId().' WHERE parent_id = '.$group->getId();
				$db->setQuery($query);
				$db->Query();
			}
			if(count($group->getUsers()))
			{
				foreach($group->getUsers() as $user)
				{
					$this->removeUser($group->getId(), $user);
				}
			}
			$query = 'DELETE FROM #__core_acl_aro_groups_map WHERE group_id = '.(int) $group->getId();
			$db->setQuery($query);
			$db->Query();
			$query = 'DELETE FROM #__core_acl_aro_groups WHERE id = '.(int) $group->getId();
			$db->setQuery($query);
			$db->Query();
			return true;
		}
		return false;
	}
	
	function addUser($id, $user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			$db =& JFactory::getDBO();
			$query = 'INSERT INTO #__core_acl_groups_aro_map (group_id, aro_id) VALUES ('.(int) $id.','.(int)$user->getAccessId().');';
			$db->setQuery($query);
			$db->Query();
			return $db->insertid();
		}
		return false;
	}
	
	function removeUser($id, $user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			$db =& JFactory::getDBO();
			$query = 'DELETE FROM #__core_acl_groups_aro_map WHERE group_id = '.(int) $id.' AND aro_id = '.(int)$user->getAccessId().';';
			$db->setQuery($query);
			$db->Query();
			return true;
		}
		return false;
	}
	
	function getUngroupedUser()
	{
		$db =& JFactory::getUser();
		$query = 'SELECT value FROM #__core_acl_aro WHERE id NOT IN (SELECT aro_id FROM #__core_acl_groups_aro_map)';
		$db->setQuery($query);
		$result = $db->loadResultList();
		if($result)
		{
			$users = array();
			foreach($result as $temp_user)
			{
				$users[] = new JAuthorizationUser($temp_user);
			}
			return $users;
		}
		return false;
	}
}

class JAuthorizationJACLRule extends JAuthorizationRule
{
	function __construct()
	{

	}
	
	function authorizeGroup($group, $extension, $action, $contentitem = null)
	{
		
	}

	function addRule($allow = true, $group, $action, $contentitem = null)
	{
		if(!is_a($group, 'JAuthorizationUsergroup') || !is_a($action, 'JAuthorizationAction'))
		{
			return false;
		}
		
		if($contentitem != null && !is_a($contentitem, 'JAuthorizationContentItem'))
		{
			return false;
		}
		
		$db =& JFactory::getDBO();
		if($contentitem == null)
		{
			$query = 'SELECT acl.id FROM #__core_acl_acl acl'
				.' LEFT JOIN #__core_acl_aro_groups_map arogm ON acl.id = arogm.acl_id'
				.' WHERE acl.section_value = \'actionrule\' AND arogm.group_id = '.$group->getId().' AND acl.allow = '.($allow ? '1':'0').';';
			$db->setQuery($query);
			$acl_id = $db->loadResult();
			if($acl_id == null)
			{
				$query = 'INSERT INTO #__core_acl_acl (section_value, allow, enabled, updated_date)'
					.' VALUES (\'actionrule\', '.($allow ? '1':'0').', 1, '.time().');';
				$db->setQuery($query);
				$db->Query();
				$acl_id = $db->insertid();
				$query = 'INSERT INTO #__core_acl_aro_groups_map (acl_id, group_id) VALUES ('.$acl_id.','.$group->getId().');';
				$db->setQuery($query);
				$db->Query();
			}
			$query = 'INSERT INTO #__core_acl_aco_map (acl_id, section_value, value) VALUES ('.$acl_id.',\''.$action->getExtension().'\',\''.$action->getAction().'\');';
			$db->setQuery($query);
			$db->Query();
			return true;
		} else {
			$query = 'SELECT acl.id FROM #__core_acl_acl acl'
				.' LEFT JOIN #__core_acl_aro_groups_map arogm ON acl.id = arogm.acl_id'
				.' LEFT JOIN #__core_acl_aco_map acom ON acl.id = acom.acl_id'
				.' WHERE acl.section_value = \'contentrule\' AND arogm.group_id = '.$group->getId().' AND acl.allow = '.($allow ? '1':'0')
				.' AND acom.section_value = '.$db->Quote($action->getExtension()).' AND acom.value = '.$db->Quote($action->getAction()).';';
			$db->setQuery($query);
			$acl_id = $db->loadResult();
			if($acl_id == null)
			{
				$query = 'INSERT INTO #__core_acl_acl (section_value, allow, enabled, updated_date)'
					.' VALUES (\'actionrule\', '.($allow ? '1':'0').', 1, '.time().');';
				$db->setQuery($query);
				$db->Query();
				$acl_id = $db->insertid();
				$query = 'INSERT INTO #__core_acl_aro_groups_map (acl_id, group_id) VALUES ('.$acl_id.','.$group->getId().');';
				$db->setQuery($query);
				$db->Query();
				$query = 'INSERT INTO #__core_acl_aco_map (acl_id, section_value, value) VALUES ('.$acl_id.',\''.$action->getExtension().'\',\''.$action->getAction().'\');';
				$db->setQuery($query);
				$db->Query();
			}
			$query = 'INSERT INTO #__core_acl_axo_map (acl_id, section_value, value) VALUES ('.$acl_id.',\''.$contentitem->getExtension().'\',\''.$contentitem->getContentItem().'\');';
			$db->setQuery($query);
			$db->Query();
			return true;
		}
	}
	
	function removeRule($allow = true, $group, $action, $contentitem = null)
	{
		if(!is_a($group, 'JAuthorizationUsergroup') || !is_a($action, 'JAuthorizationAction'))
		{
			return false;
		}
		
		if($contentitem != null && !is_a($contentitem, 'JAuthorizationContentItem'))
		{
			return false;
		}
		
		$db =& JFactory::getDBO();
		if($contentitem == null)
		{
			$query = 'SELECT acl.id FROM #__core_acl_acl acl'
				.' LEFT JOIN #__core_acl_aro_groups_map arogm ON acl.id = arogm.acl_id'
				.' LEFT JOIN #__core_acl_aco_map acom ON acl.id = acom.acl_id'
				.' WHERE acl.section_value = \'actionrule\' AND arogm.group_id = '.$group->getId().' AND acl.allow = '.($allow ? '1':'0')
				.' AND acom.section_value = '.$db->Quote($action->getExtension()).' AND acom.value = '.$db->Quote($action->getAction()).';';
			$db->setQuery($query);
			$acl_id = $db->loadResult();
			if(!($acl_id > 0))
			{
				return false;
			}
			$query = 'DELETE FROM #__core_acl_aco_map WHERE acl_id = '.$acl_id.' AND section_value = '.$db->Quote($action->getExtension()).' AND value = '.$db->Quote($action->getAction());
			$db->setQuery($query);
			$db->Query();
			$query = 'SELECT COUNT(value) FROM #__core_acl_aco_map WHERE acl_id = '.$acl_id;
			$db->setQuery($query);
			$actions_count = $db->loadResult();
			if($actions_count == 0)
			{
				$query = 'DELETE FROM #__core_acl_aro_groups_map WHERE acl_id = '.$acl_id;
				$db->setQuery($query);
				$db->Query();
				$query = 'DELETE FROM #__core_acl_acl WHERE id = '.$acl_id;
				$db->setQuery($query);
				$db->Query();
			}
			return true;
		} else {
			$query = 'SELECT acl.id FROM #__core_acl_acl acl'
				.' LEFT JOIN #__core_acl_aro_groups_map arogm ON acl.id = arogm.acl_id'
				.' LEFT JOIN #__core_acl_aco_map acom ON acl.id = acom.acl_id'
				.' LEFT JOIN #__core_acl_axo_map axom ON acl.id = axom.acl_id'
				.' WHERE acl.section_value = \'actionrule\' AND arogm.group_id = '.$group->getId().' AND acl.allow = '.($allow ? '1':'0')
				.' AND acom.section_value = '.$db->Quote($action->getExtension()).' AND acom.value = '.$db->Quote($action->getAction())
				.' AND axom.section_value = '.$db->Quote($contentitem->getExtension()).' AND axom.value = '.$db->Quote($contentitem->getContentItem()).';';
			$db->setQuery($query);
			$acl_id = $db->loadResult();
			if(!($acl_id > 0))
			{
				return false;
			}
			$query = 'DELETE FROM #__core_acl_axo_map WHERE acl_id = '.$acl_id.' AND section_value = '.$db->Quote($contentitem->getExtension()).' AND value = '.$db->Quote($contentitem->getContentItem());
			$db->setQuery($query);
			$db->Query();
			$query = 'SELECT COUNT(value) FROM #__core_acl_axo_map WHERE acl_id = '.$acl_id;
			$db->setQuery($query);
			$content_count = $db->loadResult();
			if($content_count == 0)
			{
				$query = 'DELETE FROM #__core_acl_aco_map WHERE acl_id = '.$acl_id;
				$db->setQuery($query);
				$db->Query();
				$query = 'DELETE FROM #__core_acl_aro_groups_map WHERE acl_id = '.$acl_id;
				$db->setQuery($query);
				$db->Query();
				$query = 'DELETE FROM #__core_acl_acl WHERE id = '.$acl_id;
				$db->setQuery($query);
				$db->Query();
			}
			return true;
		}
	}
}

class JAuthorizationJACLUserHelper extends JAuthorizationUserHelper
{
	var $_users = array();
	
	function __construct()
	{
		if(!count($this->_users))
		{
			$db =& JFactory::getDBO();
			$query = 'SELECT id as accessid, value AS id, name FROM #__core_acl_aro';
			$db->setQuery($query);
			$this->_users = $db->loadObjectList('id');
		}
	}
	
	function getUser($id = null, $accessid = null)
	{
		if($id)
		{
			if(isset($this->_users[$id]))
			{
				return $this->_users[$id];
			}
		}
		if($accessid)
		{
			foreach($this->_users as $user)
			{
				if($user->accessid == $accessid)
				{
					return $user;
				}
			}
		}
		return false;
	}
	
	function store($user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			if($user->getId() > 0)
			{
				$db =& JFactory::getDBO();
				$query = 'UPDATE #__core_acl_aro'
					.' SET name = \''.$user->getName().'\','
					.' value = \''.$user->getId().'\''
					.' WHERE id = '.$user->getAccessid();
				$db->setQuery($query);
				$db->Query();
				return true;
			} else {
				$db =& JFactory::getDBO();
				$query = 'INSERT INTO #__core_acl_aro'
					.' (name, value)'
					.' VALUES (\''.$user->getName().'\',\''.$user->getId().'\');';
				$db->setQuery($query);
				$db->Query();
				$user->setId($db->inserid());
				return true;
			}
		}
		return false;		
	}
	
	function delete($user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			$db =& JFactory::getDBO();
			$query = 'DELETE FROM #__core_acl_aro WHERE id = '.$user->getAccessid();
			$db->setQuery($query);
			$db->Query();
			$query = 'DELETE FROM #__core_acl_groups_aro_map WHERE aro_id = '.$user->getAccessid();
			$db->setQuery($query);
			$db->Query();
			return true;
		}
		return false;
	}
}

class JAuthorizationJACLActionHelper extends JAuthorizationActionHelper
{
	var $_actions = array();
	
	function __construct()
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT id, section as extension, name, value as action FROM #__core_acl_aco';
		$db->setQuery($query);
		$actions = $db->loadObjectList();
		foreach($actions as $action)
		{
			$this->_actions[$action->extension.'.'.$action->action] = $action;
		}
	}
	
	function getAction($extension, $action)
	{
		return $this->_actions[$extension.'.'.$action];
	}
	
	function getActions($extension)
	{
		$result = array();
		foreach($this->_actions as $action)
		{
			if($action->extension == $extension)
			{
				$result[] = $action;
			}
		}
		return $result;
	}
	
	function store($action)
	{
		if(is_a($action, 'JAuthorizationAction'))
		{
			if($action->getId() > 0)
			{
				$db =& JFactory::getDBO();
				$query = 'UPDATE #__core_acl_aco'
					.' SET name = \''.$action->getName().'\','
					.' section = \''.$action->getExtension().'\','
					.' value = \''.$action->getAction().'\''
					.' WHERE id = '.$action->getId();
				$db->setQuery($query);
				$db->Query();
				return true;				
			} else {
				$db =& JFactory::getDBO();
				$query = 'INSERT INTO #__core_acl_aco'
					.' (name, value, extension)'
					.' VALUES (\''.$action->getName().'\',\''.$action->getAction().'\',\''.$action->getExtension().'\');';
				$db->setQuery($query);
				$db->Query();
				return true;
			}
		}
		return false;		
	}
	
	function delete($action)
	{
		if(is_a($contentitem, 'JAuthorizationAction'))
		{
			$db =& JFactory::getDBO();
			$query = 'DELETE FROM #__core_acl_aco_map WHERE section_value = '.$action->getExtension().' AND value = '.$action->getAction();
			$db->setQuery($query);
			$db->Query();
			$query = 'DELETE FROM #__core_acl_aco WHERE id = '.$action->getId();
			$db->setQuery($query);
			$db->Query();
			return true;		
		}
		return false;
	}
}

class JAuthorizationJACLContentItemHelper extends JAuthorizationContentItemHelper
{
	var $_contentitems = array();
	
	function __construct()
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT id, section as extension, name, value FROM #__core_acl_axo';
		$db->setQuery($query);
		$contentitems = $db->loadObjectList();
		foreach($contentitems as $contentitem)
		{
			$this->_contentitems[$contentitem->extension.'.'.$contentitem->value] = $contentitem;
		}
	}
	
	function getContentItem($extension, $item)
	{
		return $this->_contentitems[$extension.'.'.$item];
	}
	
	function getContentItems($extension)
	{
		$result = array();
		foreach($this->_contentitems as $contentitem)
		{
			if($contentitem->extension == $extension)
			{
				$result[] = $contentitem;
			}
		}
		return $result;
	}
	
	function store($contentitem)
	{
		if(is_a($contentitem, 'JAuthorizationContentItem'))
		{
			if($contentitem->getId() > 0)
			{
				$db =& JFactory::getDBO();
				$query = 'UPDATE #__core_acl_axo'
					.' SET name = \''.$contentitem->getName().'\','
					.' section = \''.$contentitem->getExtension().'\','
					.' value = \''.$contentitem->getValue().'\''
					.' WHERE id = '.$contentitem->getId();
				$db->setQuery($query);
				$db->Query();
				return true;
				
			} else {
				$db =& JFactory::getDBO();
				$query = 'INSERT INTO #__core_acl_axo'
					.' (name, value, extension)'
					.' VALUES (\''.$contentitem->getName().'\',\''.$contentitem->getOption().'\',\''.$contentitem->getExtension().'\');';
				$db->setQuery($query);
				$db->Query();
				return true;
			}
		}
		return false;		
	}
	
	function delete($contentitem)
	{
		if(is_a($contentitem, 'JAuthorizationContentItem'))
		{
			$db =& JFactory::getDBO();
			$query = 'DELETE FROM #__core_acl_axo_map WHERE section_value = '.$contentitem->getExtension().' AND value = '.$contentitem->getValue();
			$db->setQuery($query);
			$db->Query();
			$query = 'DELETE FROM #__core_acl_axo WHERE id = '.$contentitem->getId();
			$db->setQuery($query);
			$db->Query();
			return true;		
		}
		return false;
	}
}

class JAuthorizationJACLExtensionHelper extends JAuthorizationExtensionHelper
{
	var $_extensions = array();
	
	function __construct()
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT id, name, value as option FROM #__core_acl_aco_sections';
		$db->setQuery($query);
		$this->_extensions = $db->loadObjectList('option');
	}
	
	function getExtension($option)
	{
		return $this->_extensions[$option];
	}
	
	function getExtensions()
	{
		return $this->_extensions;
	}
	
	function store($extension)
	{
		if(is_a($extension, 'JAuthorizationExtension'))
		{
			if($extension->getId() > 0)
			{
				$db =& JFactory::getDBO();
				$query = 'UPDATE #__core_acl_aco_sections'
					.' SET name = \''.$extension->getName().'\','
					.' value = \''.$extension->getOption().'\''
					.' WHERE id = '.$extension->getId();
				$db->setQuery($query);
				$db->Query();
				$query = 'UPDATE #__core_acl_axo_sections'
					.' SET name = \''.$extension->getName().'\','
					.' value = \''.$extension->getOption().'\''
					.' WHERE id = '.$extension->getId();
				$db->setQuery($query);
				$db->Query();
				return true;
			} else {
				$db =& JFactory::getDBO();
				$query = 'INSERT INTO #__core_acl_aco_sections'
					.' (name, value)'
					.' VALUES (\''.$extension->getName().'\',\''.$extension->getOption().'\');';
				$db->setQuery($query);
				$db->Query();
				$db =& JFactory::getDBO();
				$query = 'INSERT INTO #__core_acl_axo_sections'
					.' (name, value)'
					.' VALUES (\''.$extension->getName().'\',\''.$extension->getOption().'\');';
				$db->setQuery($query);
				$db->Query();
				return true;
			}
		}
		return false;		
	}
	
	function delete($extension)
	{
		$db =&JFactory::getDBO();
		$query = 'DELETE FROM #__core_acl_aco_sections WHERE id = '.$extension->getId();
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_axo_sections WHERE id = '.$extension->getId();
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_aco WHERE section_value = \''.$extension->getOption().'\'';
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_axo WHERE section_value = \''.$extension->getOption().'\'';
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_aco_map WHERE section_value = \''.$extension->getOption().'\'';
		$db->setQuery($query);
		$db->Query();
		$query = 'DELETE FROM #__core_acl_axo_map WHERE section_value = \''.$extension->getOption().'\'';
		$db->setQuery($query);
		$db->Query();
		return true;
	}
}