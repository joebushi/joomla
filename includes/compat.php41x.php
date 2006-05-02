<?php
/**
* PHP 4.1.x Compatibility functions
* @version $Id: compat.php41x.php,v 1.1 2005/08/26 16:33:38 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

if (!function_exists( 'array_change_key_case' )) {
	if (!defined('CASE_LOWER')) {
		define('CASE_LOWER', 0);
	}

	if (!defined('CASE_UPPER')) {
		define('CASE_UPPER', 1);
	}

	function array_change_key_case( $input, $case=CASE_LOWER ) {
		if (!is_array( $input )) {
			return false;
		}

		$array = array();

		foreach ($input as $k=>$v) {
			if ($case) {
				$array[strtoupper( $k )] = $v;
			} else {
				$array[strtolower( $k )] = $v;
			}
		}
		return $array;
	}
}

/**
 * Replace function is_a()
 *
 * @category	PHP
 * @package	 PHP_Compat
 * @link		http://php.net/function.is_a
 * @author	  Aidan Lister <aidan@php.net>
 * @version	 $Revision: 1.1 $
 * @since	   PHP 4.2.0
 * @require	 PHP 4.0.0 (is_subclass_of)
 */
if (!function_exists('is_a')) {
	function is_a($object, $class) {
		if (!is_object($object)) {
			return false;
		}

		if (get_class($object) == strtolower($class)) {
			return true;
		} else {
			return is_subclass_of($object, $class);
		}
	}
}

/**
 * Replace ob_flush()
 *
 * @category	PHP
 * @package	 PHP_Compat
 * @link		http://php.net/function.ob_flush
 * @author	  Aidan Lister <aidan@php.net>
 * @author	  Thiemo M�ttig (http://maettig.com/)
 * @version	 $Revision: 1.1 $
 * @since	   PHP 4.2.0
 * @require	 PHP 4.0.1 (trigger_error)
 */
if (!function_exists('ob_flush')) {
	function ob_flush() {
		if (@ob_end_flush()) {
			return ob_start();
		}

		trigger_error("ob_flush() Failed to flush buffer. No buffer to flush.", E_USER_NOTICE);

		return false;
	}
}
?>