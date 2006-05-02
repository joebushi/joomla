<?php
/**
 * @version $Id: export.class.php,v 1.1 2005/08/25 14:14:15 johanjanssens Exp $
 * @package Mambo
 * @subpackage Export
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Export
 */
class ExportFactory {

}


function populateDatabase( $sqlfile, &$errors, $debug=false ) {
	global $database;

	set_time_limit( 120 );
	$buffer = file_get_contents( dirname( __FILE__ ) . '/files/' . $sqlfile );
	$queries = splitSql( $buffer );

	foreach ($queries as $query) {
		$query = trim( $query );
		
		if ($query != '' && $query{0} != '#') {
			$database->setQuery( $query );
			if ($debug) {
				echo '<pre>'.$database->getQuery().'</pre>';
			}
			$database->query();
			if ($database->getErrorNum() > 0) {
				$errors[] = array (
					'msg' => $database->getErrorMsg(),
					'sql' => $query
				);
			}
		}
	}

	return count( $errors );
}

/**
 * @param string
 * @return array
 */
function splitSql( $sql ) {
	$sql = trim( $sql );
	$sql = preg_replace( "/\n\#[^\n]*/", '', "\n" . $sql );

	$buffer = array();
	$ret = array();
	$in_string = false;

	for ($i = 0; $i < strlen( $sql )-1; $i++) {
		
		if($sql[$i] == ";" && !$in_string) {
			$ret[] = substr($sql, 0, $i);
			$sql = substr($sql, $i + 1);
			$i = 0;
		}

		if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
			$in_string = false;
		} else if(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
			$in_string = $sql[$i];
		}

		if (isset( $buffer[1] )) {
			$buffer[0] = $buffer[1];
		}

		$buffer[1] = $sql[$i];
	}

	if(!empty($sql)) {
		$ret[] = $sql;
	}

	return($ret);
}
?>