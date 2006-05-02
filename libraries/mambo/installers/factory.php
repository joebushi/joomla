<?php
/**
* @version $Id: factory.php,v 1.4 2005/08/28 06:43:06 pasamio Exp $
* @package Mambo Update Client
* @subpackage Installers
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

mosFS::load('#mambo.installers.installer');
mosFS::load('#mambo.installers.components');
mosFS::load('#mambo.installers.mambots');
mosFS::load('#mambo.installers.templates');
mosFS::load('#mambo.installers.modules');


class mosInstallerFactory {
	var $_class 	= null;		// The class
	var $_result 	= null; 	// The last result
	var $_type 	= null; 	// Its type e.g. component
		
	function mosInstallerFactory($name=null) {	
		if($name) {
			$this->createClass($name);
		}
	}
	
	function &getClass() {
		return $this->_class;
	}
	
	function &createClass($name) {
		$success = false;
		$this->_type = $name;
		switch($name) {
			case 'component':					
		                $this->_class = new mosComponentInstaller();
				$success = true;
				break;
			case 'module':
				$this->_class = new mosModuleInstaller();
				$success = true;
				break;
			case 'mambot':
				$this->_class = new mosMambotInstaller();
				$success = true;
				break;
			case 'template':
				$this->_class = new mosTemplateInstaller();
				$success = true;
				break;
			case 'language':
				$this->_class = new mosLanguageInstaller();
				$success = true;
				break;
			default:
				die("<p>Attept to create a '$name' installer failed</p>");
				break;
		}
		$this->_result = $success;
		return $this->_class;
	}

	function autoInstall($method,$data) {
		// Provide an auto install system. Generic should work for all cases,
		// but a switch statement is provided for future possibilities
		switch($this->_type) {
			default: $this->autoInstallGeneric($method, $data); break;
		}
	}
	
	function webInstall($url) {
		$processor = new mosInstaller();
		$location = $processor->downloadPackage($url);
		$processor->extractArchive();
		$type = $this->detectType($processor->unpackDir());		
		$this->createClass($type);	
		//$this->_class->allowOverwrite(1);	
		return $this->autoInstallGeneric('directory',$processor->unpackDir());
	}
	
	function detectType( $location ) {
		global $_LANG;

		$found = false;
		// Search the install dir for an xml file
		$files = mosFS::listFiles( $location, '\.xml$', true, true );

		if (count( $files ) > 0) {
			mosFS::load( '@domit' );

			foreach ($files as $file) {
				$xmlDoc = new DOMIT_Lite_Document();
				$xmlDoc->resolveErrors( true );

				if (!$xmlDoc->loadXML( $file, false, true )) {
					return false;
				}
				$root = &$xmlDoc->documentElement;
					
				if ($root->getTagName() != "mosinstall") {
					continue;
				}
				//echo "<p>Looking at file $file, I consider it to be a valid installer file.</p>";
				return $root->getAttribute( 'type' );
				
			}
			$this->setError( 1, $_LANG->_( 'ERRORNOTFINDMAMBOXMLSETUPFILE' ) );
			return false;
		} else {
			$this->setError( 1, $_LANG->_( 'ERRORNOTFINDXMLSETUPFILE' ) );
			return false;
		}
		return false;
	}
	
	function autoInstallGeneric($method=null,$data=null,$type=null) {
		$msg = "SUCCESS";
		if($type) {
			$this->createClass($type);
		}
		$installer = $this->_class; // Class should have been set already by initializer or done manually
		switch($method) {	
			case 'upload':
				$userfile = mosGetParam( $_FILES, 'userfile', null );
				if (!$installer->uploadArchive( $userfile )) {
					$msg = $installer->error();					
				}
				if (!$installer->extractArchive()) {
					$msg = $installer->error();
				}
				break;
			default:
				$installer->installDir( $data );
				break;
                }
                if (!$installer->install()) {
			$installer->cleanupInstall();
			$msg = $installer->error();
                }
                $installer->cleanupInstall();
		//return $msg;
		return $installer;
	}
}

