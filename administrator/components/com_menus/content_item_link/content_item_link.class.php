<?php
/**
* @version $Id: content_item_link.class.php,v 1.2 2005/08/29 15:52:19 alekandreev Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* Content item link class
* @package Mambo
* @subpackage Menus
*/
class content_item_link_menu {

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
			$menu->type 		= 'content_item_link';
			mosMenuFactory::setValues( $menu, $menutype );
		}

		if ( $uid ) {
			$link 	= 'javascript:submitbutton( \'redirect\' );';

			$temp 	= explode( 'id=', $menu->link );
			 $query = "SELECT a.title, c.name AS category, s.name AS section"
			. "\n FROM #__content AS a"
			. "\n LEFT JOIN #__categories AS c ON a.catid = c.id"
			. "\n LEFT JOIN #__sections AS s ON a.sectionid = s.id"
			. "\n WHERE a.active = 1 AND a.id = '". $temp[1] ."'"
			;
			$database->setQuery( $query );
			$content = $database->loadObjectlist();
			// outputs item name, category & section instead of the select list
			$lists['content'] = '
			<table width="100%">
			<tr>
				<td width="10%">
				'. $_LANG->_( 'Item' ) .':
				</td>
				<td>
				<a href="'. $link .'" title="'. $_LANG->_( 'Edit Content Item' ) .'">
				'. $content[0]->title .'
				</a>
				</td>
			</tr>
			<tr>
				<td width="10%">
				'. $_LANG->_( 'Category' ) .':
				</td>
				<td>
				'. $content[0]->category .'
				</td>
			</tr>
			<tr>
				<td width="10%">
				'. $_LANG->_( 'Section' ) .':
				</td>
				<td>
				'. $content[0]->section .'
				</td>
			</tr>
			</table>';
			$contents = '';
			$lists['content'] .= '<input type="hidden" name="content_item_link" value="'. $temp[1] .'" />';
		} else {
			$query = "SELECT a.id AS value, a.title AS text, a.sectionid, a.catid "
			. "\n FROM #__content AS a"
			. "\n INNER JOIN #__categories AS c ON a.catid = c.id"
			. "\n INNER JOIN #__sections AS s ON a.sectionid = s.id"
			. "\n WHERE a.state = '1' AND a.active = 1"
			. "\n ORDER BY a.sectionid, a.catid, a.title"
			;
			$database->setQuery( $query );
			$contents = $database->loadObjectList( );

			foreach ( $contents as $content ) {
				$database->setQuery( "SELECT s.title"
				. "\n FROM #__sections AS s"
				. "\n WHERE s.scope = 'content'"
				. "\n AND s.id = '". $content->sectionid ."'"
				);
				$section = $database->loadResult();

				$database->setQuery( "SELECT c.title"
				. "\n FROM #__categories AS c"
				. "\n WHERE c.id = '". $content->catid ."'"
				);
				$category = $database->loadResult();

				$value = $content->value;
				$text = $section ." - ". $category ." / ". $content->text ."&nbsp;&nbsp;&nbsp;&nbsp;";

				$temp[] = mosHTML::makeOption( $value, $text );
				$contents = $temp;
			}

			//	Create a list of links
			$lists['content'] = mosHTML::selectList( $contents, 'content_item_link', 'class="inputbox" size="10"', 'value', 'text', '' );
		}

		// build common lists
		mosMenuFactory::buildLists( $lists, $menu, $uid );

		// get params definitions
		$params =& new mosParameters( $menu->params, $mainframe->getPath( 'menu_xml', $menu->type ), 'menu' );

		content_item_link_menu_html::edit( $menu, $lists, $params, $option, $contents );
	}

	function redirect( $id ) {
		global $database;

		$menu = new mosMenu( $database );
		$menu->bind( $_POST );
		$menuid = mosGetParam( $_POST, 'menuid', 0 );
		if ( $menuid ) {
			$menu->id = $menuid;
		}
		$menu->checkin();

		mosRedirect( 'index2.php?option=com_content&task=edit&id='. $id );
	}
}
?>