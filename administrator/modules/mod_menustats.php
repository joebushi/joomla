<?php
/**
* @version $Id: mod_menustats.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$query = "SELECT menutype, COUNT( id ) AS numitems"
. "\n FROM #__menu"
. "\n WHERE published = 1"
. "\n GROUP BY menutype"
;
$database->setQuery( $query );
$rows = $database->loadObjectList();

$i = 0;
foreach ($rows as $row) {
	$link = 'index2.php?option=com_menus&amp;menutype='. $row->menutype;
	
	$rows[$i]->num 	= $i + 1; 
	$rows[$i]->link = $link;
	
	$i++;
}
mod_statsScreens::view( $rows );


class mod_statsScreens {
	function view( &$rows ) {
		$tmpl =& moduleScreens_admin::createTemplate( 'mod_stats.html' );

		$tmpl->addObject( 'menus', $rows, 'row_' );

		$tmpl->displayParsedTemplate( 'mod_stats' );
	}
}
?>