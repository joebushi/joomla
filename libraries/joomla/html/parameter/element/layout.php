<?php
/**
* @version		$Id: filelist.php 9764 2007-12-30 07:48:11Z ircmaxell $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a filelist element
 *
 * @author 		Johan Janssens <johan.janssens@joomla.org>
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementLayout extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Filelist';

	function fetchElement($name, $value, &$node, $control_name)
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		// path to images directory
		$component	= $node->attributes('component');
		$view		= $node->attributes('view');

		$options[] = JHTML::_('select.option', JText::_('Select a layout'), '');

		$return = false;
		$folder	= JPATH_ROOT.DS.'components'.DS.$component.DS.'views'.DS.$view.DS.'tmpl';
		if (is_dir( $folder ))
		{
			$files = JFolder::files($folder, '.xml$');
			if (count($files)) {
				foreach ($files as $file)
				{
					// Load view metadata if it exists
					$xml =& JFactory::getXMLParser('Simple');
					if ($xml->loadFile($folder.DS.$file)) {
						if (isset( $xml->document )) {
							$data = $xml->document->getElementByPath('layout');
						}
					}

					if ($data) {
						if ($data->attributes('hidden') != 'true') {
							$title = $data->attributes('title');
						}
					} else {
						// Add default info for the layout
						$title = ucfirst($layout).' '.JText::_('Layout');
					}
					$options[] = JHTML::_('select.option', JFile::stripext($file), JText::_($title));
				}
			}
		}
		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, "param$name");
	}
}