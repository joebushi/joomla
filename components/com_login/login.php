<?php
/**
* @version $Id: login.php,v 1.1 2005/08/25 14:18:11 johanjanssens Exp $
* @package Mambo
* @subpackage Users
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// load the html drawing class
mosFS::load( '@front_html' );

global $database, $my;
global $mosConfig_live_site, $mainframe;

$return = mosGetParam( $_SERVER, 'QUERY_STRING', null );
$return = 'index.php?'. $return;
// converts & to &amp; for xtml compliance
$return = ampReplace( $return );

$menu = new mosMenu( $database );
$menu->load( $Itemid );
$params =& new mosParameters( $menu->params );

$params->def( 'page_title',					1 );
$params->def( 'header_login', 				$menu->name );
$params->def( 'header_logout', 				$menu->name );
$params->def( 'pageclass_sfx', 				'' );
$params->def( 'back_button', 				$mainframe->getCfg( 'back_button' ) );
$params->def( 'login', 						$mosConfig_live_site );
$params->def( 'logout', 					$mosConfig_live_site );
$params->def( 'login_message', 				0 );
$params->def( 'logout_message', 			0 );
$params->def( 'description_login', 			1 );
$params->def( 'description_logout', 		1 );
$params->def( 'description_login_text', 	$_LANG->_( 'LOGIN_DESCRIPTION' ) );
$params->def( 'description_logout_text', 	$_LANG->_( 'LOGOUT_DESCRIPTION' ) );
$params->def( 'image_login', 				'key.jpg' );
$params->def( 'image_logout', 				'key.jpg' );
$params->def( 'image_login_align', 			'right' );
$params->def( 'image_logout_align', 		'right' );
$params->def( 'registration', 				$mainframe->getCfg( 'allowUserRegistration' ) );

$image_login 	= '';
$image_logout 	= '';
if ( $params->get( 'image_login' ) != -1 ) {
	$image 			= $mosConfig_live_site .'/images/stories/'. $params->get( 'image_login' );
	$image_login 	= '<img src="'. $image  .'" align="'. $params->get( 'image_login_align' ) .'" hspace="10" alt="" />';
}
if ( $params->get( 'image_logout' ) != -1 ) {
	$image 			= $mosConfig_live_site .'/images/stories/'. $params->get( 'image_logout' );
	$image_logout 	= '<img src="'. $image .'" align="'. $params->get( 'image_logout_align' ) .'" hspace="10" alt="" />';
}

// Back Button
$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );

if ( $my->id ) {
// Logout Output
	$return 		= strlen( $params->get( 'logout' ) ) ? $params->get( 'logout' ) : $return;
	$row->return 	= $return;
	$row->image 	= $image_logout;

	loginScreens_front::logout( $row, $params );
} else {
// Login Output
	$return 		= strlen( $params->get( 'login' ) ) ? $params->get( 'login' ) : $return;
	$row->return 	= $return;
	$row->image 	= $image_login;
	$row->register	= $mainframe->getCfg( 'allowUserRegistration' );

	loginScreens_front::login( $row, $params );
}
?>