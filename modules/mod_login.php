<?php
/**
 * @version $Id: mod_login.php,v 1.1 2005/08/25 14:23:45 johanjanssens Exp $
 * @package Mambo
 * @subpackage mod_login
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class modLoginData {

	function getType( &$params ) {
		global $my;
	    $type = ($my->id) ? 'login' : 'logout';
		return $type;
	}

	function getVars( &$params, $return ) {
		 global $my, $mainframe, $database;

		$vars = array(
			'allowUserRegistration' => $mainframe->getCfg( 'allowUserRegistration' ),
			// converts & to &amp; for xtml compliance
			'return' => ampReplace( $return )
		);

		if ( $params->get( 'name' ) ) {
			$query = "SELECT name"
			. "\n FROM #__users"
			. "\n WHERE id = $my->id"
			;
			$database->setQuery( $query );
			$vars['name'] = $database->loadResult();
		} else {
			$vars['name'] = $my->username;
		}

		return $vars;
	}
}

class modLogin {

	/**
	 * Show the login/logout form
	 */
	function show (&$params) {
		global $my;
		$cache = mosFactory::getCache( "mod_login");

		$cache->setCaching($params->get('cache', 1));
		$cache->setCacheValidation(false);

		$cache->callId( "modLogin::_display", array( $params ), "mod_login".$my->gid );
	}

	function _display( &$params ) {

		$return = mosGetParam( $_SERVER, 'REQUEST_URI', null );
		$params->def( 'logout', $return );

		$type = modLoginData::getType($params);
		$vars = modLoginData::getVars($params, $return);

		$tmpl =& moduleScreens::createTemplate( 'mod_login.html' );

		$tmpl->addVar( 'mod_login', 'type', $type );
		$tmpl->addVars( 'mod_login', $vars );
		$tmpl->addObject( 'mod_login', $params->toObject() );

		$tmpl->displayParsedTemplate( 'mod_login' );
	}
}

modLogin::show( $params );
?>