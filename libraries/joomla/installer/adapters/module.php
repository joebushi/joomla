<?php
/**
 * @version		$Id: module.php 6138 2007-01-02 03:44:18Z eddiea $
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * Module installer
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 */
class JInstaller_module extends JObject
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [JInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct(&$parent)
	{
		$this->parent =& $parent;
	}

	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function install()
	{
		// Get database connector object
		$db =& $this->parent->getDBO();
		$manifest =& $this->parent->getManifest();
		$root =& $manifest->document;

		// Get the client application target
		if ($cname = $root->attributes('client')) {
			// Attempt to map the client to a base path
			jimport('joomla.application.helper');
			$client = JApplicationHelper::getClientInfo($cname, true);
			if ($client === false) {
				$this->parent->abort('Module Install: '.JText::_('Unknown client type').' ['.$client->name.']');
				return false;
			}
			$basePath = $client->path;
			$clientId = $client->id;
		} else {
			// No client attribute was found so we assume the site as the client
			$cname = 'site';
			$basePath = JPATH_SITE;
			$clientId = 0;
		}

		// Get the module name
		$name =& $root->getElementByPath('name');
		$this->set('name', $name->data());

		// Set the installation path
		$element =& $root->getElementByPath('files');
		if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
			$files =& $element->children();
			foreach ($files as $file) {
				if ($file->attributes('module')) {
					$mname = $file->attributes('module');
					break;
				}
			}
		}
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', JPath::clean($basePath.DS.'modules'.DS.$mname));
		} else {
			$this->parent->abort('Module Install: '.JText::_('No module file specified'));
			return false;
		}

		/*
		 * If the module directory already exists, then we will assume that the
		 * module is already installed or another module is using that
		 * directory.
		 */
		if (file_exists($this->parent->getPath('extension_root'))) {
			$this->parent->abort('Module Install: '.JText::_('Another module is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the module directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort('Module Install: '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		 * Since we created the module directory and will want to remove it if
		 * we have to roll back the installation, lets add it to the
		 * installation step stack
		 */
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy all images, media and languages as well
		$this->parent->parseFiles($root->getElementByPath('images'), -1);
		$this->parent->parseFiles($root->getElementByPath('media'));
		$this->parent->parseFiles($root->getElementByPath('administration/media'), 1);
		$this->parent->parseFiles($root->getElementByPath('languages'));
		$this->parent->parseFiles($root->getElementByPath('administration/languages'), 1);

		// Check to see if a module by the same name is already installed
		$query = "SELECT `id`" .
				"\n FROM `#__modules` " .
				"\n WHERE module = ".$db->Quote($mname) .
				"\n AND client_id = ".(int)$clientId;
		$db->setQuery($query);
		if (!$db->Query()) {
			// Install failed, roll back changes
			$this->parent->abort('Module Install: '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

		// Was there a module already installed with the same name?
		if ($id) {
			// Install failed, roll back changes
			$this->parent->abort('Module Install: '.JText::_('Module').' "'.$mname.'" '.JText::_('already exists!'));
			return false;
		} else {
			$row = & JTable::getInstance('module');
			$row->title = $this->get('name');
			$row->ordering = 99;
			$row->position = 'left';
			$row->showtitle = 1;
			$row->iscore = 0;
			$row->access = $clientId == 1 ? 2 : 0;
			$row->client_id = $clientId;
			$row->module = $mname;
			$row->params = $this->parent->getParams();

			if (!$row->store()) {
				// Install failed, roll back changes
				$this->parent->abort('Module Install: '.$db->stderr(true));
				return false;
			}

			// Since we have created a module item, we add it to the installation step stack
			// so that if we have to rollback the changes we can undo it.
			$this->parent->pushStep(array ('type' => 'module', 'id' => $row->id));

			// Clean up possible garbage first
			$query = 'DELETE FROM #__modules_menu WHERE moduleid = '.(int) $row->id;
			$db->setQuery( $query );
			if (!$db->query()) {
				// Install failed, roll back changes
				$this->parent->abort('Module Install: '.$db->stderr(true));
				return false;
			}

			// Time to create a menu entry for the module
			$query = "INSERT INTO `#__modules_menu` " .
					"\nVALUES (".(int) $row->id.", 0 )";
			$db->setQuery($query);
			if (!$db->query()) {
				// Install failed, roll back changes
				$this->parent->abort('Module Install: '.$db->stderr(true));
				return false;
			}

			/*
			 * Since we have created a menu item, we add it to the installation step stack
			 * so that if we have to rollback the changes we can undo it.
			 */
			$this->parent->pushStep(array ('type' => 'menu', 'id' => $db->insertid()));
		}

		// Get the module description
		$description = & $root->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $this->get('name').'<p>'.$description->data().'</p>');
		} else {
			$this->parent->set('message', $this->get('name'));
		}

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->parent->abort('Module Install: '.JText::_('Could not copy setup file'));
			return false;
		}
		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$id			The id of the module to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall( $id, $clientId )
	{
		// Initialize variables
		$row	= null;
		$retval = true;
		$db		=& $this->parent->getDBO();

		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.
		$row = & JTable::getInstance('module');
		$row->load((int) $id);

		// Is the module we are trying to uninstall a core one?
		// Because that is not a good idea...
		if ($row->iscore) {
			JError::raiseWarning(100, 'Module Uninstall: '.JText::sprintf('WARNCOREMODULE', $row->name)."<br />".JText::_('WARNCOREMODULE2'));
			return false;
		}

		// Get the extension root path
		jimport('joomla.application.helper');
		$client = JApplicationHelper::getClientInfo($row->client_id);
		if ($client === false) {
			$this->parent->abort('Module Uninstall: '.JText::_('Unknown client type').' ['.$row->client_id.']');
			return false;
		}
		$this->parent->setPath('extension_root', $client->path.DS.'modules'.DS.$row->module);

		// Get the package manifest objecct
		$this->parent->setPath('source', $this->parent->getPath('extension_root'));
		$manifest =& $this->parent->getManifest();
		if (!is_a($manifest, 'JSimpleXML')) {
			// Make sure we delete the folders
			JFolder::delete($this->parent->getPath('extension_root'));
			JError::raiseWarning(100, 'Module Uninstall: Package manifest file invalid or not found');
			return false;
		}

		// Remove other files
		$root =& $manifest->document;
		$this->parent->removeFiles($root->getElementByPath('media'));
		$this->parent->removeFiles($root->getElementByPath('administration/media'), 1);
		$this->parent->removeFiles($root->getElementByPath('languages'));
		$this->parent->removeFiles($root->getElementByPath('administration/languages'), 1);

		// Lets delete all the module copies for the type we are uninstalling
		$query = "SELECT `id`" .
				"\n FROM `#__modules`" .
				"\n WHERE module = ".$db->Quote($row->module) .
				"\n AND client_id = ".(int)$row->client_id;
		$db->setQuery($query);
		$modules = $db->loadResultArray();

		// Do we have any module copies?
		if (count($modules)) {
			$modID = implode(',', $modules);
			$query = "DELETE" .
					"\n FROM #__modules_menu" .
					"\n WHERE moduleid IN ('".$modID."')";
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseWarning(100, 'Module Uninstall: '.$db->stderr(true));
				$retval = false;
			}
		}

		// Now we will no longer need the module object, so lets delete it and free up memory
		$row->delete($row->id);
		unset ($row);

		// Remove the installation folder
		if (!JFolder::delete($this->parent->getPath('extension_root'))) {
			// JFolder should raise an error
			$retval = false;
		}
		return $retval;
	}

	/**
	 * Custom rollback method
	 * 	- Roll back the menu item
	 *
	 * @access	public
	 * @param	array	$arg	Installation step to rollback
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _rollback_menu($arg)
	{
		// Get database connector object
		$db =& $this->parent->getDBO();

		// Remove the entry from the #__modules_menu table
		$query = "DELETE" .
				"\n FROM `#__modules_menu`" .
				"\n WHERE moduleid=".(int)$arg['id'];
		$db->setQuery($query);
		return ($db->query() !== false);
	}

	/**
	 * Custom rollback method
	 * 	- Roll back the module item
	 *
	 * @access	public
	 * @param	array	$arg	Installation step to rollback
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _rollback_module($arg)
	{
		// Get database connector object
		$db =& $this->parent->getDBO();

		// Remove the entry from the #__modules table
		$query = "DELETE" .
				"\n FROM `#__modules`" .
				"\n WHERE id=".(int)$arg['id'];
		$db->setQuery($query);
		return ($db->query() !== false);
	}
}
?>