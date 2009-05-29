<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * CodeMirror Editor Plugin
 *
 * @package Editors
 * @since 1.6
 */
class plgEditorCodemirror extends JPlugin
{
	/**
	 * Method to handle the onInitEditor event.
	 *  - Initializes the Editor
	 *
	 * @access public
	 * @return string JavaScript Initialization string
	 * @since 1.5
	 */
	function onInit()
	{
		$document = &JFactory::getDocument();
		$document->addScript(JURI::root().'plugins/editors/codemirror/codemirror.js');
		$document->addStyleDeclaration("
		.CodeMirror-line-numbers {
		width: 2.2em;
		color: #aaa;
		background-color: #eee;
		text-align: right;
		padding-right: .3em;
		font-size: 10pt;
		font-family: monospace;
		padding-top: .4em;
		}");
		
		return '';
	}

	/**
	 * Copy editor content to form field
	 *
	 * @param string 	The name of the editor
	 */
	function onSave($editor) {
		// this is handled by Codemirror itself by using an event listener
		return;
	}

	/**
	 * Get the editor content
	 *
	 * @param string 	The name of the editor
	 */
	function onGetContent($editor) {
		return "Joomla.editors.instances['$editor'].getCode();\n";
	}

	/**
	 * Set the editor content
	 *
	 * @param string 	The name of the editor
	 */
	function onSetContent($editor, $html) {
		return "Joomla.editors.instances['$editor'].setCode($html);\n";
	}

	/**
	 * No WYSIWYG Editor - display the editor
	 *
	 * @param string The name of the editor area
	 * @param string The content of the field
	 * @param string The name of the form field
	 * @param string The width of the editor area
	 * @param string The height of the editor area
	 * @param int The number of columns for the editor area
	 * @param int The number of rows for the editor area
	 */
	function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true)
	{
		// Only add "px" to width and height if they are not given as a percentage
		if (is_numeric($width)) {
			$width .= 'px';
		}
		if (is_numeric($height)) {
			$height .= 'px';
		}

		$buttons = $this->_displayButtons($name, $buttons);
		$editor  = "<textarea name=\"$name\" id=\"$name\" cols=\"$col\" rows=\"$row\">$content</textarea>" . $buttons;
		$editor	 .= "<script type=\"text/javascript\">
			var editor = CodeMirror.fromTextArea('$name', {
				height: '$height',
				width: '$width',
				parserfile: 'parsexml.js',
				stylesheet: '".JURI::root()."plugins/editors/codemirror/css/xmlcolors.css',
				path: '".JURI::root()."plugins/editors/codemirror/',
				continuousScanning: 500,
				lineNumbers: true,
				textWrapping: false
			});
			Joomla.editors.instances['$name'] = editor;
		</script>";

		return $editor;
	}

	function onGetInsertMethod($name)
	{
		$doc = & JFactory::getDocument();

		$js= "\tfunction jInsertEditorText(text, editor) {
				Joomla.editors.instances[editor].replaceSelection(text);\n
		}";
		$doc->addScriptDeclaration($js);

		return true;
	}

	function _displayButtons($name, $buttons)
	{
		// Load modal popup behavior
		JHtml::_('behavior.modal', 'a.modal-button');

		$args['name'] = $name;
		$args['event'] = 'onGetInsertMethod';

		$return = '';
		$results[] = $this->update($args);
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
				$return .= $result;
			}
		}

		if (!empty($buttons))
		{
			$results = $this->_subject->getButtons($name, $buttons);

			/*
			 * This will allow plugins to attach buttons or change the behavior on the fly using AJAX
			 */
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";
			foreach ($results as $button)
			{
				/*
				 * Results should be an object
				 */
				if ($button->get('name'))
				{
					$modal		= ($button->get('modal')) ? 'class="modal-button"' : null;
					$href		= ($button->get('link')) ? 'href="'.$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$return .= "<div class=\"button2-left\"><div class=\"".$button->get('name')."\"><a ".$modal." title=\"".$button->get('text')."\" ".$href." ".$onclick." rel=\"".$button->get('options')."\">".$button->get('text')."</a></div></div>\n";
				}
			}
			$return .= "</div>\n";
		}

		return $return;
	}
}