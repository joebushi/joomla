<?php
/**
* @version $Id: components.class.php,v 1.1 2005/08/25 14:14:28 johanjanssens Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Menus
*/
class components_menu {
	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function edit( $uid, $menutype, $option ) {
		global $database, $my, $mainframe;
		global $_LANG;

		$menu = new mosMenu( $database );
		$menu->load( $uid );

		$row = new mosComponent( $database );
		// load the row from the db table
		$row->load( $menu->componentid );

		// fail if checked out not by 'me'
		if ( $menu->checked_out && $menu->checked_out <> $my->id ) {
			mosErrorAlert( $_LANG->_( 'The module' ) .' '. $menu->title .' '. $_LANG->_( 'descBeingEditted' ) );
		}

		if ( $uid ) {
			// do stuff for existing item
			$menu->checkout( $my->id );
		} else {
			// do stuff for new item
			$menu->type 		= 'components';
			mosMenuFactory::setValues( $menu, $menutype );
		}

		$query = "SELECT c.id AS value, c.name AS text, c.link" 
		. "\n FROM #__components AS c"
		. "\n WHERE c.link <> ''" 
		. "\n ORDER BY c.name"
		;
		$database->setQuery( $query );
		$components = $database->loadObjectList( );
		
		// build the html select list for section
		mosFS::load( '@class', 'com_components' );
		$lists['componentid'] 	= mosComponentFactory::buildList( $menu, $uid );

		// componentname
		$lists['componentname'] = mosComponentFactory::getComponentName( $menu, $uid );
		// build common lists
		mosMenuFactory::buildLists( $lists, $menu, $uid );
		
		// get params definitions
		// common
		$commonParams =& new mosParameters( $menu->params, $mainframe->getPath( 'commonmenu_xml' ), 'menu' );
		// menu type specific
		$itemParams =& new mosParameters( $menu->params, $mainframe->getPath( 'com_xml', $row->option ), 'component' );
		$params[] = $commonParams;
		$params[] = $itemParams;		

		components_menu_html::edit( $menu, $components, $lists, $params, $option );
	}
}
?>