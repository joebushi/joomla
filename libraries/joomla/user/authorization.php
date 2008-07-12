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
	var $_groups = array();

	var $_name = null;

	var $_id = null;

	var $_members = null;

	var $_children = null;

	var $_parent = null;

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

	function load()
	{

	}

	function store()
	{

	}

	function remove()
	{

	}

	function getMembers()
	{

	}

	function addMember()
	{

	}

	function removeMember()
	{

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

	function load()
	{

	}

	function store()
	{

	}

	function remove()
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
}

class JAuthorizationUser
{
	var $_id = null;

	var $_name = null;

	var $_value = null;

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

	function getName()
	{

	}

	function setName()
	{

	}

	function getUserID()
	{

	}

	function setUserID()
	{

	}
}

class JAuthorizationExtension
{
	var $_id = null;

	var $_name = null;

	var $_value = null;

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
}