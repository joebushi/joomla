<?php
/**
* @version $Id: mambo_update.php,v 1.1 2005/08/25 14:23:44 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Include the Update Server Function library
global $mosConfig_live_site;
include_once($mosConfig_live_site . "/components/com_update_server/functions.php");
$_MAMBOTS->registerFunction( 'onGetWebServices', 'wsGetUpdaterWebServices' );

/**
* @return array An array of associative arrays defining the available methods
*/
function wsGetUpdaterWebServices() {
	return array(
		array(
			'name' => 'update.searchProducts',
			'method' => 'wsSearchProducts',
			'help' => 'Searches Products on site',
			'signature' => array('string') // Signature of method
		),
		array(
			'name' => 'update.listProducts',
			'method' => 'wsListProducts',
			'help' => 'Lists available products on site',
			'signature' => array()
		),
		array(
			'name' => 'update.getProduct',
			'method' => 'wsGetProduct',
			'help' => 'Gets information about a specific product',
			'signature' => array('string') // Signature of method (still);
		),
		array(
			'name' => 'update.getCurrentVersion',
			'method' => 'wsGetCurrentVersion',
			'help' => 'Retrieves the current version of a product',
			'signature' => array('string') // Signature of method (still);
		),	
		array(
			'name' => 'update.getVersion',
			'method' => 'wsGetVersion',
			'help' => 'Retrieves the details for a version of a product',
			'signature' => array('string','string') // Signature of method (still);
		),		
		array(
			'name' => 'update.getDependencies',
			'method' => 'wsGetDependencies',
			'help' => 'Retrieves the dependencies of a product version',
			'signature' => array('string','string') // Signature of method (really?)
		),
		array(
			'name' => 'update.getDownloadUrl',
			'method' => 'wsGetDownloadUrl',
			'help' => 'Returns the update and relase download urls for the package.',
			'signature' => array('string','string') // Signature of method
		),
	
	);
}

/**
* Remote Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
*/
function wsSearchProducts( $searchword) {
	global $database, $my, $acl, $_LANG, $_MAMBOTS, $mosConfig_live_site;

	if (!defined( '_MAMBOT_REMOTE_SEARCH')) {
		// flag that the site is being remotely accessed
		define( '_MAMBOT_REMOTE_SEARCH', 1 );
	}

	$searchword = $database->getEscaped( trim( $searchword ) );
	$database->setQuery("SELECT * FROM #__update_product WHERE (productname LIKE '%$searchword%' OR productdescription LIKE '%$searchword%') AND published = 1");
	$database->Query() or die("<string>Failure issuing query to database: " . $database->getErrorMsg() . "</string>");
	$results = $database->loadObjectList();
	return $results;

	//return new dom_xmlrpc_fault( '-1', 'Fault' );
}

function wsListProducts() {
	global $database, $my, $acl, $_LANG, $mosConfig_live_site;	
	if(defined('_MAMBOT_REMOTE_QUERY')) {
		define('_MAMBOT_REMOTE_QUERY',1);
	}	
	$database->setQuery("SELECT * FROM #__update_product WHERE published = 1");
	$database->Query() or die("<string>Error: Query Failure</string>");//return new dom_xmlrpc_fault('-2','SQL Failure');
	$result = $database->loadObjectList();	
	return $result;
	
}

function wsGetProduct($productid) {
	global $database, $my, $acl, $_LANG, $mosConfig_live_site;
	
	if(defined('_MAMBOT_REMOTE_QUERY')) {
		define('_MAMBOT_REMOTE_QUERY',1);
	}	
	if(!$productid) { die("<string>Error: Not a valid product ID ($productid)</string>"); }
	if(is_string($productid)) {
		$database->setQuery("SELECT * FROM #__update_product WHERE productname = '$productid' AND published = 1 LIMIT 0,1");
	} else {
		$database->setQuery("SELECT * FROM #__update_product WHERE productid = '$productid' AND published = 1 LIMIT 0,1");
	}
	$database->Query() or die("<string>Error: Query Failure</string>");//return new dom_xmlrpc_fault('-2','SQL Failure');	
	if($database->getNumRows() != 1) { die("<string>Error: Please check your product ID is valid.</string>"); }
	$database->loadObject($result);
	//die("<string>" . $database->getQuery() . "</string>");
	return $result;
}

function wsGetCurrentVersion($productid) {
	global $database, $my, $acl, $_LANG, $mosConfig_live_site;
	
	if(defined('_MAMBOT_REMOTE_QUERY')) {
		define('_MAMBOT_REMOTE_QUERY',1);
	}	
	if(!$productid) { die("<string>Error: Not a valid product ID ($productid)</string>"); }
	$database->setQuery("SELECT DISTINCT a.productid, a.releaseid, a.versionstring, a.releasesurl, a.updateurl, a.releaseurl FROM #__update_releases AS a, #__update_product AS b WHERE b.productid = '$productid' AND a.productid = b.productid  AND a.published = 1  AND b.published = 1 ORDER BY a.releaseid DESC LIMIT 0,1");
	$database->Query() or die("<string>Error: Query Failure</string>");//return new dom_xmlrpc_fault('-2','SQL Failure');	
	if($database->getNumRows() != 1) { die("<string>Error: Please check your product ID is valid.".$database->getNumRows()."</string>"); }
	$database->loadObject($result);	
	//die("<string>" . $database->getQuery() . "</string>");
	return $result;
}

function wsGetVersion($productid,$versionstring) {
	global $database, $my, $acl, $_LANG, $mosConfig_live_site;
	if(defined('_MAMBOT_REMOTE_QUERY')) {
		define('_MAMBOT_REMOTE_QUERY',1);
	}	
	if(!$productid) { die("<string>Error: Not a valid product ID ($productid)</string>"); }
	$database->setQuery("SELECT DISTINCT a.productid, a.releaseid, a.versionstring, a.releaseurl, a.updateurl, a.releaseurl FROM #__update_releases a, #__update_product b WHERE (b.productid = '$productid' OR b.productname = '$productid') AND (a.versionstring = '$versionstring' OR a.releaseid = '$versionstring') AND a.productid = b.productid  AND a.published = 1 AND b.published = 1 ORDER BY a.releaseid DESC LIMIT 0,1");
//	$database->setQuery("SELECT DISTINCT a.productid, a.releaseid, a.versionstring, a.releasesurl, a.updateurl, a.releaseurl FROM #__update_releases AS a, #__update_product AS b WHERE b.productid = '$productid' AND a.versionstring = '$versionstring' AND a.productid = b.productid  AND a.published = 1 AND b.published = 1 ORDER BY a.releaseid DESC LIMIT 0,1");
	$database->Query() or die("<string>Error: Query Failure</string>");//return new dom_xmlrpc_fault('-2','SQL Failure');	
	if($database->getNumRows() != 1) { die("<string>Error: Please check your product ID is valid.".$database->getNumRows()."</string>"); }
	$database->loadObject($result);	
	//die("<string>" . $database->getQuery() . "</string>");
	return $result;
}
function wsGetDependencies($productid,$releaseid) {
	global $database;
	if(!$releaseid) { die('<string>Error: Not a valid release ID ('. print_r($releaseid) . ')</string>'); }
//	$database->setQuery("SELECT count(*) FROM #__update_dependencies a, #__update_remotesite b, #__update_releases c, #__update_product d WHERE a.currentrelease = c.releaseid AND c.published = 1 AND c.productid = d.productid AND (a.currentrelease = '$releaseid' OR (c.versionstring = '$releaseid' AND d.productname = '$productid')) AND a.depremotesite = b.remotesiteid");
//	$count = $database->loadResult();
//	if($count) {
		$database->setQuery("SELECT a.*,b.remotesiteurl FROM #__update_dependencies a, #__update_remotesite b, #__update_releases c, #__update_product d WHERE a.currentrelease = c.releaseid AND c.published = 1 AND c.productid = d.productid AND (a.currentrelease = '$releaseid' OR (c.versionstring = '$releaseid' AND d.productname = '$productid')) AND a.depremotesite = b.remotesiteid");
		$database->Query() or die('<string>Error: Query Failure: ' . stripslashes($database->getErrorMsg()) . '. &lt;br&gt;Query: ' . stripslashes($database->getQuery()) . '</string>');
		$error 	= 0;		$result 	= $database->loadAssocList();
		if($database->getNumRows()) {
			return $result;
		} else {
			return '<string>No dependencies found.</string>';
		}//*/
//	} else {
//		die('<string>Error: No dependencies found. Query: '. $database->getQuery() .'</string>');
//	}

}

function wsGetDownloadUrl($type,$param1,$param2=0) {
	global $database;
	$query = '';
	switch($type) {
		case 1:	// Release ID Only
			$query = "SELECT updateurl, releaseurl, productid, releaseid FROM #__update_releases WHERE releaseid = '$param1' AND published = 1 ORDER BY releaseid DESC";
			break;
		case 2:	// Product ID Only
			$query = "SELECT updateurl, releaseurl, productid, releaseid FROM #__update_releases WHERE productid = '$param1' AND published = 1 ORDER BY releaseid DESC";
			break;
		case 3:	// Product ID and Release ID
			$query = "SELECT updateurl, releaseurl, productid, releaseid FROM #__update_releases WHERE productid = '$param1' AND releaseid = '$param2'  AND published = 1";
			break;
	}
	if($query) {
		$database->setQuery($query);
		return $database->loadAssocList();
	}
	return 'Error: Unable to determine appropriae download URL';
}
?>
