<?php
/**
* @version $Id: search.html.php,v 1.1 2005/08/25 14:18:14 johanjanssens Exp $
* @package Mambo
* @subpackage Search
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Search
 */
class searchScreens_front {
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

	function displaylist( &$params, &$lists, &$areas, &$searches ) {	
				
		$showAreas = mosGetParam( $lists, 'areas', array() );
		$allAreas = array();
		foreach ( $showAreas as $area ) {
			$allAreas = array_merge( $allAreas, $area );
		}
		$i = 0;
		$hasAreas = is_array( $areas );
		foreach ( $allAreas as $val => $txt ) {
			$checked = $hasAreas && in_array( $val, $areas ) ? 'checked="true"' : '';
			$rows[$i]->val 		= $val;
			$rows[$i]->txt 		= $txt;
			$rows[$i]->checked 	= $checked;
			$i++;
		}		
		
		$tmpl =& searchScreens_front::createTemplate( 'list.html' );
			
		$tmpl->addObject( 'rows', $rows, 'row_' );
		$tmpl->addObject( 'searches', $searches, 'search_' );
				
		$tmpl->addObject( 'body', $params->toObject(), 'p_' );
		
		$tmpl->displayParsedTemplate( 'body' );
	}
}
?>