<?php
/**
* @version $Id$
* @package Joomla
* @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
* @license GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Don't allow direct linking
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'authorization'.DS.'helper.php');

/**
 * Abstract class representing the manager for all authorization requests.
 * This manager implements a agregated instance of the generic model handling
 * requests to the authorization databases. In the default Joomla! framework this
 * default implementation is based on phpGACL.
 *
 * @package 	Joomla.Framework
 * @subpackage	Authorization
 * @author 		Hannes Papenberg
 * @abstract 
 * @since		1.6
 */
class JAuthorization
{
	/**
	 * Default Constructor
	 * @param object	service adapter to forward the acl requests
	 */
	function __construct() 
	{
		parent::__construct();
	}

	/**
	 * Singelton method to create one unique instance of a manager per specific acl service
	 * 
	 * @param string	acl service that should be loaded, default JACL
	 * @return object	new instance of the ACLManager
	 */
	function getInstance()
	{
		static $instance;

		if (empty($instances))
		{
			$config =& JFactory::getConfig();
			$driver = $config->getValue('config.aclservice', 'JACL');
			require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'authorization'.DS.strtolower($driver).'.php');
			$adapter	= 'JAuthorization'.$driver;
			$instance	= new $adapter();

		}

		return $instance;
	}

	/**
	 * Hands the ACL check over to the actual checking function
	 *
	 * This function does the actual checking and returns TRUE or FALSE depending on the result.
	 *
	 * @param string $extension The extension the action belongs to
	 * @param string $action The action we want to check for
	 * @param string $xobject The xobject that should be checked [optional]
	 * @param string $xobjectextension If the xobjects extension differs 
	 * from the one of the action, this has to be set to the new extension [optional]
	 * @param string $user If the user ID differs from the one currently logged in,
	 * this one has to be set [optional]
	 * @return boolean
	 */
	function authorize( $extension, $action, $contentitem = null,  $user = null ) {
		return;
	}

	function getAllowedContent($extension, $action, $user = null) {
		return;
	}

	/**
	 * Returns all Usergroups starting with the given root-group [optional]
	 *
	 * @param integer root-group-ID to start from
	 * @param integer return only groups the given user-id is part of
	 * @param boolean returns extensive information about the group if set to true
	 * @return array 
	 */
	function getUsergroups( $root_group = 0, $user = 0, $data = false )
	{
		return;
	}
}

class JAuthorizationUsergroup
{
	/**
	 * ID of the usergroup
	 *
	 * @var int
	 */
	var $_id = null;
	
	/**
	 * Name of the usergroup
	 *
	 * @var string
	 */
	var $_name = null;

	/**
	 * Array of IDs of the access objects representing the users
	 *
	 * @var array
	 */
	var $_users = null;

	/**
	 * Array of IDs of the childgroups
	 *
	 * @var array
	 */
	var $_children = null;

	/**
	 * parent ID of the group 
	 *
	 * @var int
	 */
	var $_parent = null;

	/**
	 * Constructor for the class
	 * This constructor loads the helper class that retrieves
	 * the data and if given a group ID, loads the data into the object
	 *
	 * @param int $id
	 * @return boolean
	 */
	function __construct($id = null)
	{
		$engine =& JAuthorizationUsergroupHelper::getInstance();
		$group =& $engine->getGroup($id);
		
		if($group)
		{
			$this->_id = $group->id;
			$this->_name = $group->name;
			$this->_users = $group->users;
			$this->_children = $group->children;
			$this->_parent = $group->parent;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * returns the group ID
	 *
	 * @return int
	 */
	function getID()
	{
		return $this->_id;
	}

	/**
	 * sets the group ID
	 *
	 * @param int $id
	 * @return int old ID
	 */
	function setID($id = null)
	{
		if($id)
		{
			$temp = $this->_id;
			$this->_id = $id;
			return $temp;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the name of the group
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->_name;
	}
	
	/**
	 * sets the name of the group
	 *
	 * @param string $name
	 * @return string old name of the group
	 */
	function setName($name = null)
	{
		if($name)
		{
			$temp = $this->_name;
			$this->_name = $name;
			return $temp;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns all users that are member of the group
	 *
	 * @return array Array of JAuthorizationUser objects 
	 */
	function getUsers()
	{
		$result = array();
		foreach($this->_users as $user)
		{
			$result[] = new JAuthorizationUser(null, $user);
		}
		return $result;
	}
	
	/**
	 * Returns all users that are not member of any group
	 *
	 * @return array Array of JAuthorizationUser objects
	 */
	function getUngroupedUsers()
	{
		$engine =& JAuthorizationUsergroupHelper::getInstance();
		return $engine->getUngroupedUsers();
	}
	
	/**
	 * Adds a user to the current group
	 *
	 * @param JAuthorizationUser $user
	 * @return bool
	 */
	function addUser($user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			$engine =& JAuthorizationUsergroupHelper::getInstance();
			$result = $engine->addUser($this->_id, $user);
			if($result)
			{
				$this->_users[] = $user->getAccessId();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * removes a user from a group
	 *
	 * @param JAuthorizationUser $user
	 * @return bool
	 */
	function removeUser($user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			$engine =& JAuthorizationUsergroupHelper::getInstance();
			$result = $engine->removeUser($this->_id, $user);
			if($result)
			{
				$users = $this->_users;
				$this->_users = array();
				foreach($users as $temp_user)
				{
					if($temp_user != $user)
					{
						$this->_users[] = $temp_user;
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the parent Group of this group as a JAuthorizationUsergroup object
	 *
	 * @return JAuthorizationUsergroup
	 */
	function getParent()
	{
		return new JAuthorizationUsergroup($this->_parent);
	}
	
	/**
	 * Sets the parent of the group
	 *
	 * @param JAuthorizationUsergroup $parent
	 * @return int Old ID of the previous usergroup
	 */
	function setParent($parent)
	{
		$temp = $this->_parent;
		$this->_parent = $parent->getId();
		return $temp;
	}

	/**
	 * Returns all children of a group as JAuthorizationUsergroup objects
	 *
	 * @return array Array of JAuthorizationUsergroup objects
	 */
	function getChildren()
	{
		if(count($this->_children))
		{
			$result = array();
			foreach($this->_children as $child)
			{
				$result[] = new JAuthorizationUsergroup($child);
			}
			return $result;
		} else {
			return false; 
		}
	}

	/**
	 * Adds the group in the parameter as a child to the current group
	 * This is the way to save new groups in the system
	 *
	 * @param JAuthorizationUsergroup $group
	 * @return bool
	 */
	function addChild($group)
	{
		if(is_a($group, 'JAuthorizationUsergroup'))
		{
			$engine =& JAuthorizationUsergroupHelper::getInstance();
			$group->setParent($this->_id);
			$result = $engine->addGroup($group);
			if($result)
			{
				$this->_children[] = $group->getId();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Removes a group out of the system
	 * All childgroups are re-assigned to the parent group
	 *
	 * @param JAuthorizationUsergroup $group
	 * @return bool
	 */
	function removeChild($group)
	{
		if(is_a($group, 'JAuthorizationGroup'))
		{
			$engine =& JAuthorizationUsergroupHelper::getInstance();
			$result = $engine->removeGroup($group);
			if($result)
			{
				$groups = $this->_children;
				$this->_children = array();
				foreach($groups as $temp_group)
				{
					if($temp_group != $group->getId())
					{
						$this->_children[] = $temp_group;
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

class JAuthorizationRule
{
	function __construct()
	{

	}

	/**
	 * Singelton method to create one unique instance of an manager per specific acl service
	 * 
	 * @param string	acl service that should be loaded, default phpgacl
	 * @return object	new instance of the ACLManager
	 */
	function getInstance()
	{
		static $instance;

		if (empty($instances))
		{
			$config =& JFactory::getConfig();
			$driver = $config->getValue('config.aclservice', 'JACL');
			require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'authorization'.DS.strtolower($driver).'.php');
			$adapter	= 'JAuthorization'.$driver.'Rule';
			$instance	= new $adapter();
		}

		return $instance;
	}
	
	function authorizeGroup($group, $extension, $action, $contentitem = null)
	{
		
	}

	function addRule($allow = true, $group, $action, $contentitem = null)
	{
		
	}
	
	function removeRule($allow = true, $group, $action, $contentitem = null)
	{
		
	}
}

class JAuthorizationAction
{
	/**
	 * ID of the Action object
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Extension this action is part of
	 *
	 * @var string
	 */
	var $_extension = null;

	/**
	 * Name of the Action
	 *
	 * @var string
	 */
	var $_name = null;

	/**
	 * Internal name for the Action
	 *
	 * @var string
	 */
	var $_action = null;

	/**
	 * Constructor, loads the given action into the object
	 *
	 * @param string $extension
	 * @param string $item
	 */
	function __construct($extension = null, $item = null)
	{
		if($extension != null && $item != null)
		{
			$engine = JAuthorizationActionHelper::getInstance();
			$action = $engine->getAction($extension, $item);
			$this->_id = $action->id;
			$this->_extension = $action->extension; 
			$this->_name = $action->name;
			$this->_action = $action->action;
		}
	}
	
	function getExtension()
	{
		return $this->_extension;
	}

	function setExtension($extension)
	{
		$this->_extension = $extension;
	}

	function getName()
	{
		return $this->_name;
	}

	function setName($name)
	{
		$this->_name = $name;
	}

	function getAction()
	{
		return $this->_action;
	}

	function setAction($action)
	{
		$this->_action = $action;
	}
	
	function store()
	{
		$engine = JAuthorizationActionHelper::getInstance();
		$engine->store($this);
	}
	
	function delete()
	{
		$engine = JAuthorizationActionHelper::getInstance();
		$engine->delete($this);
	}
}

class JAuthorizationContentItem
{
	var $_id = null;

	var $_extension = null;

	var $_name = null;

	var $_value = null;

	function __construct($extension = null, $item = null)
	{
		if($extension != null && $item != null)
		{
			$engine = JAuthorizationContentItemHelper::getInstance();
			$contentitem = $engine->getContentItem($extension, $item);
			$this->_id = $contentitem->id;
			$this->_extension = $contentitem->extension; 
			$this->_name = $contentitem->name;
			$this->_value = $contentitem->value;
		}
	}
	
	function getExtension()
	{
		return $this->_extension;
	}

	function setExtension($extension)
	{
		$this->_extension = $extension;
	}

	function getName()
	{
		return $this->_name;
	}

	function setName($name)
	{
		$this->_name = $name;
	}

	function getValue()
	{
		return $this->_value;
	}

	function setValue($value)
	{
		$this->_value = $value;
	}
	
	function store()
	{
		$engine = JAuthorizationContentItemHelper::getInstance();
		$engine->store($this);
	}
	
	function delete()
	{
		$engine = JAuthorizationContentItemHelper::getInstance();
		$engine->delete($this);
	}
	
	function getContentItems($extension)
	{
		$engine = JAuthorizationContentItemHelper::getInstance();
		return $engine->getContentItems($extension);
	}
}

class JAuthorizationUser
{
	var $_accessid = null;

	var $_id = null;

	var $_name = null;

	function __construct($id = null, $accessid = null)
	{
		if($id != null || $accessid != null)
		{
			$engine = JAuthorizationUserHelper::getInstance();
			$user = $engine->getUser($id, $accessid);
			$this->_accessid = $user->accessid;
			$this->_id = $user->id;
			$this->_name = $user->name;
		}
	}
	
	function getId()
	{
		return $this->_id;
	}
	
	function setId($id)
	{
		$this->_id = $id;
	}
	
	function getAccessId($id)
	{
		return $this->_accessid;
	}
	
	function setAccessId($id)
	{
		$this->_accessid = $id;
	}
	
	function getName()
	{
		return $this->_name;
	}

	function setName($name)
	{
		$this->_name = $name;
	}
	
	function store()
	{
		$engine = JAuthorizationUserHelper::getInstance();
		$engine->store($this);
	}

	function delete()
	{
		$engine = JAuthorizationUserHelper::getInstance();
		$engine->delete($this);
	}
}


class JAuthorizationExtension
{
	var $_id = null;

	var $_name = null;

	var $_option = null;

	function __construct($option = null)
	{
		if($option != null)
		{
			$engine = JAuthorizationExtensionHelper::getInstance();
			$extension = $engine->getExtension($option);
			$this->_id = $extension->id;
			$this->_name = $extension->name;
			$this->_option = $extension->option;
		}
	}

	function getName()
	{
		return $this->_name;
	}

	function setName($name)
	{
		$this->_name = $name;
	}
	
	function getOption()
	{
		return $this->_option;
	}

	function setOption($option)
	{
		$this->_option = $option;
	}

	function store()
	{
		$engine = JAuthorizationExtensionHelper::getInstance();
		return $engine->store($this);
	}

	function delete()
	{
		$engine = JAuthorizationExtensionHelper::getInstance();
		return $engine->delete($this);
	}
}