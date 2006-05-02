<?php
/**
* @version $Id: mospage.btn.php,v 1.1 2005/08/25 14:21:49 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onCustomEditorButton', 'botMosPageButton' );

/**
* mospage button
* @return array A two element array of ( imageName, textToInsert )
*/
function botMosPageButton() {
	global $mosConfig_live_site, $option;

	$button = array( '', '' );
	// button is only active in specific content components
	if ( $option == 'com_content' || $option == 'com_typedcontent' ) {
		$button = array( 'mospage.gif', '{mospagebreak}' );
	}

	return $button;
}
?>