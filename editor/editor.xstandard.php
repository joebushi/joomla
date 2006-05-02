<?php
/**
* @version $Id: editor.xstandard.php,v 1.1 2005/08/25 14:18:17 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
?>
<script type="text/javascript"> 
<!--
/** Wrapper around the editor specific update function in JavaScript
*/
function updateEditorContents( editorName, newValue ) {
	//TODO: correct call
}
//-->
</script>
<?php

function initEditor() {
	global $mosConfig_live_site, $mainframe;
		
	if(!$mainframe->get('loadEditor')) {
		return false;
	}
}

function editorArea( $name, $content, $hiddenField, $width, $height, $col, $row, $showbut=1 ) {
	global $mainframe;
		
	$mainframe->set('loadEditor', true);
?>
<object classid="clsid:0EED7206-1661-11D7-84A3-00606744831D" id="<?php echo $name; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
<param name="Value" value="<?php echo $content; ?>" />
</object>
<input type="hidden" name="<?php echo $hiddenField; ?>" id="<?php echo $hiddenField; ?>" value='' />
<?php
}

function getEditorContents( $editorArea, $hiddenfield ) {
	global $mainframe;
		
	if(!$mainframe->set('loadEditor')) {
		return false;
	}
?>
	document.getElementById('<?php echo $editorArea ; ?>').EscapeUNICODE = true;
	document.getElementById('<?php echo $hiddenfield ; ?>').value = document.getElementById('<?php echo $editorArea ; ?>').value;
<?php
}
?>