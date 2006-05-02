<?php
/**
 * @version $Id: dbtable.php,v 1.1 2005/08/25 14:14:16 johanjanssens Exp $
 * @package Mambo
 * @subpackage Export
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$exportFormatters['class'] = 'dbTableFormatter';

class dbTableFormatter {
	function options( &$tmpl ) {
		$tmpl->readTemplatesFromInput( '../formatters/dbtable.html' );
		return $tmpl->getParsedTemplate( 'dbtable-formatter-options' );
	}

	/**
	 * Creates an XMI document that can be imported into Visual Paradigm
	 */
	function export( &$tables, &$table_fields, &$table_creates, &$options ) {
		$buffer = '';
		foreach ($tables as $table) {
			$buffer .= $this->_createClass( $table, $table_fields[$table] );
		}
		return $buffer;
	}

	function _createClass( &$table, &$table_fields ) {
		global $database;

		$tableName = str_replace( $database->getPrefix(), '', $table );
		$className = 'mos' . ucfirst( strtolower( $tableName ) );
		$buffer = "\n/**";
		$buffer .= "\n* Class $className";
		$buffer .= "\n*/";
		$buffer .= "\nclass $className extends mosDBTable {";
		foreach ($table_fields as $k=>$v) {
			$buffer .= "\n/** @var $v */";
			$buffer .= "\n	var \$$k;";
		}
		$buffer .= "\n\n	function $className() {";
		$buffer .= "\n		global \$database;";
		$buffer .= "\n		\$this->mosDBTable( '#__$tableName', 'id', \$database );";
		$buffer .= "\n	}";
		$buffer .= "\n}\n";

		return $buffer;
	}
}
?>