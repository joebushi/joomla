<?php
/**
* @version $Id: mambo.serialize.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Class to handle serializing of data
 */
class mosSerializer {

	/**
	 * serializes $_POST and $_REQUEST in a file in cache/ dir
	 */
	function serializeState() {
		global $my, $mosConfig_absolute_path;

		$handle = md5($my->id . microtime());
		$path = $mosConfig_absolute_path . '/cache/';
		$filename = $path . $handle . '.state';

		//flushstates
		mosSerializer::flushStates($path);

		// saving state
		$fp = fopen($filename, "wb");
		mosFixRequestURI(); //fix for IIS
		$_POST['target_uri'] = $_SERVER['REQUEST_URI'];  //save the referer
		$recovery['POST'] = $_POST;
		$recovery['REQUEST'] = $_REQUEST;
	  	array_walk($recovery, 'base64_encoder_multi');
	  	$data = serialize($recovery);
	  	fwrite($fp, $data);
	   	fclose($fp);

		return $handle;
	}

	/**
	 * Restores the state of $_POST and $_REQUEST from saved state file
	 */
	function deserializeState($handle) {
		global $mosConfig_absolute_path;
		// reloading state
		$path = $mosConfig_absolute_path . '/cache/';
		$filename = $path . $handle . '.state';

		if (file_exists($filename)) {

			$fp = fopen($filename,"r");
			$data = fread($fp,filesize($filename));
			fclose($fp);
			//delete redundant file
			unlink($filename);

			$recovery = unserialize($data);
			array_walk($recovery, 'base64_cleaner_multi');
			//extract($recovery, EXTR_OVERWRITE);

			$_POST = $recovery['POST'];
			$_REQUEST = $recovery['REQUEST'];
		}
	}

	/**
	 * Deletes all saved states over 2 days old
	 */
	function flushStates($path) {
		$handle=opendir($path);
		while (false!==($file = readdir($handle))) {
			if (substr_count($file, ".state") > 0) {
				$diff = (time() - filemtime("$path/$file"))/60/60/24;
				if ($diff > 2) unlink("$path/$file"); //deletes filse older than 2 days
			}
		}
		closedir($handle);
	}
}

/**
 * Functions to handle base64 encoding of mutlidimensional arrays
 */

function base64_encoder_multi( &$val, $key ) {
	if (is_array( $val )) {
		//array_walk($val, 'base64_encoder_multi', $new);
		array_walk( $val, 'base64_encoder_multi', $key );
	} else {
		$val = base64_encode( $val );
	}
}

function base64_cleaner_multi(&$val,$key) {
	if (is_array( $val )) {
		//array_walk( $val, 'base64_cleaner_multi', $new );
		array_walk( $val, 'base64_cleaner_multi', $key );
	} else {
	$val = base64_decode( $val );
	}
}

/**
 * Function to fix REQUEST_URI in a manner that is safe for IIS
 */

function mosFixRequestURI() {

	if ( !isset($_SERVER['REQUEST_URI']) || !$_SERVER[ 'REQUEST_URI' ] )  {
		if ( !( $_SERVER[ 'REQUEST_URI' ] = @$_SERVER['PHP_SELF'] ) ) {
			$_SERVER[ 'REQUEST_URI' ] = $_SERVER['SCRIPT_NAME'];
		}
		if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
			$_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
		}
	}
}
?>