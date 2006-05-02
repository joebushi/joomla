<?php
/**
* @version $Id: admin.update_client.php,v 1.4 2005/08/30 14:41:32 pasamio Exp $
* @package Mambo Update Client
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
// update_client logic code
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mainframe->getPath('admin_html') );
//require_once( $mainframe->getPath('class') );
require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
require_once( $mosConfig_absolute_path . '/includes/domit/dom_xmlrpc_client.php' );

mosFS::load('#mambo.installers');
mosFS::load('#mambo.update');

$bail = 0;	// Emergency recursion terminator

// Task Switcher
switch($task) {
	case "purgeCache":
		purgeCache($option);
		break;
	case "buildCache":
		buildCache($option);
		break;
	case "viewPackage":
 		viewPackage($option);
		break;
	case "listPackages":
		listPackages($option);
		break;
	case "addRemoteSite":
		addRemoteSite($option);
		break;
	case "update":
		update($option);
		break;
	case "editRemoteSite":
		editRemoteSite($option);
		break;
	case "upgrade":
		upgrade($option);
		break;
	default:
		listPackages($option);
		break;
}

// Function Implementation
/*function doPurgeCache() {
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
}*/


/**
 * Description: Shows details about a cache'd package
 */
function viewPackage($option) {
	global $database;
	$cid = intval(mosGetParam($_REQUEST, "cid", 0));
	if($cid != 0) {
		$query = "SELECT * FROM #__update_cache WHERE cacheid = $cid";
		$database->setQuery($query);				
		$database->loadObject($row);
	} else {
		$row = "Invalid Cache ID selected.";
	}
	HTML_update_server::viewPackage($option, $row);	
}

/**
 * Description: Gets a file name out of a url
 */
function getFilenameFromURL($url) {
	if(is_string($url)) {
		$parts = split('/', $url);
		return $parts[count($parts)-1];
	}
	return 0;
}



/**
 * Description: Cache directory
 */
/*function cacheDirectory($dir,$silent=0) {
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
								doAddPackage($dir . $file . '/update.xml');
								//echo "<p>filename: $file : filetype: " . filetype($dir . $file) . "</p>\n";
							} else if((($comfile != "." && $comfile != ".." && $comfile != "CVS" && $comfile != "tmpl") && ($comfile != "." || $comfile != "..") && is_dir($dir . $file . '/' . $comfile))) {
								//echo $dir . $file . '/' . $comfile . "<br>\n";								
								//cacheDirectory($dir . $file . '/' . $comfile);
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
}*/

/**
 * Description: Builds a cache of installed packages that are capable of upgrading.
 */
function buildCache($option,$silent=0) {
	global $database, $mosConfig_absolute_path;
	update_client_common::buildCache($silent);
	HTML_update_server::returnToList($option);
	HTML_update_server::emptyAdminForm($option);	
}


/**
 * Description: Displays a list of installed packages.
 */
function listPackages($option) {
        global $database, $mainframe, $mosConfig_list_limit;

	if(!isset($sectionid)) { $sectionid = ''; }
	
        $limit      = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
        $limitstart = $mainframe->getUserStateFromRequest( "view{$option}{$sectionid}limitstart", 'limitstart', 0 );


        // get the total number of records
        $database->setQuery( "SELECT count(*) FROM #__update_cache");
        $total = $database->loadResult();
        require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
        $pageNav = new mosPageNav( $total, $limitstart, $limit );

        $query = "SELECT * FROM #__update_cache LIMIT $pageNav->limitstart,$pageNav->limit";
        $database->setQuery( $query );
        $rows = $database->loadObjectList();

        if ($database->getErrorNum()) {
                echo $database->stderr();
                return false;
        }
        HTML_update_server::listPackages( $rows, $pageNav,$option);
}


/**
 * Description: Adds a remote site
 */
function addRemoteSite($option) {

}


/**
 * Description: Edits a remote site
 */
function editRemoteSite($option) {

}

/**
 * Description: Converts a string (url) to an xml rpc server
 */
function stringToXmlRpcServer(&$url) {
	if(!strpos($url, "http://")) {			
		$pos = strpos($url, "/", 7); // Offset to avoid "http://"
		if($pos === false) {
			return "Invalid path: $url<br>";
		} else {
			$host = substr($url,0,$pos);
			$path = substr($url,$pos);
			$rpc_url = new update_xmlrpcserver($host, $path);
			return $rpc_url;
		}
	} else { return "Invalid URL"; }
}

/**
 * Description: Updates installation candidates
 */
function update($option) {
	global $database, $mosConfig_absolute_path;
	$debug = 0;
	$updates = Array();	// Updates array
	echo "<p>Building XML-RPC Server list...";
	$query = "SELECT * FROM #__update_cache";
	$database->setQuery($query); $database->Query();
	if($database->getNumRows()) {
		echo "Done!</p>";
		$result = $database->loadObjectList();
		foreach($result as $product) {
			echo "<p>Updating product \"{$product->productname}\"...";
			$server_url = stringToXmlRpcServer($product->updateurl);
			if(is_object($server_url)) {				
				$client =& new dom_xmlrpc_client( $server_url->host, $server_url->path );
	                	$client->setResponseType( 'array' );
				
				if ($debug) {
					$client->setHTTPEvent( 'onRequest', true );
        	        	        $client->setHTTPEvent( 'onResponse', true );
				}
				if($product->productid) {
					$search = $product->productid;
				} else {
					$search = $product->productname;
				}
		                $myXmlRpc =& new dom_xmlrpc_methodcall( 'update.getCurrentVersion', $search );
        	        	$xmlrpcdoc = $client->send( $myXmlRpc );
				
				if (!$xmlrpcdoc->isFault()) {
					// convert returned array to object
                		       	$result = $xmlrpcdoc->getParam(0);
					if(is_string($result) && !update_client_common::in_string("error", strtolower($result))) {
						echo "<pre> Server Generated Error: " . print_r($result) . "</pre>";
					} else {
        		               		//foreach ($results as $i=>$result) {
						$cache_product = new updatecache($database);
						$cache_product->cacheid = $product->cacheid;
						$cache_product->load();
                                       		//$o = new updatecache();
						if($result['productid']) {
							$cache_product->productid = $result['productid'];
						}
						if($result['releaseid']) {
							$cache_product->releaseid = $result['releaseid'];
						}
						if($result['versionstring'] != $product->versionstring) {
							$cache_product->updated = 1;
							$cache_product->updatedversion = $result['versionstring'];
							$cache_product->releaseinfourl = $result['releasesurl'];
						} 
						$cache_product->lastupdate = date("Y-m-d");
						//print_r($cache_product);
						$cache_product->store();
        	        	       	}
	        	        }	
				echo "Done.</p>";
			} else {
				echo "Invalid server.";
			}
		}		
	} else {
		echo "Failed! No updateable elemnts found!<br>";
	}
	HTML_update_server::returnToList($option);
	HTML_update_server::emptyAdminForm($option);	
}

/**
 * Description: Gathers data before handing the upgrade process
 */
function upgrade($option) {
	global $database;
	set_time_limit(0);	// Make sure we don't over step our boundaries...or atleast reset them
	// Build a list of packages that need upgrading...
	$packages = mosGetParam($_REQUEST,"cid",null);
	if($packages != null) {
//		echo $packages;
 	  	foreach($packages as $package) {	// */
  	 		$query = "SELECT productid FROM #__update_cache WHERE cacheid = $package AND updated = 1";
			$database->setQuery($query);
			$result = $database->loadResult();
			if($result) {
 	 			processUpgrade($package);
 	 		} else {
 	 			echo '<p>The package selected is not valid or does not need updates at this time.</p>';
 	 		}
 	 	}
	}
	//buildCache($option,1);
	HTML_update_server::returnToList($option);
	HTML_update_server::emptyAdminForm($option);
}

/**
 * Description: Determines if a given package is installed
 */
function isInstalled($xmlrpcserver, $productid,$releaseid=0) {
	global $database;	
	if(is_object($xmlrpcserver)) {
		$xmlrpcserver = $xmlrpcserver->host . $xmlrpcserver->path;
	}
	if(is_string($xmlrpcserver)) { 
		if($releaseid) {			
//			$database->setQuery('SELECT productid, versionid, updateurl FROM #__update_cache WHERE productid = '. $productid . ' AND releaseid = '. $releaseid .' AND updateurl = "' . $xmlrpcserver . '"');
			$database->setQuery('SELECT count(*) FROM #__update_cache WHERE productid = '. $productid . ' AND releaseid = '. $releaseid .' AND updateurl = "' . $xmlrpcserver . '"');
//			echo '<pre>';
//			print_r(debug_backtrace());
//			echo '</pre>';
//			die("<p>Release ID has been actually used in this case! OMG: " . $database->getQuery() . "; <br></p>");
			echo "<p>Checking if installed with a valid release id</p>";
		} else {
//			$database->setQuery('SELECT productid, updateurl FROM #__update_cache WHERE productid = '. $productid . ' AND updateurl = "' . $xmlrpcserver . '"');
			$database->setQuery('SELECT count(*) FROM #__update_cache WHERE productid = '. $productid . ' AND updateurl = "' . $xmlrpcserver . '"');
		}
		$database->Query();
		if($database->loadResult()) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}

/**
 * Description: Determines if a given package version combo is installed.
 */
function isVersionInstalled($productname,$versionstring) {
	global $database;
	$database->setQuery('SELECT count(*) FROM #__update_cache WHERE productname = "' . $productname . '" AND versionstring = "' . $versionstring . '"');
//	echo '<p>' . $database->getQuery() . '</p>';
	$database->Query();
	if($database->loadResult()) {
		return true;
	} else {
		return false;
	}
}

/**
 * Description: Process and begin upgrades (including download)
 */
function processUpgrade($package) {
	global $database;
	echo "<p>Processing upgrade for $package</p>";
	$query = "SELECT * FROM #__update_cache WHERE cacheid = $package";
	$database->setQuery($query);
	$result = $database->loadAssocList();
	$result = $result[0];
	if($result) {
		// Get the server
		$xmlrpcserver = StringToXmlRpcServer($result['updateurl']);
		// Installs the package
		installPackage($xmlrpcserver, $result['productid'], $result['releaseid']);
		// Display a funky message
		echo '<p>You are up to date!</p>';
	} else {
		echo '<p>The database check failed.</p>';
		echo '<p>Result: ' . $result . '</p>';		
	}
}

/**
 * Description: Changes a string or part string product version to the int ids
 */
function productStringToInt($server_url, $productid,$versionid) {
	$debug = 0;
	if(is_string($server_url)) {
		$server_url = stringToXmlRpcServer($server_url);
	}
	if(is_object($server_url)) {				
		$client =& new dom_xmlrpc_client( $server_url->host, $server_url->path );
    	$client->setResponseType( 'array' );
				
		if ($debug) {
			$client->setHTTPEvent( 'onRequest', true );
			$client->setHTTPEvent( 'onResponse', true );
		}
	
		$myXmlRpc =& new dom_xmlrpc_methodcall( 'update.getVersion', $productid, $versionid );
      $xmlrpcdoc = $client->send( $myXmlRpc );
				
		if (!$xmlrpcdoc->isFault()) {
			// convert returned array to object
         $result = $xmlrpcdoc->getParam(0);
			if(is_string($result) && !update_client_common::in_string("error", strtolower($result))) {
				echo "<pre> Server Generated Error: " . print_r($result) . "</pre>";
			} else {
				return Array('productid'=>$result['productid'],'releaseid'=>$result['releaseid']);
//        		echo "Done.</p>";
        	}
		}
	} else {
		echo "Invalid server.";
	}
}

/**
 * Description: Gets the download url from the xml rpc server for the latest release of a given product
 */
function getDownloadUrl($xmlrpcserver, $productid, $versionid=0) {
//	echo "<p>Get Download URL: $xmlrpcserver, $productid, $versionid</p>";
	$debug = 0;
	$url = "";
	echo "<p>Getting the download URL for $productid:$versionid from $xmlrpcserver...";
	if(is_string($xmlrpcserver)) {
		$xmlrpcserver = StringToXmlRpcServer($xmlrpcserver);
	}
	if(is_object($xmlrpcserver)) {
		$xmlrpcclient =& new dom_xmlrpc_client($xmlrpcserver->host, $xmlrpcserver->path);
		$xmlrpcclient->setResponseType( 'array' );
		if($debug) {
			$client->setHTTPEvent( 'onRequest', true );
			$client->setHTTPEvent( 'onResponse', true );
		}
		
		if(is_string($productid) || is_string($versionid)) {
			$data = productStringToInt($xmlrpcserver, $productid,$versionid);
		}
		$productid = $data['productid'];
		$versionid = $data['releaseid'];
/*		echo '<p>Integer Data: ';
		print_r($data); echo '<br>';
		echo "$productid:$versionid";
		echo '</p>';*/
		if($versionid) {
			if($productid) {
				$xmlrpcrequest =& new dom_xmlrpc_methodcall( 'update.getDownloadUrl', 3, $productid,$versionid);
			} else {
				$xmlrpcrequest =& new dom_xmlrpc_methodcall( 'update.getDownloadUrl', 1, $versionid);
			}
		} else {
			if($productid) {
				$xmlrpcrequest =& new dom_xmlrpc_methodcall( 'update.getDownloadUrl', 2, $productid);
			} else {
				echo 'Error: Unable to determine an unique identifier</p>';
				return false;
			}
		}

		$xmlrpcdoc = $xmlrpcclient->send($xmlrpcrequest);
		if(!$xmlrpcdoc->isFault()) {
			$results = $xmlrpcdoc->getParam(0);
			$results = $results[0];
/*			echo '<pre>Stuff: ';
			print_r($results);
			echo '</pre>';*/
			if(is_string($results) && update_client_common::in_string("error", strtolower($results))) {
				echo '<pre> Server Generated Error: ';
				print_r($results);
				echo '</pre>';
			} else {
				if(isInstalled($xmlrpcserver, $productid,$versionid)) {
//					echo "<p>This package {$results['productid']}:{$results['releaseid']} is installed, upgrade package available: {$results['updateurl']}</p>";
					return $results['updateurl'];
				} else {
//					echo "<p>This package {$results['productid']}:{$results['releaseid']} is not installed, install package available: {$results['releaseurl']}</p>";
					return $results['releaseurl'];
				}
			}		
		} else {
			echo "<p>XML RPC Communication Error</p>";
		}
	}
	return 0;
}

/**
 * Description: Calculate dependencies. Takes the current releaes and works out whats needed. Recursive.
 */
function calculateDependencies($xmlrpcserver, $productid, $current_releaseid) {
//	echo '<p>Calculating Dependencies</p>';
	global $database,$mosConfig_absolute_path;
	$debug = 0;
	$dependencies = Array();	// Updates array
	if(is_object($xmlrpcserver)) {
		$client =& new dom_xmlrpc_client( $xmlrpcserver->host, $xmlrpcserver->path );
		$client->setResponseType( 'array' );
		if ($debug) {
			$client->setHTTPEvent( 'onRequest', true );
			$client->setHTTPEvent( 'onResponse', true );
		}

		$myXmlRpc =& new dom_xmlrpc_methodcall( 'update.getDependencies', $productid,$current_releaseid);
		$xmlrpcdoc = $client->send( $myXmlRpc );
				
		if (!$xmlrpcdoc->isFault()) {
			// convert returned array to object
			$results = $xmlrpcdoc->getParam(0);
			if(is_string($results) && update_client_common::in_string("error", strtolower($results))) {
				echo '<pre> Server Generated Error: ';
				print_r($results);
				echo '</pre>';
			} else {
				//echo "<br>This product ($productid:$current_releaseid) is dependent upon: <br>";
        			foreach ($results as $i=>$result) {
/*					echo '<p>Foreach Result: ';
					print_r($result); 
					echo '</p>';*/
//					echo '<p>Product Name: ' . $result['depprodname'] .':'.$result['depversionstring'] . '</p>';
					if(!isVersionInstalled($result['depprodname'],$result['depversionstring'])) {
						installPackage(StringToXmlRpcServer($result['remotesiteurl']),$result['depprodname'],$result['depversionstring']);
					} else {
//	 	      			   	echo 'The package is installed...not going to install. ';
	        			}
//	        			getDownloadUrl($xmlrpcserver, 0, $result['currentrelease']);
//					$current_dependency = new updatedependency($database);
//					$current_dependency->bind($result);
/*					echo '<p>Object: ';
					print_r($current_dependency);
					echo '</p>';*/							
   	     			}
   	     		}
  		}
		echo "Done.</p>";
	} else {
		echo "Invalid server.";
	} //*/
}

/**
 * Description: Does the installation of the file
 * Technical: This function uses multifunction recursion. First level fires
 * 				off the particular install, second level fires off a dependency
 *					check which then gathers a list of dependent packages and
 *					executes installPackage to install them, which then checks if
 *					they themself have any unmet or unresolved packages.
 *					Eventually the original package gets installed...
 *					Worst comes to worst, the depedency checker mambot will cancel
 *					any install that has unmet dependencies (in theory anyway).
 *					This could of course be from server malfunctions...
 */
function installPackage($xmlrpcserver, $productid, $releaseid) {
	global $bail;
	$bail++;
	if($bail > 10) {
		die("Warning: Script is terminating due to excessive loops!");
	}//*/
	// Multifunction recursion starts Now!
	echo '<table cellpadding="5" cellspacing="5"><tr><td>';
	echo "Installing package $productid:$releaseid from $xmlrpcserver...<br>";
	echo 'Checking Dependencies...';
	calculateDependencies($xmlrpcserver, $productid, $releaseid);
	echo '<p>Dependencies have been checked, proceeding with install ('.$productid.':'.$releaseid.')...';
	$url = getDownloadUrl($xmlrpcserver, $productid, $releaseid);		
	if($url) {
		echo 'Download URL: '.$url.'. <br>Target: ' . getFilenameFromUrl($url);				
		$installer = new mosInstallerFactory();
		$installer->webInstall($url);
	} else {
		echo 'Download link is blank.';
	}
	echo '</td></tr></table>';
}

?>
