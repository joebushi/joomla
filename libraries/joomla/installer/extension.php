<?php
/**
 * @version		$Id: installer.php 9783 2007-12-31 14:56:55Z pasamio $
 * @package		Joomla.Framework
 * @subpackage	Installer
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
 * Extension object
 *
 * @author 		Sam Moffatt <pasamio@gmail.com> 
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.6
 */
class JExtension extends JObject {
		
	var $filename = '';
	var $type = '';
	var $id = '';
	var $client = 'site'; // valid for modules, templates and languages; set by default
	var $group =  ''; // valid for plugins
	
	function __construct($element=null) {
		if($element && is_a($element, 'JSimpleXMLElement')) {
			$this->type = $element->attributes('type');
			$this->id = $element->attributes('id');
			switch($this->type) {
				case 'component':
					// by default a component doesn't have anything
					break;
				case 'module':
				case 'template':
				case 'language':
					$this->client = $element->attributes('client');
					$this->client_id = JApplicationHelper::getClientInfo($this->client,1);
					$this->client_id = $this->client_id->id;
					break;
				case 'plugin':
					$this->group = $element->attributes('group');
					break;
				default:
					// catch all
					// get and set client and group if we don't recognise the extension
					if($client = $element->attributes('client')) {
						$this->client_id = JApplicationHelper::getClientInfo($this->client,1);
						$this->client_id = $this->client_id->id;
					}
					if($group = $element->attributes('group')) {
						$this->group = $element->attributes('group');
					}
					break;
			}
			$this->filename = $element->data();
		}
	}
}