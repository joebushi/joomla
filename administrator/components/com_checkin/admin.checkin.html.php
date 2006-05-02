<?php
/**
* @version $Id: admin.checkin.html.php,v 1.1 2005/08/25 14:14:13 johanjanssens Exp $
* @package Mambo
* @subpackage Checkin
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Checkin
 */
class checkinScreens {
	/**
	 * Static method to create the template object
	 * @param array An array of other standard files to include
	 * @return patTemplate
	 */
	function &createTemplate( $files=null) {
		$tmpl =& mosFactory::getPatTemplate( $files );
		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl' );

		return $tmpl;
	}

	/**
	* index
	* @param array Data rows
	* @param object Page navigation
	*/
	function checkinList( &$rows, &$pageNav, &$lists, &$vars ) {
		$tmpl =& checkinScreens::createTemplate( );
		$tmpl->setAttribute( 'body', 'src', 'checkinList.html' );

		$tmpl->addObject( 'body-list-rows', $rows, 'row_' );
		$tmpl->addVars( 'body', $lists );
		$tmpl->addVars( 'body', $vars );

		// setup the page navigation footer
		$pageNav->setTemplateVars( $tmpl );

		$tmpl->displayParsedTemplate( 'form' );
	}
}
?>