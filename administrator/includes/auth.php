<?php
/**
* @version $Id: auth.php 177 2005-09-13 12:11:40Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$basePath 	= dirname( __FILE__ );
$path 		= $basePath . '/../../configuration.php';
require( $path );

if (!defined( '_MOS_MAMBO_INCLUDED' )) {
	$path = $basePath . '/../../includes/joomla.php';
	require( $path );
}

session_name( md5( $mosConfig_live_site ) );
session_start();
// restore some session variables
if (!isset( $my )) {
	$my = new mosUser( $database );
}

$my->id 		= mosGetParam( $_SESSION, 'session_user_id', '' );
$my->username 	= mosGetParam( $_SESSION, 'session_username', '' );
$my->usertype 	= mosGetParam( $_SESSION, 'session_usertype', '' );
$my->gid 		= mosGetParam( $_SESSION, 'session_gid', '' );
$session_id 	= mosGetParam( $_SESSION, 'session_id', '' );
$logintime 		= mosGetParam( $_SESSION, 'session_logintime', '' );

if ( $session_id != md5( $my->id.$my->username.$my->usertype.$logintime ) ) {
	mosRedirect( "../index.php" );
	die;
}
?>