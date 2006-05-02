<?php
/**
* @version $Id: poll.html.php,v 1.1 2005/08/25 14:18:12 johanjanssens Exp $
* @package Mambo
* @subpackage Polls
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Polls
 */
class pollScreens_front {
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

	function displaylist( &$params, &$poll, &$rows ) {
		global $_LANG;

		$tmpl =& pollScreens_front::createTemplate( 'list.html' );

		if ( $params->get( 'show_poll' ) ) {
			// individual poll variables
			$tmpl->addObject( 'rows', $rows, 'row_' );
		}

		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->displayParsedTemplate( 'body' );
	}

	function vote( $text, $link='', $show=0 ) {
		global $mainframe;

		$params =& new mosParameters( '' );
		$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );

		$tmpl =& pollScreens_front::createTemplate( 'vote.html' );

		$tmpl->addVar( 'body', 'text', 			$text );
		$tmpl->addVar( 'body', 'show', 			$show );
		$tmpl->addVar( 'body', 'link', 			$link );

		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->displayParsedTemplate( 'body' );
	}
}
?>