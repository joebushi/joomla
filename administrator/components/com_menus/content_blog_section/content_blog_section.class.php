<?php
/**
* @version $Id: content_blog_section.class.php,v 1.1 2005/08/25 14:14:32 johanjanssens Exp $
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
class content_blog_section {

	/**
	* @param database A database connector object
	* @param integer The unique id of the section to edit (0 if new)
	*/
	function edit( $uid, $menutype, $option ) {
		global $database, $my, $mainframe;
  		global $_LANG;

		$menu = new mosMenu( $database );
		$menu->load( $uid );

		// fail if checked out not by 'me'
		if ($menu->checked_out && $menu->checked_out <> $my->id) {
			mosErrorAlert( $_LANG->_( 'The module' ) .' '. $menu->title .' '. $_LANG->_( 'descBeingEditted' ) );
		}

		if ($uid) {
			$menu->checkout( $my->id );
			// get previously selected Categories
			$params =& new mosParameters( $menu->params );
			$secids = $params->def( 'sectionid', '' );
			if ( $secids ) {
				$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`"
				. "\n FROM #__sections AS s"
				. "\n WHERE s.scope = 'content'"
				. "\n AND s.id IN ( ". $secids . ")"
				. "\n ORDER BY s.name"
				;
				$database->setQuery( $query );
				$lookup = $database->loadObjectList();
			} else {
				$lookup 			= '';
			}
		} else {
			$menu->type 			= 'content_blog_section';
			mosMenuFactory::setValues( $menu, $menutype );
			$lookup 				= '';
		}

		// build the html select list for section
		$rows[] = mosHTML::makeOption( '', $_LANG->_( 'All Sections' ) );
		$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`"
		. "\n FROM #__sections AS s"
		. "\n WHERE s.scope = 'content'"
		. "\n ORDER BY s.name"
		;
		$database->setQuery( $query );
		$rows = array_merge( $rows, $database->loadObjectList() );
		$section = mosHTML::selectList( $rows, 'secid[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', $lookup );
		$lists['sectionid']	= $section;
		
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

		content_blog_section_html::edit( $menu, $lists, $params, $option );
	}

	function saveMenu( $option, $task ) {
		global $database;
  		global $_LANG;

		$params = mosGetParam( $_POST, 'params', '' );
		$secids	= mosGetParam( $_POST, 'secid', array() );
		$secid	= implode( ',', $secids );
		
		$params[sectionid]	= $secid;
		if (is_array( $params )) {
		    $txt = array();
		    foreach ($params as $k=>$v) {
			   $txt[] = "$k=$v";
			}
			$_POST['params'] = mosParameters::textareaHandling( $txt );
		}

		$row = new mosMenu( $database );

		if (!$row->bind( $_POST )) {
			mosErrorAlert( $row->getError() );
		}
		
		if ( count( $secids )== 1 && $secids[0] != '' ) {
			$row->link = str_replace( 'id=0','id='. $secids[0], $row->link );
			$row->componentid = $secids[0];
		}
		
		if (!$row->check()) {
			mosErrorAlert( $row->getError() );
		}
		if (!$row->store()) {
			mosErrorAlert( $row->getError() );
		}
		$row->checkin();
		$row->updateOrder( "menutype = '$row->menutype' AND parent = '$row->parent'" );
		
		$msg = $_LANG->_( 'Menu item Saved' );
		switch ( $task ) {
			case 'apply':
				mosRedirect( 'index2.php?option='. $option .'&menutype='. $row->menutype .'&task=edit&id='. $row->id, $msg );
				break;
		
			case 'save':
			default:
				mosRedirect( 'index2.php?option='. $option .'&menutype='. $row->menutype, $msg );
			break;
		}
	}

}
?>