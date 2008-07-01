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
 * @author 		Alex Kempkens, Hannes Papenberg
 * @abstract 
 * @since		1.5
 */
class JAuthorization
{
	/** @var local adapter to redirect all ACL requests */
	var $_acladapter=null;
	
	/**
	 * Default Constructor
	 * @param object	service adapter to forward the acl requests
	 */
	function __construct( $acladapter=NULL ) 
	{
		$this->_acladapter = $acladapter;
	}

	/**
	 * Singelton method to create one unique instance of an manager per specific acl service
	 * 
	 * @param string	acl service that should be loaded, default phpgacl
	 * @return object	new instance of the ACLManager
	 */
	function getInstance($aclservice = 'phpgacl')
	{
		static $instances;

		if (!isset( $instances )) {
			$instances = array();
		}

		if( empty( $aclservice ) ) {
			// setting default
			$aclservice = 'phpgacl';
		}
		
		if (empty($instances[$aclservice])) {
			$adapter = 'JACL'.$aclservice.'Manager';

			$aclAdapter = new $adapter();
			$aclManager = new JAuthorization( $aclAdapter );
			$instances[$aclservice] = $aclManager;
		}

		return $instances[$aclservice];
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
	function authorize( $extension, $action, $xobject = null, $xobjectextension = null,  $user = null ) {
		return $this->_acladapter->authorize( $extension, $action, $xobject, $xobjectextension, $user );
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
		return $this->_acladapter->getUsergroups( $root_group, $user );
	}

	/**
	 * Returns extensive informations about a Usergroup
	 *
	 * @param mixed group-id or groupname
	 * @return array 
	 */
	function getUsergroup( $group )
	{
		return $this->_acladapter->getUsergroups( $root_group, $user );
	}

	/**
	 * Returns all content-object-groups starting with the given root-group [optional]
	 *
	 * @param integer root-group-ID to start from
	 * @param boolean if set to TRUE, all groups are returned with extensive data
	 * @return array 
	 */
	function getContentGroups( $root_group = 0, $data = false )
	{
		return $this->_acladapter->getXGroups( $root_group, $data );
	}

	/**
	 * Returns extensive information on a content-object-group
	 *
	 * @param mixed group-id or groupname
	 * @return array 
	 */
	function getContentGroup( $group )
	{
		return $this->_acladapter->getXGroups( $root_group, $data );
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
		return $this->_acladapter->addUserGroup( $name, $parent, $id );
	}

	/**
	 * Adds an user to a group
	 *
	 * @param integer User-ID
	 * @param integer group-ID
	 */
	function addUser2Group( $user, $group )
	{
		return $this->_acladapter->addUser2Group( $user, $group );
	}

	/**
	 * Removes a user group
	 *
	 * @param integer group-ID
	 * @param boolean If set to TRUE, child-groups are reassigned
	 */
	function delusergroup( $group, $reparent_children = TRUE )
	{
		return $this->_acladapter->delusergroup( $group, $reparent_children );
	}

	/**
	 * Removes a user from a group
	 *
	 * @param integer user-ID to be removed from the group
	 * @param integer group-ID
	 */
	function deluser2group( $user, $group )
	{
		return $this->_acladapter->deluser2group( $user, $group );
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
		return $this->_acladapter->allowuser( $user, $extension, $action, $xobject, $xobject_extension, $xobjectgroup );
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
		return $this->_acladapter->denyuser( $user, $extension, $action, $xobject, $xobject_extension, $xobjectgroup );
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
		return $this->_acladapter->allowgroup( $group, $extension, $action, $xobject, $xobject_extension, $xobjectgroup );
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
		return $this->_acladapter->denygroup( $group, $extension, $action, $xobject, $xobject_extension, $xobjectgroup );
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
		return $this->_acladapter->addxgroup( $name, $parent, $oldgroup );
	}

	/**
	 * Removes an xobject group
	 *
	 * @param integer group-ID
	 */
	function delxgroup( $group, $reparent_children = TRUE )
	{
		return $this->_acladapter->delxgroup( $group, $reparent_children );
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
		return $this->_acladapter->addxobject2group( $extension, $xobject, $group );
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
		return $this->_acladapter->delxobject2group( $extension, $xobject, $group );
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
		return $this->_acladapter->getUsers( $group );
	}

	/**
	 * Returns users that are not assigned to a group
	 *
	 * @return array Username and user-ID
	 */
	function getUsersUngrouped()
	{
		return $this->_acladapter->getUsersUngrouped();
	}

	/**
	 * Adds a user
	 * 
	 * @param string User-ID
	 * @param string user name
	 * @param string Old user-ID. Use for editing users
	 */
	function adduser( $userid, $name, $oldID = null)
	{
		return $this->_acladapter->adduser( $userid, $name, $oldid );
	}

	/**
	 * Removes a user
	 *
	 * @param integer user-ID
	 */
	function deluser( $user )
	{
		return $this->_acladapter->deluser( $user );
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
		return $this->_acladapter->getactions( $extension );
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
		return $this->_acladapter->addaction( $value, $name, $extension, $oldname );
	}

	/**
	 * Removes an action
	 *
	 * @param string Extension of the action
	 * @param string internal value of an action
	 */
	function delaction( $extension, $value )
	{
		return $this->_acladapter->delaction( $extension, $value );
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
		return $this->_acladapter->getxobjects( $extension, $group );
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
		return $this->_acladapter->addxobject( $name, $value, $extension, $oldname );
	}

	/**
	 * Removes an xobject
	 *
	 * @param string Extension of the xobject
	 * @param string Internal value of an xobject
	 */
	function delxobject( $extension, $value )
	{
		return $this->_acladapter->delxobject( $extension, $value );
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
		return $this->_acladapter->getextensions( $extension );
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
		return $this->_acladapter->addextension( $name, $value, $xobjects, $oldname );
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
		return $this->_acladapter->delextension( $name, $value );
	}

	function getAllowedContent($extension, $action, $user = 0, $contentExtension = 0, $contentGroup = 0)
	{

	}

	function getAllowedActions($extension = 0, $action = 0, $user = 0)
	{
		return $this->_acladapter->getAllowedActions($extension, $action, $user);
	}
}	
?>