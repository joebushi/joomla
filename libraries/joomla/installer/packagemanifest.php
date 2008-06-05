<?php
/**
 * @version		$Id: installer.php 9783 2007-12-31 14:56:55Z pasamio $
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.filesystem.file' );

/**
 * Joomla! Package Manifest File
 *
 * @author 		Sam Moffatt <pasamio@gmail.com> 
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.6
 */
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
			$this->fullname = $xml->fullname[0]->data();
			$this->update = $xml->update[0]->data();
			$this->authorurl = $xml->authorUrl[0]->data();
			$this->author = $xml->author[0]->data();
			$this->authoremail = $xml->authorEmail[0]->data();
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
