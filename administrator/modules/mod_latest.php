<?php
/**
* @version $Id: mod_latest.php,v 1.2 2005/08/29 15:52:19 alekandreev Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$query = "SELECT a.id, a.sectionid, a.title, a.created, u.name, a.created_by_alias, a.created_by"
. "\n FROM #__content AS a"
. "\n LEFT JOIN #__users AS u ON u.id=a.created_by"
. "\n WHERE a.state <> -2 AND a.active = 1"
. "\n ORDER BY created DESC"
;
$database->setQuery( $query, 0, 10 );
$rows = $database->loadObjectList();

$i=0;
foreach ($rows as $row) {
	if ( $row->sectionid == 0 ) {
		$link = 'index2.php?option=com_typedcontent&amp;task=edit&amp;id='. $row->id;
	} else {
		$link = 'index2.php?option=com_content&amp;task=edit&amp;id='. $row->id;
	}

	$linkA = '#';
	if ( $acl->acl_check( 'com_users', 'manage', 'users', $my->usertype ) ) {		
		if ( $row->created_by_alias ) {
			$author = $row->created_by_alias;
		} else {
			$linkA 	= 'index2.php?option=com_users&amp;task=editA&amp;id='. $row->created_by;
			$author = htmlspecialchars( $row->name, ENT_QUOTES );
		}
	} else {
		if ( $row->created_by_alias ) {
			$author = $row->created_by_alias;
		} else {
			$author = htmlspecialchars( $row->name, ENT_QUOTES );
		}
	}
	
	$rows[$i]->num 		= $i + 1; 
	$rows[$i]->link 	= $link; 
	$rows[$i]->linkA 	= $linkA; 
	$rows[$i]->title 	= htmlspecialchars($row->title, ENT_QUOTES);
	$rows[$i]->date 	= mosFormatDate( $row->created, $_LANG->_( 'DATE_FORMAT_LC3' ) ); 
	$rows[$i]->author 	= $author;
	
	$i++;
}
mod_latestScreens::view( $rows );


class mod_latestScreens {
	function view( &$rows ) {
		$tmpl =& moduleScreens_admin::createTemplate( 'mod_latest.html' );

		$tmpl->addObject( 'latest', $rows, 'row_' );

		$tmpl->displayParsedTemplate( 'mod_latest' );
	}
}
?>