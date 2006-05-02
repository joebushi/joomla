<?php
/**
 * Support functions for installation
 * @version $Id: installation.functions.php,v 1.1 2005/08/25 14:21:20 johanjanssens Exp $
 * @package Mambo
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

error_reporting( E_ALL );
@set_magic_quotes_runtime( 0 );

$steps = array(
	'lang' => 'off',
	'preinstall' => 'off',
	'license' => 'off',
	'dbconfig' => 'off',
	'mainconfig' => 'off',
	'finish' => 'off'
);

/**
 * Determine the absolute path for the main Mambo tree
 * @return string A file path
 */
function getAbsolutePath() {
	$path = dirname( __FILE__ );
	$path = str_replace( '\\', '/', $path );
	$parts = explode( '/', $path );
	array_pop( $parts );
	return implode( '/', $parts );
}

/**
* Utility function to return a value from a named array or a specified default
*/
define( "_MOS_NOTRIM", 0x0001 );
define( "_MOS_ALLOWHTML", 0x0002 );
function mosGetParam( &$arr, $name, $def=null, $mask=0 ) {
	$return = null;
	if (isset( $arr[$name] )) {
		if (is_string( $arr[$name] )) {
			if (!($mask&_MOS_NOTRIM)) {
				$arr[$name] = trim( $arr[$name] );
			}
			if (!($mask&_MOS_ALLOWHTML)) {
				$arr[$name] = strip_tags( $arr[$name] );
			}
			if (!get_magic_quotes_gpc()) {
				$arr[$name] = addslashes( $arr[$name] );
			}
		}
		return $arr[$name];
	} else {
		return $def;
	}
}

function mosMakePassword($length) {
	$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$len = strlen($salt);
	$makepass='';
	mt_srand(10000000*(double)microtime());
	for ($i = 0; $i < $length; $i++)
	$makepass .= $salt[mt_rand(0,$len - 1)];
	return $makepass;
}


function get_php_setting($val) {
	$r =  (ini_get($val) == '1' ? 1 : 0);
	return $r ? 'ON' : 'OFF';
}

/**
* Mambo Mainframe class
*
* Provide many supporting API functions
* @package Mambo
*/
class mosMainFrame {
	function getBasePath() {
		return dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
	}
	function getClientID() {
		return 2;
	}
}

?>