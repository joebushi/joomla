<?php
/**
* @version $Id: factory.php,v 1.2 2005/08/30 14:43:22 pasamio Exp $
* @package Mambo Update
* @copyright (C) 2005 Samuel Moffatt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

mosFS::load('#mambo.update.client');
mosFS::load('@domit');

class update_client_common {
	function in_string($needle, $haystack, $insensitive = 0) {
		if ($insensitive) {
			return (false !== stristr($haystack, $needle)) ? true : false;
		} else {
			return (false !== strpos($haystack, $needle))  ? true : false;
		}
	}

	function isInstalled($packageid,$versioid,$remotesite) {
	
	}

	function doPurgeCache() {
	        global $database;
	        $query = "DELETE FROM #__update_cache WHERE remoteapp != 1";
	        $database->setQuery($query);
	        $database->Query() or die($database->getErrorMsg());
	}

	function doAddPackage($xmlfile) {
        	global $database;
		//echo "<p>Adding a package for the file $xmlfile</p>";
		$xmlDoc =& new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
		if(!$xmlDoc->loadXML($xmlfile, false, true)) {
			echo "<p>Error Loading XML File for $xmlfile</p>";
			continue;
		}
	
		$element = &$xmlDoc->documentElement;
		
		if($element->getTagName() != 'mosupdate') {
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
	}
	
	/**
	* Description: Cache directory
	*/
	function cacheDirectory($dir,$silent=0) {
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(($file != "." && $file != ".." && $file != "CVS" && $file != "tmpl") && (is_dir($dir . $file))) {	
	//					echo "<p>Searching '$file' for entries: ";
						if($cdir = opendir($dir . $file)) {
							$found = 0;
							while(($comfile = readdir($cdir)) !== false) {
								//echo "<p>$comfile</p>";
								if($comfile == "update.xml" ) {								
									if(!$silent) { echo "<P>Update File Found for $file, adding to cache.</P>"; }
									$found = 1;
									update_client_common::doAddPackage($dir . $file . '/update.xml');
									//echo "<p>filename: $file : filetype: " . filetype($dir . $file) . "</p>\n";
								} else if((($comfile != "." && $comfile != ".." && $comfile != "CVS" && $comfile != "tmpl") && ($comfile != "." || $comfile != "..") && is_dir($dir . $file . '/' . $comfile))) {
									//echo $dir . $file . '/' . $comfile . "<br>\n";								
									update_client_common::cacheDirectory($dir . $file . '/' . $comfile);
								}
							}	
							if(!$found) {
	//							echo "None found.";
							}				
							closedir($cdir);
						} else {
	//						echo "None found.";
						}
	//					echo "</p>";
					} else {
						//echo "<p>Failed to open directory for $file.</p>";
					}
				}
				closedir($dh);
			} 	
		} else {
			echo "<p>Failed to find directory: $dir</p>";
		}
	}	
	
	/**
	* Description: Builds a cache of installed packages that are capable of upgrading.
	*/
	function buildCache($silent=0) {
		global $database, $mosConfig_absolute_path;
		update_client_common::doPurgeCache();
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/administrator/components/',$silent);
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/administrator/modules/',$silent);
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/administrator/templates/',$silent);
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/mambots/',$silent);
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/templates/',$silent);
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/modules/',$silent);
		update_client_common::cacheDirectory($mosConfig_absolute_path . '/libraries/',$silent);	
	}	
	
}
?>
	
