<?php
/**
* @version $Id: auth.php,v 1.1 2005/08/25 14:17:43 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require( dirname( dirname( dirname( __FILE__ ) ) ) . '/configuration.php' );

// enables switching to secure https
if ( $_SERVER["SERVER_PORT"] == '443' ) {
   $mosConfig_live_site = str_replace( 'http://', 'https://', $mosConfig_live_site );
}

if (!defined( '_MOS_MAMBO_INCLUDED' )) {
	require( $mosConfig_absolute_path . '/includes/mambo.php' );
}

session_name( 'mosadmin' );
session_start();

if (!mosGetParam( $_SESSION, 'session_id' )) {
	mosRedirect( 'index.php' );
}

// mainframe is an API workhorse, lots of 'core' interaction routines
$mainframe = new mosMainFrame( $database, null, true );
$mainframe->initSession( 'php' );

/** get the information about the current user from the sessions table */
$my = $mainframe->getUser();
// TODO: fix this patch to get gid to work properly
$my->gid = array_shift( $acl->get_object_groups( $acl->get_object_id( 'users', $my->id, 'ARO' ), 'ARO' ) );

// double check
if ($my->id < 1 || !$acl->acl_check( 'login', 'administrator', 'users', $my->usertype )) {
	$mainframe->logout();
	mosRedirect( 'index.php' );
}
?>