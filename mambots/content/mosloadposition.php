<?php
/**
* @version $Id: mosloadposition.php,v 1.1 2005/08/25 14:21:34 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosLoadPosition' );

/**
* Mambot that loads module positions within content
*/
function botMosLoadPosition( $published, &$row, &$params, $page=0 ) {
	global $database;

 	// expression to search for
 	$regex = '/{mosloadposition\s*.*?}/i';

 	// find all instances of mambot and put in $matches
	preg_match_all( $regex, $row->text, $matches );

	// Number of mambots
 	$count = count( $matches[0] );

 	// mambot only processes if there are any instances of the mambot in the text
 	if ( $count ) {
		// load mambot params info
		$query = "SELECT id FROM #__mambots WHERE element = 'mosloadmodule' AND folder = 'content'";
		$database->setQuery( $query );
	 	$id 	= $database->loadResult();
	 	$mambot = new mosMambot( $database );
	  	$mambot->load( $id );
	 	$Mparams =& new mosParameters( $mambot->params );

	 	$style	= $Mparams->def( 'style', -2 );

 		processPositions( $row, $matches, $count, $regex, $style );
	}
}

function processPositions ( &$row, &$matches, $count, $regex, $style ) {
	global $database;

	$query = "SELECT position"
	. "\n FROM #__template_positions"
	. "\n ORDER BY position"
	;
	$database->setQuery( $query );
 	$positions 	= $database->loadResultArray();

 	for ( $i=0; $i < $count; $i++ ) {
 		$load = str_replace( 'mosloadposition', '', $matches[0][$i] );
 		$load = str_replace( '{', '', $load );
 		$load = str_replace( '}', '', $load );
 		$load = trim( $load );

		foreach ( $positions as $position ) {
	 		if ( $position == @$load ) {
				$modules	= loadPosition( $load, $style );
				$row->text 	= preg_replace( '{'. $matches[0][$i] .'}', $modules, $row->text );
				break;
	 		}
 		}
 	}

  	// removes tags without matching module positions
	$row->text = preg_replace( $regex, '', $row->text );
}

function loadPosition( $position, $style=-2 ) {
	$modules = '';

	if ( mosCountModules( $position ) ) {
		ob_start();
		mosLoadModules( $position, $style );
		$modules = ob_get_contents();
		ob_end_clean();
	}

	return $modules;
}

