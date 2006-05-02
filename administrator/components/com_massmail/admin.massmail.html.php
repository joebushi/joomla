<?php
/**
* @version $Id: admin.massmail.html.php,v 1.1 2005/08/25 14:14:26 johanjanssens Exp $
* @package Mambo
* @subpackage massmail
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage massmail
 */
class massmailScreens {
	/**
	 * Static method to create the template object
	 * @param array An array of other standard files to include
	 * @return patTemplate
	 */
	function &createTemplate( $files=null) {
		mosFS::load( '@patTemplate' );

		$tmpl =& mosFactory::getPatTemplate( $files );
		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl' );

		return $tmpl;
	}

	/**
	 * Example screen with data table and pagination
	 * @param array An array of list data
	 * @param object Page navigation
	 */
	function messageForm( &$lists ) {
		$tmpl =& massmailScreens::createTemplate();
		$tmpl->setAttribute( 'body', 'src', 'messageForm.html' );

		$tmpl->addRows( 'options-list', $lists['gid'] );

		$tmpl->displayParsedTemplate( 'form' );
	}
}
?>