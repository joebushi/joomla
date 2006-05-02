<?php
/**
* @version $Id: functions.php,v 1.1 2005/08/25 14:18:15 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed!' );


function listUpdates($product) {
	
	
	return array();	// Blank return...for now
}

function listVersions($product) {

	return array();	// Blank return...also only for now
}

function listProducts() {
	global $database;
	$query = "SELECT * FROM #__updater_products";
	$database->setQuery($query);
	return array('Updater','Update');
}
?>
