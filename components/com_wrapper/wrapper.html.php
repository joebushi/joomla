<?php
/**
* @version $Id: wrapper.html.php,v 1.1 2005/08/25 14:18:16 johanjanssens Exp $
* @package Mambo
* @subpackage Wrapper
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Wrapper
 */
class wrapperScreens_front {
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

	function view( &$row, &$params ) {
		$tmpl =& wrapperScreens_front::createTemplate( 'view.html' );
		
		$tmpl->addVar( 'body', 'url', 			$row->url );
		$tmpl->addVar( 'body', 'load', 			$row->load );
		$tmpl->addVar( 'body', 'loadx', 		$row->loadx );
	
		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->displayParsedTemplate( 'body' );
	}
}
?>