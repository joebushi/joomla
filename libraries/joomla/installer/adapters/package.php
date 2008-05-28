<?php
/**
 * @version		$Id:plugin.php 6961 2007-03-15 16:06:53Z tcp $
 * @package		JPackageMan
 * @subpackage	Installer
 * @copyright	Copyright (C) 2008 Toowoomba Regional Council/Sam Moffatt
 * @copyright 	Copyright (C) 2005-2007 Open Source Matters (Portions)
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if(!defined('PACKAGE_MANIFEST_PATH')) {
	define('PACKAGE_MANIFEST_PATH',JPATH_ADMINISTRATOR . DS . 'manifests' . DS . 'packages');
}

/**
 * Package installer
 *
 * @author 		Sam Moffatt <pasamio@gmail.com> 
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.6
 */
class JInstallerPackage extends JObject
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
		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =& $manifest->document;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =& $this->manifest->getElementByPath('packagename');
		$name = JFilterInput::clean($name->data(), 'cmd');
		$this->set('name', $name);

		// Get the component description
		$description = & $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}

		// Set the installation path
		$element =& $this->manifest->getElementByPath('files');
		$group = $this->manifest->getElementByPath('packagename');
		$group = $group->data();
		if (!empty($group)) {
			// TODO: Remark this location
			$this->parent->setPath('extension_root', JPATH_ROOT.DS.'libraries'.DS.implode(DS,explode('/',$group)));
		} else {
			$this->parent->abort(JText::_('Package').' '.JText::_('Install').': '.JText::_('No package file specified'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// If the plugin directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Package').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		 * If we created the plugin directory and will want to remove it if we
		 * have to roll back the installation, lets add it to the installation
		 * step stack
		 */
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}
		
		if ($folder = $element->attributes('folder')) {
			$source = $this->parent->getPath('source').DS.$folder;
		} else {
			$source = $this->parent->getPath('source');
		}

		// Install all necessary files
		if(is_a($element, 'JSimpleXMLElement') && count($element->children())) { 
			foreach($element->children() as $child) {
				$file = $source . DS . $child->data();
				jimport('joomla.installer.helper');
				$package = JInstallerHelper::unpack($file);
				$tmpInstaller = new JInstaller();
				if(!$tmpInstaller->install($package['dir'])) {
					$this->parent->abort(JText::_('Package').' '.JText::_('Install').': '.JText::_('There was an error installing an extension:') . basename($file));
					return false;
				}
			}
		} else {
			$this->parent->abort(JText::_('Package').' '.JText::_('Install').': '.JText::_('There were no files to install!').print_r($element,1));
			return false;
		}
				
		/*if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}*/

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		$manifest = Array();
		$manifest['src'] = $this->parent->getPath('manifest');
		$manifest['dest'] = PACKAGE_MANIFEST_PATH.DS.basename($this->parent->getPath('manifest'));
		if (!$this->parent->copyFiles(array($manifest), true)) {
			// Install failed, rollback changes
			$this->parent->abort(JText::_('Package').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}
		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	string	$id	The id of the package to uninstall
	 * @param	int		$clientId	The id of the client (unused; libraries are global)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall($id, $clientId )
	{
		// Initialize variables
		$row	= null;
		$retval = true;
		$manifestFile = PACKAGE_MANIFEST_PATH . DS . $id .'.xml';
		$manifest = new JPackageManifest($manifestFile);		

		// Set the plugin root path
		$this->parent->setPath('extension_root', PACKAGE_MANIFEST_PATH.DS.$manifest->packagename);

		// Because libraries may not have their own folders we cannot use the standard method of finding an installation manifest
		if (file_exists($manifestFile))
		{
			$xml =& JFactory::getXMLParser('Simple');

			// If we cannot load the xml file return null
			if (!$xml->loadFile($manifestFile)) {
				JError::raiseWarning(100, JText::_('Package').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));
				return false;
			}

			/*
			 * Check for a valid XML root tag.
			 * @todo: Remove backwards compatability in a future version
			 * Should be 'install', but for backward compatability we will accept 'mosinstall'.
			 */
			$root =& $xml->document;
			if ($root->name() != 'install' && $root->name() != 'mosinstall') {
				JError::raiseWarning(100, JText::_('Package').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));
				return false;
			}
			
			$error = false;
			foreach($manifest->filelist as $extension) {
				$tmpInstaller = new JInstaller();
				$id = $this->_getExtensionId($extension->type, $extension->id, $extension->client, $extension->group);
				$client = JApplicationHelper::getClientInfo($extension->client,true);
				if(!$tmpInstaller->uninstall($extension->type, $id, $client->id)) {
					$error = true;
					JError::raiseWarning(100, JText::_('Package').' '.JText::_('Uninstall').': '.
//							JText::_('There was an error removing an extension!') . ' ' .
							JText::_('This extension may have already been uninstalled or might not have been uninstall properly') .': ' . 
							basename($extension->filename));
					//$this->parent->abort(JText::_('Package').' '.JText::_('Uninstall').': '.JText::_('There was an error removing an extension, try reinstalling:') . basename($extension->filename));
					//return false;
				}
			}
			// clean up manifest file after we're done if there were no errors
			if(!$error) 	JFile::delete($manifestFile);
			else JError::raiseWarning(100, JText::_('Package'). ' ' . JText::_('Uninstall'). ': '.
					JText::_('Errors were detected, manifest file not removed!'));
		} else {
			JError::raiseWarning(100, JText::_('Package').' '.JText::_('Uninstall').': '. 
				JText::_('Manifest File invalid or not found'));
			return false;
		}

		return $retval;
	}
	
	function _getExtensionID($type, $id, $client, $group) {
		$db		=& $this->parent->getDBO();
		$result = $id;
		switch($type) {
			case 'plugin':
				$db->setQuery("SELECT id FROM #__plugins WHERE folder = '$group' AND element = '$id'");
				$result = $db->loadResult();
				break;
			case 'component':
				$db->setQuery("SELECT id FROM #__components WHERE parent = 0 AND `option` = '$id'");
				$result = $db->loadResult();
				break;
			case 'module':
				$db->setQuery("SELECT id FROM #__modules WHERE module = '$id' and client_id = '$client'");
				$result = $db->loadResult();
				break;
			case 'language':
				// A language is a complex beast
				// its actually a path!
				$clientInfo =& JApplicationHelper::getClientInfo($this->_state->get('filter.client'));
				$client = $clientInfo->name;
				$langBDir = JLanguage::getLanguagePath($clientInfo->path);
				$result = $langBDir . DS . $id;
				break;
		}
		// note: for templates, libraries and packages their unique name is their key
		// this means they come out the same way they came in
		return $result;
	}

}

if(!class_exists('JPackageManifest')) {
	class JPackageManifest extends JObject {
		
		var $name = '';		
		var $packagename = '';
		var $url = '';
		var $description = '';
		var $packager = '';
		var $packagerurl = '';
		var $update = '';
		var $version = '';
		var $filelist = Array();
		var $manifest_file = '';
		
		function __construct($xmlpath='') {
			if(strlen($xmlpath)) $this->loadManifestFromXML($xmlpath);
		}
		
		function loadManifestFromXML($xmlfile) {
			$this->manifest_file = JFile::stripExt(basename($xmlfile));
			$xml = JFactory::getXMLParser('Simple');
			if(!$xml->loadFile($xmlfile)) {
				$this->_errors[] = 'Failed to load XML File: ' . $xmlfile;
				return false;
			} else {
				$xml = $xml->document;
				$this->name = $xml->name[0]->data();
				$this->packagename = $xml->packagename[0]->data();
				$this->update = $xml->update[0]->data();
				$this->url = $xml->url[0]->data();
				$this->description = $xml->description[0]->data();
				$this->packager = $xml->packager[0]->data();
				$this->packagerurl = $xml->packagerurl[0]->data();
				$this->version = $xml->version[0]->data();
				if(isset($xml->files[0]->file) && count($xml->files[0]->file)) {
					foreach($xml->files[0]->file as $file) {
						$this->filelist[] = new JExtension($file);
					}
				}
				return true;
			}
		}
	}
	
	class JExtension extends JObject {
		
		var $filename = '';
		var $type = '';
		var $id = '';
		var $client = 'site'; // valid for modules, templates and languages; set by default
		var $group =  ''; // valid for plugins
		
		function __construct($element=null) {
			if($element && is_a($element, 'JSimpleXMLElement')) {
				$this->type = $element->attributes('type');
				$this->id = $element->attributes('id');
				switch($this->type) {
					case 'module':
					case 'template':
					case 'language':
						$this->client = $element->attributes('client');
						$this->client_id = JApplicationHelper::getClientInfo($this->client,1);
						$this->client_id = $this->client_id->id;
						break;
					case 'plugin':
						$this->group = $element->attributes('group');
						break;
				}
				$this->filename = $element->data();
			}
		}
	}
}
