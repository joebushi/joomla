<?php
/**
* @version $Id: mod_sections.php,v 1.2 2005/08/29 15:52:20 alekandreev Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

//** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class modSectionsData {

	function &getLists( &$params ){
		global $my, $mainframe, $database;
		global $mosConfig_zero_date;

		$count 	= intval( $params->get( 'count', 20 ) );
		$access = !$mainframe->getCfg( 'shownoauth' );
		$now 	= $mainframe->getDateTime();

		$query = "SELECT a.id AS id, a.title AS title, COUNT(b.id) as cnt"
		. "\n FROM #__sections as a"
		. "\n LEFT JOIN #__content as b"
		. "\n ON a.id=b.sectionid"
		. ( $access ? "\n AND b.access<='$my->gid'" : '' )
		. "\n AND ( b.publish_up = '$mosConfig_zero_date' OR b.publish_up <= '$now' )"
		. "\n AND ( b.publish_down = '$mosConfig_zero_date' OR b.publish_down >= '$now' )"
		. "\n WHERE a.scope='content'"
		. "\n AND a.published='1' AND a.active = 1"
		. ( $access ? "\n AND a.access<='$my->gid'" : '' )
		. "\n GROUP BY a.id"
		. "\n HAVING COUNT(b.id)>0"
		. "\n ORDER BY a.ordering"
		;
		$database->setQuery( $query, 0, $count );

		$rows = $database->loadObjectList();
		$i = 0;
		foreach ( $rows as $row ) {
			$lists[$i]->link	= $link;
			$lists[$i]->text	= $row->title;
			$i++;
		}
		return $lists;
	}
}

class modSections {

	function show ( &$params ) {
		global $my;

		$cache  = mosFactory::getCache( "mod_sections" );

		$cache->setCaching($params->get('cache', 1));
		$cache->setLifeTime($params->get('cache_time', 900));
		$cache->setCacheValidation(true);

		$cache->callId( "modSections::_display", array( $params ), "mod_sections".$my->gid );
	}

	function _display( &$params ) {

		$lists = modSectionsData::getLists( $params );
		$tmpl =& moduleScreens::createTemplate( 'mod_sections.html' );

		$tmpl->addVar( 'mod_sections', 'class', $params->get( 'moduleclass_sfx' ) );

		$tmpl->addObject( 'mod_sections','' );

		$tmpl->addObject( 'mod_sections-items', $lists, 'row_' );

		$tmpl->displayParsedTemplate( 'mod_sections' );
	}
}

modSections::show( $params );
?>
