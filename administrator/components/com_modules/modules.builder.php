<?php
/**
* @version $Id: modules.builder.php,v 1.1 2005/08/25 14:14:49 johanjanssens Exp $
* @package Mambo
* @subpackage Modules
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Components
 */
class moduleCreator {
	/**
	 * Static method to create the component object
	 * @param int The client identifier
	 * @param array An array of other standard files to include
	 * @return patTemplate
	 */
	function &createTemplate( $files=null ) {
		$tmpl =& mosFactory::getPatTemplate( $files );
		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl/create' );
		$tmpl->setNamespace( 'pat' );

		return $tmpl;
	}

	/**
	 * Main PHP File
	 */
	function phpMain( &$tmpl ) {
		$buffer = '';
		$buffer .= $tmpl->getParsedTemplate( 'php-start' );
		$buffer .= $tmpl->getParsedTemplate( 'php-module' );
		$buffer .= $tmpl->getParsedTemplate( 'php-end' );
		
		return $buffer;
	}

	/**
	 * HTML View
	 */
	function htmlMain( &$tmpl ) {

		$buffer = '';
		$buffer .= $tmpl->getParsedTemplate( 'html-start' );
		$buffer .= $tmpl->getParsedTemplate( 'html-module' );

		return $buffer;
	}

	/**
	 * Blank index file
	 */
	function htmlIndex() {
		return '<html><body></body></html>';
	}

	/**
	 * XML Main
	 */
	function xmlMain( &$tmpl ) {

		$buffer = '';
		$buffer .= $tmpl->getParsedTemplate( 'xml-main' );

		return $buffer;
	}


}
?>