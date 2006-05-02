<?php
/**
* @version $Id: content_archive_category.class.php,v 1.1 2005/08/25 14:14:31 johanjanssens Exp $
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
class content_archive_category_menu {
	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function editCategory( $uid, $menutype, $option ) {
		global $database, $my, $mainframe;
 		global $_LANG;

		mosFS::load( '@class', 'com_content' );

		$menu = new mosMenu( $database );
		$menu->load( $uid );
	
		// fail if checked out not by 'me'
		if ($menu->checked_out && $menu->checked_out <> $my->id) {
			mosErrorAlert( $_LANG->_( 'The module' ) .' '. $menu->title .' '. $_LANG->_( 'descBeingEditted' ) );
		}
	
		if ($uid) {
			$menu->checkout( $my->id );
		} else {
			$menu->type 		= 'content_archive_category';
			mosMenuFactory::setValues( $menu, $menutype );
		}
		
		// build the html select list for category
		$lists['componentid']	= mosContentFactory::buildCategoryList( $menu, $uid );
		// build common lists
		mosMenuFactory::buildLists( $lists, $menu, $uid );
		
		// get params definitions
		// common
		$commonParams =& new mosParameters( $menu->params, $mainframe->getPath( 'commonmenu_xml' ), 'menu' );
		// blog type specific
		$blogParams =& new mosParameters( $menu->params, $mainframe->getPath( 'blogmenu_xml' ), 'menu' );
		// menu type specific
		$itemParams =& new mosParameters( $menu->params, $mainframe->getPath( 'menu_xml', $menu->type ), 'menu' );
		$params = array();
		$params[] = $commonParams;
		$params[] = $blogParams;		
		$params[] = $itemParams;		
	
		content_archive_category_menu_html::editCategory( $menu, $lists, $params, $option );
	}
}
?>