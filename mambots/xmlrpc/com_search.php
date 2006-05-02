<?php
/**
* @version $Id: com_search.php,v 1.1 2005/08/25 14:23:44 johanjanssens Exp $
* @package Rambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onGetWebServices', 'wsGetSearchWebServices' );

/**
* @return array An array of associative arrays defining the available methods
*/
function wsGetSearchWebServices() {
	return array(
		array(
			'name' => 'search.site',
			'method' => 'wsSearchSite',
			'help' => 'Searches a remote site',
			'signature' => array('string','string','string') // ??
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
function wsSearchSite( $searchword, $phrase='', $order='' ) {
	global $database, $my, $acl, $_LANG, $_MAMBOTS, $mosConfig_live_site;

	if (!defined( '_MAMBOT_REMOTE_SEACH')) {
		// flag that the site is being searched remotely
		define( '_MAMBOT_REMOTE_SEACH', 1 );
	}

	$searchword = $database->getEscaped( trim( $searchword ) );
	$phrase = '';
	$ordering = '';

	$_MAMBOTS->loadBotGroup( 'search' );
	$results = $_MAMBOTS->trigger( 'onSearch', array( $searchword, $phrase, $ordering ) );

	foreach ($results as $i=>$rows) {
		foreach ($rows as $j=>$row) {
			$results[$i][$j]->href = $mosConfig_live_site . '/' . $row->href;
			$results[$i][$j]->text = mosPrepareSearchContent( $row->text );
		}
	}
	return $results;

	//return new dom_xmlrpc_fault( '-1', 'Fault' );
}

?>