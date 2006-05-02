<?php
/**
* @version $Id: dependency.check.php,v 1.3 2005/09/16 14:24:52 pasamio Exp $
* @package Mambo Update
* @copyright (C) 2005 Samuel Moffatt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

//require_once($mosConfig_absolute_path . "/includes/patTemplate/patError.php");
$_MAMBOTS->registerFunction( 'onBeforeInstall', 'botCheckDependencies_Install');
$_MAMBOTS->registerFunction( 'onBeforeUninstall', 'botCheckDependencies_Uninstall');

/**
 * Approves or denies an installed based on dependency check
 *
 * These functions checks the installed elements table to check that the dependencies have bee resolved
 */
function botCheckDependencies_Install($dir) {
	if(false !== ($file = findDepFile($dir))) {
		return depCheckFile($file);
	}
	return "No dependency data found, check passed.";	
} 

function botCheckDependencies_Uninstall($id,$type) {
	global $mosConfig_absolute_path,$database;
//	die($id.'<br>'.$type);
//	return botCheckDependencies("uninstall", $component, $type);	
	$dir = $mosConfig_absolute_path;
	switch($type) {
		case 'component':
			$dir .= '/administrator/components/';
			$query = 'SELECT option FROM #__components WHERE id = ' . $id;
			break;
// 		case 'mambot':
// 			$dir .= '/mambots/';
// 			$query = 'SELECT folder FROM #__mambots WHERE element = '. $id;
// 			break;
// 		case 'module':
// 			$query = "SELECT IF(client_id = 1,concat('/administrator/modules/',module,'/'),concat('/modules/',module,'/')) as folder FROM #__modules WHERE id = $id";
// 			break;
// 		case 'template':
//			
// 			break;
// 		case 'language':
//			
// 			break;
		default: // return false;
	}

	return "Dependency check complete!";
//	return botCheckDependencies("install", null, $directory);
	
} 

function findDepFile($dir,$name='') {
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..' && is_dir($dir . '/' . $file)) {
					if(false !== ($res = findDepFile($dir . '/' . $file))) {
						return $res;
					}
				}
				if($file == 'dependencies.xml' || $file == "$name-dependencies.xml") {
					return depCheckFile($dir . '/' . $file);
				}
			}
		}
	}
	return false;	
}

function depCheckFile($xmlfile='') {
        	global $database;
		if($xmlfile != '') { echo '<p>XML File name not passed...ignoring</p>'; return true; }
		//echo "<p>Adding a package for the file $xmlfile</p>";
		$xmlDoc =& new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
		if(!$xmlDoc->loadXML($xmlfile, false, true)) {
			echo "<p>Error Loading XML File for $xmlfile</p>";
			return;
		}
	
		$element = &$xmlDoc->documentElement;
		
		if($element->getTagName() != 'mosdependencies') {
			echo "<p>Invalid XML Document found! ($xmlfile)</p>";
			return false;
		}
		$row = new updatecache($database);
		$row->type =  $element->getAttribute("type");
		
		$element = &$xmlDoc->getElementsByPath('name', 1);
		$row->productname = $element ? $element->getText() : 'Unknown';
	
		$element = &$xmlDoc->getElementsByPath('versionstring', 1);
		$row->versionstring = $element ? $element->getText() : 'Unknown';
	
		$element = &$xmlDoc->getElementsByPath('productid', 1);
		$row->productid = $element ? $element->getText() : '0';
		$element = &$xmlDoc->getElementsByPath('server', 1);
		$row->updateurl = $element ? $element->getText() : 'Unknown';
		$row->store() or die("<p>Failed to store: " . $row->getError()."</p>");
		//echo "<p>Done!</p>";
		return true;	
//	return new patError(0,0,"Dependency Check Failed: I don't like you ($directory)",0);
//	return "Succesfully passed dependency checks";
} 