<?php
/**
* @version $Id: login.html.php,v 1.1 2005/08/25 14:18:11 johanjanssens Exp $
* @package Mambo
* @subpackage Users
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Login
 */
class loginScreens_front {
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

	function login( &$row, &$params ) {
		global $mosConfig_lang;

		$tmpl =& loginScreens_front::createTemplate( 'login.html' );

		$tmpl->addVar( 'body', 'form_url',		sefRelToAbs( 'index.php?option=login' ) );
		$tmpl->addVar( 'body', 'url_password',	sefRelToAbs( 'index.php?option=com_registration&amp;task=lostPassword' ) );
		$tmpl->addVar( 'body', 'url_register',	sefRelToAbs( 'index.php?option=com_registration&amp;task=register' ) );

		$tmpl->addVar( 'body', 'image',			$row->image );
		$tmpl->addVar( 'body', 'return',		$row->return );
		$tmpl->addVar( 'body', 'lang',			$mosConfig_lang );
		$tmpl->addVar( 'body', 'register',		$row->register );

		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->displayParsedTemplate( 'body' );
	}

	function logout( &$row, &$params ) {
		global $mosConfig_lang;

		$tmpl =& loginScreens_front::createTemplate( 'logout.html' );

		$tmpl->addVar( 'body', 'form_url',		sefRelToAbs( 'index.php?option=logout' ) );

		$tmpl->addVar( 'body', 'image',			$row->image );
		$tmpl->addVar( 'body', 'return',		$row->return );
		$tmpl->addVar( 'body', 'lang',			$mosConfig_lang );

		$tmpl->addObject( 'body', $params->toObject(), 'p_' );

		$tmpl->displayParsedTemplate( 'body' );
	}
}
?>