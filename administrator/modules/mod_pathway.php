<?php
/**
* @version $Id: mod_pathway.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_sitename;

if ($option != '') {
	$html = '';

	$html .= '<div class="pathway">';	
	$html .= '<a href="'. $mosConfig_live_site .'/administrator/index2.php">';
	$html .= '<strong>' . $mosConfig_sitename . '</strong>';
	$html .= "</a>";

	if ($option != '') {
		$html .= " / ";
		// try to miss edit functions
		if ($task != '' && !eregi( 'edit', $task )) {
			$html .= "<a href=\"index2.php?option=$option\">$option</a>";
		} else {
			$html .= $option;
		}
	}

	if ( $task != '' ) {
		$html .= " / $task";
	} else {
		$html .= " /";
	} 
	$html .= '</div>';
	echo $html;
}
?>