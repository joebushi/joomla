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
 * @since		1.5
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
	var $_id = null;
	
	var $_name = null;

	var $_users = null;

	var $_children = null;

	var $_parent = null;

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

	function getID()
	{
		return $this->_id;
	}

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
	
	function getName()
	{
		return $this->_name;
	}
	
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
	
	function getUsers()
	{
		$result = array();
		foreach($this->_users as $user)
		{
			$result[] = new JAuthorizationUser($user);
		}
		return $result;
	}
	
	function getUngroupedUsers()
	{
		$engine =& JAuthorizationUsergroupHelper::getInstance();
		return $engine->getUngroupedUsers();
	}
	
	function addUser($user)
	{
		if(is_a($user, 'JAuthorizationUser'))
		{
			$engine =& JAuthorizationUsergroupHelper::getInstance();
			$result = $engine->addUser($this->_id, $user);
			if($result)
			{
				$this->_users[] = $user->getId();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
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
	
	function getParent()
	{
		return new JAuthorizationUsergroup($this->_parent);
	}
	
	function setParent($parent)
	{
		$temp = $this->_parent;
		$this->_parent = $parent;
		return $temp;
	}

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

class JAuthorizationUsergroupHelper
{
	var $_groups = array();
		
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

		if (empty($instance))
		{
			$config =& JFactory::getConfig();
			$driver = $config->getValue('config.aclservice', 'JACL');
			require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'authorization'.DS.strtolower($driver).'.php');
			$adapter	= 'JAuthorization'.$driver.'UsergroupHelper';
			$instance	= new $adapter();
		}

		return $instance;
	}
	
	function getGroup($id)
	{
		return false;	
	}

	function addGroup($group)
	{
		return false;
	}

	function removeGroup($group)
	{
		return false;
	}
	
	function addUser($id, $user)
	{
		return false;
	}
	
	function removeUser($id, $user)
	{
		return false;
	}
}

class JAuthorizationRule
{
	var $_rules = null;

	var $_id = null;

	var $_allow = true;

	var $_groups = null;

	var $_actions = null;

	var $_contentitems = null;

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

	function load()
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

class JAuthorizationAction
{
	var $_actions = null;

	var $_id = null;

	var $_extension = null;

	var $_name = null;

	var $_value = null;

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
			$adapter	= 'JAuthorization'.$driver.'Action';
			$instance	= new $adapter();

		}

		return $instance;
	}

	function load()
	{
		return;
	}

	function store()
	{
		return;
	}

	function remove()
	{
		return;
	}

	function getExtension()
	{
		return;
	}

	function setExtension()
	{
		return;
	}

	function getName()
	{
		return;
	}

	function setName()
	{
		return;
	}

	function getValue()
	{
		return;
	}

	function setValue()
	{
		return;
	}

	function getActions()
	{
		return;
	}
}

class JAuthorizationContentItem
{
	var $_contentitems = null;

	var $_id = null;

	var $_extension = null;

	var $_name = null;

	var $_value = null;

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
			$adapter	= 'JAuthorization'.$driver.'ContentItem';
			$instance	= new $adapter();

		}

		return $instance;
	}

	function load()
	{
		return;
	}

	function store()
	{
		return;
	}

	function remove()
	{
		return;
	}

	function getExtension()
	{
		return;
	}

	function setExtension()
	{
		return;
	}

	function getName()
	{
		return;
	}

	function setName()
	{
		return;
	}

	function getValue()
	{
		return;
	}

	function setValue()
	{
		return;
	}

	function getContentItems()
	{
		return;
	}
}

class JAuthorizationUser
{
	var $_users = null;

	var $_id = null;

	var $_name = null;

	var $_value = null;

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
			$adapter	= 'JAuthorization'.$driver.'User';
			$instance	= new $adapter();

		}

		return $instance;
	}

	function getId()
	{
		
	}
	
	function load()
	{
		return;
	}

	function store()
	{
		return;
	}

	function remove()
	{
		return;
	}

	function getName()
	{
		return;
	}

	function setName()
	{
		return;
	}

	function getUserID()
	{
		return;
	}

	function setUserID()
	{
		return;
	}

	function getUsers()
	{
		return;
	}
}

class JAuthorizationExtension
{
	var $_extensions = null;

	var $_id = null;

	var $_name = null;

	var $_value = null;

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
			$adapter	= 'JAuthorization'.$driver.'Extension';
			$instance	= new $adapter();

		}

		return $instance;
	}

	function load()
	{
		return;
	}

	function store()
	{
		return;
	}

	function remove()
	{
		return;
	}

	function getName()
	{
		return;
	}

	function setName($name)
	{
		return;
	}

	function getValue()
	{
		return;
	}

	function setValue($value)
	{
		return;
	}

	function getExtensions()
	{
		return;
	}
}