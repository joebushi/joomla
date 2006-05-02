<?php
/** 
* version $Id: newsfeeds.html.php,v 1.1 2005/08/25 14:18:12 johanjanssens Exp $
* @package Mambo
* @subpackage Newsfeeds
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* 
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Newsfeeds
 */
class newsfeedsScreens_front {
	/**
	 * @param string The main template file to include for output
	 * @param array An array of other standard files to include
	 * @return patTemplate A template object
	 */
	function &createTemplate( $bodyHtml='', $files=null ) {
		$tmpl =& mosFactory::getPatTemplate( $files );
		
		$directory = mosComponentDirectory( $bodyHtml, dirname( __FILE__ ) );
		$tmpl->setRoot( $directory );
		
		$tmpl->setAttribute( 'body', 'src', $bodyHtml );

		return $tmpl;
	}

	function item( &$items, &$rows, &$params ) {
		$tmpl =& newsfeedsScreens_front::createTemplate( 'item.html' );
		
		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->addVar( 'body', 'show_header',		( $params->get( 'header' ) ? 1 : 0 ) );		
		
		$tmpl->addObject( 'rows', $rows, 'row_' );
		$tmpl->addObject( 'items', $items, 'item_' );		

		$tmpl->displayParsedTemplate( 'body' );
	}
	
	function list_section( &$params, &$current, &$cats ) {
		global $_MAMBOTS;
		
		// process the new bots
		$current->text = $current->descrip;
		$_MAMBOTS->loadBotGroup( 'content' );
		$results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$current, &$params ), true );
		
		$tmpl =& newsfeedsScreens_front::createTemplate( 'list-section.html' );
		
		$tmpl->addVar( 'body', 'show_image',			( $current->img ? 1 : 0 ) );	
	
		$tmpl->addObject( 'current', $current, 'cur_' );

		// category list params
		$tmpl->addObject( 'categories', $cats, 'cat_' );

		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->displayParsedTemplate( 'body' );
	}
	
	function table_category( &$params, &$current, &$cats, &$rows ) {
		global $_MAMBOTS;
		
		// process the new bots
		$current->text = $current->descrip;
		$_MAMBOTS->loadBotGroup( 'content' );
		$results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$current, &$params ), true );
		
		$tmpl =& newsfeedsScreens_front::createTemplate( 'table-category.html' );
		
		$tmpl->addVar( 'body', 'show_image',			( $current->img ? 1 : 0 ) );	
	
		$tmpl->addObject( 'current', $current, 'cur_' );

		// table item params
		$tmpl->addObject( 'rows', $rows, 'row_' );
		
		// category list params
		$tmpl->addObject( 'categories', $cats, 'cat_' );
		
		$tmpl->addObject( 'body', $params->toObject(), 'p_' );
		
		$tmpl->displayParsedTemplate( 'body' );
	}
}
?>