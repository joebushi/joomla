<?php
/**
* @version $Id: component_item_link.class.php,v 1.1 2005/08/25 14:14:27 johanjanssens Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* Component item link class
* @package Mambo
* @subpackage Menus
*/
class component_item_link_menu {

	function edit( &$uid, $menutype, $option ) {
		global $database, $my, $mainframe;
  		global $_LANG;

		$menu = new mosMenu( $database );
		$menu->load( $uid );
	
		// fail if checked out not by 'me'
		if ($menu->checked_out && $menu->checked_out <> $my->id) {
			mosErrorAlert( $_LANG->_( 'The module' ) .' '. $menu->title .' '. $_LANG->_( 'descBeingEditted' ) );
		}
	
		if ( $uid ) {
			$menu->checkout( $my->id );
		} else {
			// load values for new entry
			$menu->type 		= 'component_item_link';
			mosMenuFactory::setValues( $menu, $menutype );
		}
	
		if ( $uid ) {
			$temp = explode( '&Itemid=', $menu->link );
			 $query = "SELECT a.name"
			. "\n FROM #__menu AS a"
			. "\n WHERE a.link = '". $temp[0] ."'"
			;
			$database->setQuery( $query );
			$components = $database->loadResult();
			$lists['components'] =  $components;
			$lists['components'] .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
		} else {
			$query = "SELECT CONCAT( a.link, '&amp;Itemid=', a.id ) AS value, a.name AS text"
			. "\n FROM #__menu AS a"
			. "\n WHERE a.published = '1'"
			. "\n AND a.type = 'components'"
			. "\n ORDER BY a.menutype, a.name"
			;
			$database->setQuery( $query );
			$components = $database->loadObjectList( );
	
			//	Create a list of links
			$lists['components'] = mosHTML::selectList( $components, 'link', 'class="inputbox" size="10"', 'value', 'text', '' );
		}
	
		// build common lists
		mosMenuFactory::buildLists( $lists, $menu, $uid, 1 );
		
		// get params definitions
		$params =& new mosParameters( $menu->params, $mainframe->getPath( 'menu_xml', $menu->type ), 'menu' );

		component_item_link_menu_html::edit( $menu, $lists, $params, $option );
	}
}
?>