<?php
/**
* @version $Id: editor.php,v 1.1 2005/08/25 14:18:17 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_editor;

if ( !defined( '_MOS_EDITOR_INCLUDED' ) ) {
	global $my;

	if ( $my && $editor = $my->params->get( 'editor' ) ) {
		$mosConfig_editor = $editor;
	}
	if ($mosConfig_editor == '') {
		$mosConfig_editor = 'none';
	}

	//$_MAMBOTS->loadBotGroup( 'editors' );
	$_MAMBOTS->loadBot( 'editors', $mosConfig_editor, 1 );
	$_MAMBOTS->loadBotGroup( 'editors-xtd' );

	function initEditor() {
		global $mainframe, $_MAMBOTS;
		
		if(!$mainframe->get('loadEditor')) {
			return false;
		}

		$results = $_MAMBOTS->trigger( 'onInitEditor' );
		foreach ($results as $result) {
		    if (trim($result)) {
			   echo $result;
			}
		}
	}
	
	function getEditorContents( $editorArea, $hiddenField, $return=0 ) {
		global $mainframe, $_MAMBOTS;
		
		$mainframe->set('loadEditor', true);
		
		$results = $_MAMBOTS->trigger( 'onGetEditorContents', array( $editorArea, $hiddenField ) );
		foreach ( $results as $result ) {
		    if ( trim( $result ) ) {
		    	if ( $return ) {
		    		return $result;
		    	} else {
			   	echo $result;
		    	}
			}
		}
	}
	
	// just present a textarea
	function editorArea( $name, $content, $hiddenField, $width, $height, $col, $row, $showbut=1, $return=0 ) {
		global $mainframe, $_MAMBOTS;
		
		$mainframe->set('loadEditor', true);

		$results = $_MAMBOTS->trigger( 'onEditorArea', array( $name, $content, $hiddenField, $width, $height, $col, $row, $showbut ) );
		foreach ( $results as $result ) {
		    if ( trim( $result ) ) {
		    	if ( $return ) {
		    		return $result;
		    	} else {
			   	echo $result;
		    	}
			}
		}
	}
	define( '_MOS_EDITOR_INCLUDED', 1 );
}
?>