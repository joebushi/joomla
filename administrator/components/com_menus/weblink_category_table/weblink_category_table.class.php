<?php
/**
* @version $Id: weblink_category_table.class.php,v 1.1 2005/08/25 14:14:35 johanjanssens Exp $
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
class weblink_category_table_menu {

	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function editCategory( $uid, $menutype, $option ) {
		global $database, $my, $mainframe;
		global $_LANG;

		mosFS::load( '@class', 'com_components' );

		$menu = new mosMenu( $database );
		$menu->load( $uid );

		// fail if checked out not by 'me'
		if ($menu->checked_out && $menu->checked_out <> $my->id) {
			mosErrorAlert( $_LANG->_( 'The module' ) .' '. $menu->title .' '. $_LANG->_( 'descBeingEditted' ) );
		}

		if ( $uid ) {
			$menu->checkout( $my->id );
		} else {
			$menu->type 		= 'weblink_category_table';
			mosMenuFactory::setValues( $menu, $menutype );
		}

		// build list of categories
		$lists['componentid']	= mosComponentFactory::buildCategoryList( 'componentid', 'com_weblinks', intval( $menu->componentid ), NULL, 'ordering', 5, 0 ); 
		if ( $uid ) {
			$query = "SELECT name"
			. "\n FROM #__categories"
			. "\n WHERE section = 'com_weblinks'"
			. "\n AND published = '1'"
			. "\n AND id = ". $menu->componentid
			;
			$database->setQuery( $query );
			$category = $database->loadResult();
			$lists['componentid'] = '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />'. $category;
		}
		
		// build common lists
		mosMenuFactory::buildLists( $lists, $menu, $uid );
		
		// get params definitions
		// common
		$commonParams =& new mosParameters( $menu->params, $mainframe->getPath( 'commonmenu_xml' ), 'menu' );
		// menu type specific
		$itemParams =& new mosParameters( $menu->params, $mainframe->getPath( 'menu_xml', $menu->type ), 'menu' );
		$params[] = $commonParams;
		$params[] = $itemParams;

		weblink_category_table_menu_html::editCategory( $menu, $lists, $params, $option );
	}
}
?>