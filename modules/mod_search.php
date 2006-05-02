<?php
/**
* @version $Id: mod_search.php,v 1.1 2005/08/25 14:23:45 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class modSearchData {

	function &getLists( &$params ){
		global $database, $_LANG;

		// settings for advanced link
		$adv_link = $params->get( 'advanced_link', 0 );

		$link = '';
		if ( $adv_link ) {
			$query = "SELECT m.id"
			. "\n FROM #__menu AS m"
			. "\n WHERE m.link LIKE '%com_search%'"
			;
			$database->setQuery( $query, 0, 1 );
			$link = $database->loadResult();

			if ( $link ) {
				$link = sefRelToAbs( 'index.php?option=com_search&amp;Itemid='. $link );
			} else {
				$link = sefRelToAbs( 'index.php?option=com_search' );
			}
		}

		$list = new stdClass();
		$list->url_adv	 	= $link;
		$list->adv_link 	= $params->get( 'advanced_link', 0 );
		$list->button_text 	= $params->get( 'button_text', $_LANG->_( 'SEARCH_BUTTON' ) );
		$list->width 		= intval( $params->get( 'width', 20 ) );
		$list->text 		= $params->get( 'text', $_LANG->_( 'SEARCH_BOX' ) );
		$list->button 		= $params->get( 'button', 0 );

		return $list;
	}
}

class modSearch {

	function show( &$params ){
		$cache = mosFactory::getCache("mod_search");

		$cache->setCaching($params->get('cache', 1));
		$cache->setCacheValidation(false);

		$cache->callId("modSearch::_display", array( $params ), "mod_search");
	}

	function _display( &$params ) {

		$list = modSearchData::getLists( $params );

		$tmpl =& moduleScreens::createTemplate( 'mod_search.html' );

		$tmpl->addVar( 'mod_search', 'class', 	$params->get( 'moduleclass_sfx' ) );
		$tmpl->addVar( 'mod_search', 'url', 		sefRelToAbs( 'index.php' ) );

		$tmpl->addObject( 'mod_search', $list );

		$tmpl->displayParsedTemplate( 'mod_search' );
	}
}

modSearch::show( $params );
?>