<?php
/**
* @version $Id: authorization.php 10670 2008-08-17 11:40:33Z hackwar $
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

class JAuthorizationUserHelper
{
	var $_users = array();
	
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
			$adapter	= 'JAuthorization'.$driver.'UserHelper';
			$instance	= new $adapter();

		}

		return $instance;
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
		
	}
	
	function delete($user)
	{
		
	}
}

class JAuthorizationActionHelper
{
	var $_actions = array();
	
	function __construct()
	{
		
	}
	
	function getInstance()
	{
		
	}
	
	function getAction($extension, $action)
	{
		
	}
	
	function getActions($extension)
	{
		
	}
	
	function store($action)
	{
		
	}
	
	function delete($action)
	{
		
	}
}

class JAuthorizationContentItemHelper
{
	var $_contentitems = array();
	
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
			$adapter	= 'JAuthorization'.$driver.'ContentItemHelper';
			$instance	= new $adapter();

		}

		return $instance;
	}
	
	function getContentItem($extension, $item)
	{
		
	}
	
	function getContentItems($extension)
	{
		
	}
	
	function store($contentitem)
	{
		
	}
	
	function delete($contentitem)
	{
		
	}
}

class JAuthorizationExtensionHelper
{
	var $_extensions = array();
	
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
	
	function getExtension($option)
	{
		return false;
	}
	
	function getExtensions()
	{
		return false;
	}
	
	function store($extension)
	{
		return false;
	}
	
	function delete($extension)
	{
		return false;
	}
}
