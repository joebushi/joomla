<?php
/**
* @version $Id: mambo.userbot.php,v 1.1 2005/08/25 14:23:44 johanjanssens Exp $
* @package Mambo
* @subpackage Mambots
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

//Login User event
$_MAMBOTS->registerFunction( 'onLoginUser', 'botMamboLoginUser' );

//Logout User event
$_MAMBOTS->registerFunction( 'onLogoutUser', 'botMamboLogoutUser' );

/**
* Mambo user login method
* Method is called when a user is login in
* @param 	string	The user name
* @param	string	The password
* @return	int		The id of the user
*/
function botMamboLoginUser( $username, $password ) {
	global $database;

	$query = 'SELECT id
		FROM #__users
		WHERE username=' . $database->Quote( $username ) . ' AND password=' . $database->Quote( md5( $password ) );
	$database->setQuery( $query );

	return $database->loadResult();
}

/**
* Mambo logout user method
* Method is called when a user is login out
* @param 	array	  	holds the user data
*/
function botMamboLogoutUser( $user ) {
	//do nothing
}

?>