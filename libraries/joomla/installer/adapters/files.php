<?php
/**
 * @version		$Id:plugin.php 6961 2007-03-15 16:06:53Z tcp $
 * @package		JLibMan
 * @subpackage	Installer
 * @copyright 	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
  */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.installer.filemanifest');
jimport('joomla.base.adapterinstance');
jimport('joomla.backup.backup');

/**
 * Files installer
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.6
 */
class JInstallerFiles extends JAdapterInstance
{

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
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$this->set('name', $name);

        // Set element
        $manifestPath = $this->parent->getPath('manifest');
        $element = split(DS,$manifestPath);
        $element = $element[count($element) - 1];
        $element = preg_replace('/\.xml/', '', $element);
        $this->set('element', $element);

		// Get the component description
		$description = & $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '');
		}


        //Check if the extension by the same name is already installed
        if ($this->extensionExistsInSystem($name)) {
            // Package with same name already exists
            $this->parent->abort(JText::_('Files').' '.JText::_($this->route).': '.JText::_('Another extension with name already exists.'));
            return false;
        }


        //Populate File and Folder List to copy
        $this->populateFilesAndFolderList();


        // Before coping the files and folder, we should backup the files.
        // This will help us in restoring files when uninstalling the system.
        $backupId = $this->backupFilesAndFolders($this->get('name'), 'Backup stored by file installer for extension: '. $this->get('name'));
        
        if (! $backupId)
        {
            $this->parent->abort(JText::_('Files').' '.JText::_('Install').': '.JText::_('Could not Backup the files.'));
            return false;
        }

        $system_params  = new JParameter('');
        $system_params->set('batchid', $backupId);

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

        
        //Now that we have folder list, lets start creating them
        foreach ($this->folderList as $folder) {
            if (!JFolder::exists($folder)) {

                if (!$created = JFolder::create($folder)) {
                    JError::raiseWarning(1, JText::_('Files').' '.JText::_('Install').': '.JText::_('Failed to find source directory').': "'.$sourceFolder.'"');
                    // if installation fails, rollback
                    $this->parent->abort();
                    return false;
                }

                /*
                 * Since we created a directory and will want to remove it if we have to roll back
                 * the installation due to some errors, lets add it to the installation step stack
                 */
                if ($created) {
                    $this->parent->pushStep(array ('type' => 'folder', 'path' => $folder));
                }
            }

        }

        //Now that we have file list , lets start copying them
        $this->parent->copyFiles($this->fileList);



		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Add an entry to the extension table with a whole heap of defaults
		$row = & JTable::getInstance('extension');
		$row->set('name', $this->get('name'));
		$row->set('type', 'files');
		$row->set('element', $this->get('element'));
		$row->set('folder', ''); // There is no folder for files so leave it blank
		$row->set('enabled', 1);
		$row->set('protected', 0);
		$row->set('access', 0);
		$row->set('client_id', 0);
		$row->set('params', '');
        $row->set('system_data', $system_params->toString());
		$row->set('manifest_cache', '');
		if (!$row->store()) {
			// Install failed, roll back changes
			$this->parent->abort(JText::_('Files').' '.JText::_('Install').': '.$db->stderr(true));
			return false;
		}


		// Lastly, we will copy the manifest file to its appropriate place.
		$manifest = Array();
		$manifest['src'] = $this->parent->getPath('manifest');
		$manifest['dest'] = JPATH_MANIFESTS.DS.'files'.DS.basename($this->parent->getPath('manifest'));
		if (!$this->parent->copyFiles(array($manifest), true)) {
			// Install failed, rollback changes
			$this->parent->abort(JText::_('File').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}
		return true;
	}

	/**
	 * Custom update method
	 * @access public
	 * @return boolean True on success
	 * @since  1.5
	 */
	function update() {
		// since this is just files, an update removes old files
		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =& $manifest->document;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$installer = new JInstaller(); // we don't want to compromise this instance!
		$installer->uninstall('file', $name, 0);
		// ...and adds new files
		return $this->install();
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	string	$id	The id of the file to uninstall
	 * @param	int		$clientId	The id of the client (unused; files are global)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall($id, $clientId)
	{
		// Initialize variables
		$row	= null;
		$retval = true;
		$manifestFile = JPATH_MANIFESTS.DS.'files' . DS . $id .'.xml';

		// Because files may not have their own folders we cannot use the standard method of finding an installation manifest
		if (file_exists($manifestFile))
		{
			$manifest = new JFileManifest($manifestFile);
			// Set the plugin root path
			$this->parent->setPath('extension_root', JPATH_ROOT.DS.'files'.DS.$manifest->filename);

			$xml =& JFactory::getXMLParser('Simple');

			// If we cannot load the xml file return null
			if (!$xml->loadFile($manifestFile)) {
				JError::raiseWarning(100, JText::_('File').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));
				return false;
			}

			/*
			 * Check for a valid XML root tag.
			 * @todo: Remove backwards compatability in a future version
			 * Should be 'extension', but for backward compatability we will accept 'install'.
			 */
			$root =& $xml->document;
			if ($root->name() != 'install' && $root->name() != 'extension') {
				JError::raiseWarning(100, JText::_('File').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));
				return false;
			}

			$this->parent->removeFiles($root->getElementByPath('files'), -1);
			JFile::delete($manifestFile);

		} else {
			JError::raiseWarning(100, 'File Uninstall: Manifest File invalid or not found');
			return false;
		}

		// TODO: Change this so it walked up the path backwards so we clobber multiple empties
		// If the folder is empty, let's delete it
		if (JFolder::exists($this->parent->getPath('extension_root'))) {
			if (is_dir($this->parent->getPath('extension_root'))) {
				$files = JFolder::files($this->parent->getPath('extension_root'));
				if (!count($files)) {
					JFolder::delete($this->parent->getPath('extension_root'));
				}
			}
		}

		return $retval;
	}

	/**
	 * function used to check if extension is already installed
	 *
	 * @access	private
	 * @param	string	$name	The name of the extension to install
	 * @return	boolean	True if extension exists
	 * @since	1.6
	 */

    private function extensionExistsInSystem($name = null)
    {

		// Get a database connector object
		$db =& $this->parent->getDBO();

		$query = 'SELECT `extension_id`' .
				' FROM `#__extensions`' .
				' WHERE name = '.$db->Quote($name);

		$db->setQuery($query);

		try {
			$db->Query();
		} catch(JException $e) {
			// Install failed, roll back changes
			$this->parent->abort(JText::_('Files').' '.JText::_($this->route).': '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

        if (empty($id))
            return false;

        return true;
        
    }

	/**
	 * function used to populate files and folder list
	 *
	 * @access	private
	 * @return	boolean	none
	 * @since	1.6
	 */
    private function populateFilesAndFolderList()
    {

        // Initialize variable
        $this->folderList = array();
        $this->fileList = array();

		// Get fileset
		$eFileset =& $this->manifest->getElementByPath('fileset');

        // Set root folder names
        $packagePath = $this->parent->getPath('source');
        $jRootPath = JPath::clean(JPATH_ROOT);

        // loop through all elements and get list of files and folders
		foreach ($eFileset->children() as $eFiles)
		{
            // Check if the element is files element
            if ($eFiles INSTANCEOF JSimpleXMLElement && $eFiles->name() == 'files') {
                $folder = $eFiles->attributes("folder");
                $target = $eFiles->attributes("target");

                //Split folder names into array to get folder names. This will
                // help in creating folders
                $arrList = split("/|\\/", $target);

                $folderName = $jRootPath;
                foreach ($arrList as $dir) {
                    if(empty($dir)) continue ;

                    $folderName .= DS.$dir;
                    // Check if folder exists, if not then add to the array for folder creation
                    if (!JFolder::exists($folderName)) {
                        array_push($this->folderList, $folderName);
                    }
                }


                //Create folder path
                $sourceFolder = empty($folder)?$packagePath:$packagePath.DS.$folder;
                $targetFolder = empty($target)?$jRootPath:$jRootPath.DS.$target;

                //Check if source folder exists
                if (! JFolder::exists($sourceFolder)) {
                    JError::raiseWarning(1, JText::_('Files').' '.JText::_('Install').': '.JText::_('Failed to find source directory').': "'.$sourceFolder.'"');
                    // if installation fails, rollback
                    $this->parent->abort();
                    return false;
                }

                // Check if all children exists
                if (count($eFiles->children()) > 0) {
                    // loop through all filenames elements
                    foreach ($eFiles->children() as $eFileName)
                    {
                        if ($eFileName->name() == 'folder') {
                            $folderName = $targetFolder . DS . $eFileName->data();
                            array_push($this->folderList, $folderName);
                        } else {
                            $path['src'] = $sourceFolder . DS . $eFileName->data();
                            $path['dest'] = $targetFolder . DS . $eFileName->data();

                            array_push($this->fileList, $path);
                        }
                    }
                } else {
                    $files = JFolder::files($sourceFolder);
                    foreach ($files as $file) {
                        $path['src'] = $sourceFolder . DS . $file;
                        $path['dest'] = $targetFolder . DS . $file;

                        array_push($this->fileList, $path);
                    }

                }
            }
        }
    }

	/**
	 * function used to backup all existing files.
	 *
	 * @access	private
	 * @return	boolean	true on successful backup
	 * @since	1.6
	 */
    private function backupFilesAndFolders($name = '', $description = '')
    {
        $backup = new JBackup(JFactory::getDBO());
        $backup->setMode('backup');

        // Loop though the file list and them to backup
        foreach ($this->fileList as $fileName) {

            //Backup only those files which already exists in the system.
            if (file_exists($fileName['dest'])) {

                $backupFile = basename($fileName['dest']);

                $backup->addEntry(Array($backupFile), 'filesystem', Array('source' => $fileName['dest']));

            }
        }

        $backup->setBackupName($name);
        $backup->setBackupDescription($description);
        $backup->execute(false);

        $backupId = $backup->getBackupID();

        $backup->finish();

        return $backupId;

    }

}
