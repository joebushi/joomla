<?php
/**
 * @version		$Id: form.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla.Framework
 * @subpackage	Form
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.html.form');

/**
 * Form handler
 *
 * @author 		Hannes Papenberg <hannes.papenberg@community.joomla.org>
 * @package 	Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class AccessParameters extends JRegistry
{
	/**
	 * The xml elements
	 *
	 * @access	private
	 * @var	object
	 * @since	1.6
	 */
	var $_xml = null;

	/**
	 * The HTML elements to render
	 *
	 * @access	private
	 * @var		object
	 * @since	1.6
	 */
	var $_html = array();

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	string The raw parms text
	 * @param	string Path to the xml setup file or XML data
	 * @param	string Tagname used in the XML file
	 * @since	1.5
	 */
	function __construct($extension)
	{
		parent::__construct('_default');
		
		$this->loadSetupDirectory(JPATH_ADMINISTRATOR.DS.'components'.DS.$extension, 'access/.*/.xml');
	}
	
	function setXML( &$xml )
	{
		if (is_object( $xml ))
		{
			if (!$group = $xml->attributes( 'name' )) {
				$group = 'system';
			}
			$this->_xmlAttributes[$group] = $xml->attributes();
			if(isset($this->_xml[$group])) {
				foreach($xml->children() as $child) {
					$this->_xml[$group]->_children[] = $child;
				}
			} else {
				$this->_xml[$group] = $xml;
			}
			if ($dir = $xml->attributes( 'addpath' )) {
				$this->addElementPath( JPATH_ROOT . str_replace('/', DS, $dir) );
			}
		}
	}

	function loadSetupFile($path)
	{
		static $file;

		$result = false;

		if(!is_array($file))
		{
			$file = array();
		}
		if ($path && !in_array($path, $file))
		{
			$file[] = $path;
			$xml = & JFactory::getXMLParser('Simple');
			
			if ($xml->loadFile($path))
			{
				if ($elements = & $xml->document->extension) {
					foreach ($elements as $element)
					{
						$this->setXML( $element );
						$result = true;
					}
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}
	
	/**
	 * Loads all xml setup files from a directory and parses it
	 *
	 * @access	public
	 * @param	string	directory to xml setup file
	 * @return	object
	 * @since	1.6
	 */
	function loadSetupDirectory($path, $filter = '.xml')
	{
		$result = false;

		jimport('joomla.filesystem.folder');
		if(!JFolder::exists($path)) {
			return $result;
		}
		$files = JFolder::files($path, $filter, false, true);
		if (count($files))
		{
			foreach($files as $file)
			{
				$result = $this->loadSetupFile($file);
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}
	
	function render()
	{
		foreach($this->_xml as $extension => $parameters)
		{
			foreach($parameters as $parameter)
			{
				if($parameter->getName() == 'action' && $parameter->getAttribute('content'))
				{
					$this->_html[$extension]['content'][] = $parameter;
				} elseif($parameter->getName() == 'action') {
					$this->_html[$extension]['action'][] = $parameter;
				}
			}
		}
	}
	
	function getChanged()
	{
		
	}
}