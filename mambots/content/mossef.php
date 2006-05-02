<?php
/**
* @version $Id: mossef.php,v 1.1 2005/08/25 14:21:34 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosSef' );

/**
* Converting internal relative links to SEF URLs
*
* <b>Usage:</b>
* <code><a href="...relative link..."></code>
*/
function botMosSef( $published, &$row, &$params, $page=0 ) {
	global $mosConfig_absolute_path, $mosConfig_live_site;

	// define the regular expression for the bot
	$regex = "#href=\"(.*?)\"#is";

	// perform the replacement
	$row->text = preg_replace_callback( $regex, 'botMosSef_replacer', $row->text );

	return true;
}
/**
* Replaces the matched tags
* @param array An array of matches (see preg_match_all)
* @return string
*/
function botMosSef_replacer( &$matches ) {
	if ( substr($matches[1],0,1)=="#" ) {
		// anchor
		$temp = split("index.php", $_SERVER['REQUEST_URI']);
		return 'href="'.sefRelToAbs("index.php".@$temp[1]).$matches[1].'"';
	} else {
		return 'href="'.sefRelToAbs($matches[1]).'"';
	}
}
?>