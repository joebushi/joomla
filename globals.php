<?php
/**
* @version $Id: globals.php,v 1.1 2005/08/25 14:18:16 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/**
* Emulates register globals = off
*/
function unregister_globals () {
	$REQUEST 	= $_REQUEST;
	$GET 		= $_GET;
	$POST 		= $_POST;
	$COOKIE 	= $_COOKIE;

	if (isset ( $_SESSION )) {
		$SESSION = $_SESSION;
	}

	$FILES 	= $_FILES;
	$ENV 	= $_ENV;
	$SERVER = $_SERVER;

	foreach ($GLOBALS as $key => $value) {
		if ( $key != 'GLOBALS' ) {
			unset ( $GLOBALS [ $key ] );
		}
	}

	$_REQUEST 	= $REQUEST;
	$_GET 		= $GET;
	$_POST 		= $POST;
	$_COOKIE 	= $COOKIE;

	if (isset ( $SESSION )) {
		$_SESSION = $SESSION;
	}

	$_FILES 	= $FILES;
	$_ENV 		= $ENV;
	$_SERVER 	= $SERVER;

	// Support for IIS which does not support $_SERVER['REQUEST_URI']
	if ( strlen( $_SERVER['REQUEST_URI'] ) == 0 ) {
 		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
	}
}
unregister_globals ();
?>