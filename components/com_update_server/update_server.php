<?php
/**
* @version $Id: update_server.php,v 1.1 2005/08/25 14:18:15 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

//require_once( $mainframe->getPath( 'front_html', 'com_update_server' ) );

$limit 		= intval( mosGetParam( $_REQUEST, 'limit', '' ) );
$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

$now = date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 );

// update_server logic code
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once($mainframe->getPath('front_html'));

// Task Switcher
switch($task) { 
	case "showProduct":
		showProduct($option,$Itemid);
		break;

	case "listReleases":
		listReleases($option,$Itemid);
		break;

	case "showRelease":
		showRelease($option,$Itemid);
		break;

	case "listProducts":		
	default:
		listProducts($option,$Itemid);
		break;

}

// Function Implementation

/**
 * Description: Lists available products
 */
function listProducts($option,$Itemid) {
	global $database;
	
	$database->setQuery("SELECT * FROM #__update_product WHERE published = 1");
	$database->Query() or die("SQL Product listing Failure: " . $database->getErrorMsg() . " with query: " . $database->getQuery());
	$rows = $database->loadObjectList();
	HTML_update_server::listProducts($option,$Itemid,$rows);
}


/**
 * Description: Shows a specific product
 */
function showProduct($option,$Itemid) {
	global $database;
	$pid = mosGetParam($_REQUEST,"productid");
	$database->setQuery("SELECT * FROM #__update_product WHERE productid = $pid");
	$database->Query() or die("SQL Product listing Failure: " . $database->getErrorMsg() . " with query: " . $database->getQuery());	
	$database->getQuery();	
	$database->loadObject($row);
	$database->setQuery("SELECT * FROM #__update_releases WHERE productid = $pid");
	$database->Query() or die("SQL Release listing Failure: " . $database->getErrorMsg() . " with query: " . $database->getQuery());	
	$database->getQuery();	
	$releases = $database->loadObjectList();
	
	HTML_update_server::showProduct($option,$Itemid,$row, $releases);
}


/**
 * Description: Lists the releases for a product
 */
function listReleases($option,$Itemid) {

}


/**
 * Description: Shows a specific release
 */
function showRelease($option,$Itemid) {
	global $database;
	$pid = mosGetParam($_REQUEST,"releaseid");
	$database->setQuery("SELECT a.*, b.* FROM #__update_releases AS a, #__update_product AS b WHERE a.productid = b.productid AND a.releaseid = $pid");
	$database->Query() or die("SQL Release listing Failure: " . $database->getErrorMsg() . " with query: " . $database->getQuery());	
	$database->getQuery();	
	$database->loadObject($row);
	HTML_update_server::showRelease($option,$Itemid,$row);
	

}


?>
