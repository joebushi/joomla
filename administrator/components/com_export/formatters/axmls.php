<?php
/**
 * @version $Id: axmls.php,v 1.1 2005/08/25 14:14:16 johanjanssens Exp $
 * @package Mambo
 * @subpackage Export
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$exportFormatters['axmls'] = 'axmlsFormatter';

class axmlsFormatter {
	function options( &$tmpl ) {
		$tmpl->readTemplatesFromInput( '../formatters/axmls.html' );
		return $tmpl->getParsedTemplate( 'axmls-formatter-options' );
	}

	/**
	 * Creates an XMI document that can be imported into Visual Paradigm
	 */
	function export( &$tables, &$table_fields, &$table_creates, &$options ) {
		global $database;

		$source = mosGetParam( $options, 'source', '' );
		$sourceStructure = eregi( 's', $source );
		$sourceData = eregi( 'd', $source );

		mosFS::load( 'includes/adodb/adodb-xmlschema.inc.php' );
		$schema = new adoSchema( $database->_resource );
		$xml = $schema->ExtractSchema( $sourceData, $tables );
		$xml = str_replace( '<table name="' . $database->getPrefix(), '<table name="', $xml );
		return $xml;
	}

}


?>