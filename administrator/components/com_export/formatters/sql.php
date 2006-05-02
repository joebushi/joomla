<?php
/**
 * @version $Id: sql.php,v 1.1 2005/08/25 14:14:16 johanjanssens Exp $
 * @package Mambo
 * @subpackage Export
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$exportFormatters['sql'] = 'sqlFormatter';

class sqlFormatter {
	function options( &$tmpl ) {
		$tmpl->readTemplatesFromInput( '../formatters/sql.html' );
		return $tmpl->getParsedTemplate( 'sql-formatter-options' );
	}

	/**
	 * Creates an XMI document that can be imported into Visual Paradigm
	 */
	function export( &$tables, &$table_fields, &$table_creates, &$options ) {
		$source = mosGetParam( $options, 'source', '' );

		$sourceStructure = eregi( 's', $source );
		$sourceData = eregi( 'd', $source );

		$buffer = '';
		foreach ($tables as $table) {
			if ($sourceStructure) {
				$buffer .= "#\n# Structure for table `$table`\n#\n";
				$buffer .= $this->_createTableStructure( $table, $table_creates[$table], $options );
				$buffer .= "\n\n";
			}
			if ($sourceData) {
				$buffer .= "#\n# Data for table `$table`\n#\n";
				$buffer .= $this->_createTableData( $table, $table_fields[$table], $options  );
				$buffer .= "\n\n";
			}
		}
		return $buffer;
	}

	/**
	 * @param string The table name
	 * @param array The create syntax for the tables
	 * @param boolean A switch to add the DROP TABLE systax (if true)
	 */
	function _createTableStructure( $table, &$create, $options=array() ) {
		$dropTables = mosGetParam( $options, 'droptables', 0 );

		$buffer = '';
		if ($dropTables) {
			$buffer .= 'DROP TABLE IF EXISTS ' . $table  . ";\n";
		}
		$buffer .= $create . ';';
		return $buffer;
	}

	/**
	 *
	 */
	function _createTableData( $table, &$fields, $options=array() ) {
		global $database;

		$numbers = 'DATE TIME DATETIME CHAR VARCHAR TEXT TINYTEXT MEDIUMTEXT LONGTEXT BLOB TINYBLOB MEDIUMBLOB LONGBLOB ENUM SET';
		$completeInserts = mosGetParam( $options, 'cinsert', 0 );

		$fieldNames = '';
		if ($completeInserts) {
			$fieldNames = '(`';
			$fieldNames .= implode( '`,`', array_keys( $fields ) );
			$fieldNames .= '`)';
		}

		$database->setQuery( 'SELECT * FROM ' . $table );
		$rows = $database->loadAssocList();

		$buffer = '';
		foreach ($rows as $row) {
			$buffer .= 'INSERT INTO ' . $table . ' ' . $fieldNames . ' VALUES ';
			$values = array();
			foreach ($row as $key => $value) {
				$value = addslashes( $value );
				$value = str_replace( "\n", '\r\n', $value );
				$value = str_replace( "\r", '', $value );
				if (preg_match( "/\b" . $fields[$key] . "\b/i", $numbers )) {
					$value = "'$value'";
				}
				$values[] = $value;
			}
			$buffer .= '(' . implode( ',', $values ) . ");\n";
		}

		return $buffer;
	}

}


?>