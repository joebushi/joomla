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

class UsersHelper
{
	var $_groups = null;
	
	var $_html = '';
	
	function getGroupTree($groups)
	{
		$this->_groups = $groups->getChildren();
		$this->_html = '<ul id="groups">';
		$this->_getGroupTree();
		$this->_html .= '</ul>';
		return $this->_html;
	}
	function _getGroupTree()
	{
		$groups = $this->_groups;
		foreach($groups as $usergroup)
		{
			$this->_html .= '<li><a href="&id='.$usergroup->getId().'"><!-- icon:_open; -->'.$usergroup->getName()."</a>\n";
			if($usergroup->getChildren())
			{
				$this->_groups = $usergroup->getChildren();
				$this->_html .= "<ul>\n";
				$this->_getGroupTree();
				$this->_html .= "</ul>\n";
				$this->_groups = $usergroup->getParent();
			}
			$this->_html .= "</li>\n";
		}
	}
}

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
	
	var $_extensions = array();
	
	var $_description = array();
	
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	string The raw parms text
	 * @param	string Path to the xml setup file or XML data
	 * @param	string Tagname used in the XML file
	 * @since	1.5
	 */
	function __construct($extension, $group)
	{
		parent::__construct('_default');
		if($extension == '')
		{
			$extension = 'com_users';
		}
		
		$this->loadSetupDirectory(JPATH_ADMINISTRATOR.DS.'components'.DS.$extension, '^access(.*?)\.xml$');
		$this->_render($group);		
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
	
	function render($group, $extension, $type)
	{
		return $this->_html[$group][$extension][$type];
	}
	
	function getExtensions()
	{
		return $this->_extensions;
	}
	
	function getDescription($extension)
	{
		return $this->_description[$extension];
	}
	
	function getChanged()
	{
		
	}
	
	function _render($group)
	{
		foreach($this->_xml as $extension => $parameters)
		{
			if(!in_array($extension,$this->_extensions))
			{
				$this->_extensions[] = $extension;
			}
			if($parameters->getElementByPath('description'))
			{
				$description = $parameters->getElementByPath('description');
				$this->_description[$extension] = $description->data();
			}
			foreach($parameters->children() as $parameter)
			{
				if($parameter->name() == 'action' && $parameter->attributes('content'))
				{
					$this->_html[$group][$extension]['content'][] = $parameter;
				} elseif($parameter->name() == 'action') {
					$this->_html[$group][$extension]['action'][] = $parameter;
				}
			}
		}
		$option1 = new stdClass();
		$option1->value = '';
		$option1->text = 'Inherit';
		$option2 = new stdClass();
		$option2->value = '0';
		$option2->text = 'Deny';
		$option3 = new stdClass();
		$option3->value = '1';
		$option3->text = 'Allow';

		$access = JAuthorizationRule::getInstance();
		$selection = array($option1, $option2, $option3);
		
		foreach($this->_html[$group] as $extension => $objects)
		{
			$actionoutput = '<table>';
			foreach($objects['action'] as $action)
			{
				$actionoutput .= "\n<tr><td>".$action->attributes('name')."</td>\n";
				$actionoutput .= '<td>'.JHTML::_('select.radiolist', $selection, $extension.$action->attributes('value'), NULL, 'value', 'text', $access->authorizeGroup($group, $extension,$action->attributes('value'))).'</td></tr>';
			}
			$actionoutput .= '</table>';
			$this->_html[$group][$extension]['action'] = $actionoutput;
		}
		foreach($this->_html[$group] as $extension => $objects)
		{
			$contentitems = new JAuthorizationContentItem();
			$contentitems = $contentitems->getContentItems($extension);
			$contentoutput = '<table><tr>';
			$contentoutput .= '<th>Contentitem</th>';
			foreach($objects['content'] as $action)
			{
				$contentoutput .= '<th>'.$action->attributes('name').'</th>';
			}
			$contentoutput .= '</tr>';
			foreach($contentitems as $contentitem)
			{
				$contentoutput .= '<tr><td>'.$contentitem->getName().'</td>';
				foreach($objects['content'] as $action)
				{
					$contentoutput .= '<td>'.JHTML::_('select.genericlist', $selection, $extension.'content'.$action->attributes('value'), 'listlength="1"', 'value', 'text', $access->authorizeGroup($group, $extension,$action->attributes('value'), $contentitem->getValue())).'</td>';
				}
				$contentoutput .= '</tr>';
			}
			$contentoutput .= '</table>';
			$this->_html[$group][$extension]['content'] = $contentoutput;
		}
	}
}