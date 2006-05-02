<?php
/**
* @version $Id: update_trigger.php,v 1.1 2005/08/25 14:23:44 johanjanssens Exp $
* @package Mambo Update Client
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Include the Update Server Function library
global $mosConfig_live_site;
include_once($mosConfig_live_site . "/components/com_update_client/functions.php");
include_once($mosConfig_live_site . "/includes/mambo/registry/main.php");
include_once($mosConfig_live_site . "/includes/mambo/update/main.php");
$_MAMBOTS->registerFunction( 'onGetWebServices', 'wsGetUpdateTriggerWebServices' );

/**
* @return array An array of associative arrays defining the available methods
*/
function wsGetUpdaterWebServices() {
	return array(
		array(
			'name' => 'updatetrigger.ping',
			'method' => 'wsPing',
			'help' => 'Pings the trigger service starting an upgrade',
			'signature' => array('string') // Signature of method
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
function wsPing( $ping_key) {
	global $database, $my, $acl, $_LANG, $_MAMBOTS, $mosConfig_live_site;	$key = getEntry('update','configuration/ping/key');
	if($key != $ping_key) {
		die('<string>Authentication Error: You do not have the appropriate key for this server.</string>');
	} else {
		$result = doAutoUpdate();
		if(is_string($result)) {
			$result .= '<string>'.$result.'</string>';
		}
		return $result;
	}
	//return new dom_xmlrpc_fault( '-1', 'Fault' );
}

?>
