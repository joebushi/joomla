<?php
/**
* @version $Id: editor.htmlarea3.php,v 1.1 2005/08/25 14:18:17 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

function initEditor() {
	global $mosConfig_live_site, $mainframe;
		
	if(!$mainframe->get('loadEditor')) {
		return false;
	}
?>
<script type="text/javascript"> 
<!--
_editor_url ="<?php echo $mosConfig_live_site;?>/editor/htmlarea3/";
//-->
</script>
<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/editor/htmlarea3/htmlarea.js"></script>
<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/editor/htmlarea3/dialog.js"></script>
<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/editor/htmlarea3/lang/en.js"></script>
<style type="text/css">@import url(<?php echo $mosConfig_live_site;?>/editor/htmlarea3/htmlarea.css)</style>
<script type="text/javascript"> 
<!--
// load the plugin files
HTMLArea.loadPlugin("TableOperations");
HTMLArea.loadPlugin("EnterParagraphs");
var editor = null;

/** Wrapper around the editor specific update function in JavaScript
*/
function updateEditorContents( editorName, newValue ) {
	//TODO: correct call
}
//-->
</script>
<?php
}

function editorArea( $name, $content, $hiddenField, $width, $height, $col, $row, $showbut=1 ) {
	global $mainframe;
		
	$mainframe->set('loadEditor', true);
?>
<textarea name="<?php echo $hiddenField; ?>" id="<?php echo $hiddenField; ?>" cols="<?php echo $col; ?>" rows="<?php echo $row; ?>" style="width:<?php echo $width; ?>; height:<?php echo $height; ?>"><?php echo $content; ?></textarea>
<script language="JavaScript1.2" defer="defer">
<!--
// create the editor
var editor = new HTMLArea("<?php echo $hiddenField ?>");

// retrieve the config object
var config = editor.config;

config.sizeIncludesToolbar = false;
config.height= "200px";

config.registerButton({
	id	   : "mosimage",
	tooltip   : "Insert {mosimage} tag",
	image	: _editor_url + "images/ed_mos_image.gif",
	textMode  : false,
	action    : function(editor, id) {
		editor.focusEditor();
		editor.insertHTML('{mosimage}');
	}
});

config.registerButton({
	id	   : "mospagebreak",
	tooltip   : "Insert {mospagebreak} tag",
	image	: _editor_url + "images/ed_mos_pagebreak.gif",
	textMode  : false,
	action    : function(editor, id) {
		editor.focusEditor();
		editor.insertHTML('{mospagebreak}');
	}
});

config.toolbar = [
[ "fontname", "space",
"fontsize", "space",
"formatblock", "space",
"bold", "italic", "underline", "separator",
"strikethrough", "subscript", "superscript", "separator",
"mosimage", "mospagebreak" ],

[ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
"insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
"forecolor", "hilitecolor", "textindicator", "separator",
"inserthorizontalrule", "createlink", "insertimage", "inserttable", "htmlmode"]
];

// register the TableOperations plugin with our editor
editor.registerPlugin(TableOperations);
editor.registerPlugin(EnterParagraphs);
editor.generate('<?php echo $hiddenField ?>');
//-->
</script>
<?php
}

function getEditorContents( $editorArea, $hiddenField ) {
	global $mainframe;
		
	if(!$mainframe->set('loadEditor', true)) {
		return false;
	}
}