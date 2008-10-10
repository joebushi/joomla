<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	User
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

class JACLRule EXTENDS JObject {
	public $id = 0;
	
	protected $db = null;
	protected $type = '';

	public function __construct($id = 0) {
		$this->db = JFactory::getDBO();
		$this->id = $id;
		if($id != 0) {
			return $this->load();
		}
	}

	public function load() {
		if($this->id == 0) return;
		$sql = 'SELECT section_value, value, order_value AS order, name, hidden FROM #__core_acl_'.$this->type.' WHERE id = '.(int) $this->id;
		$this->db->setQuery($sql);
		$row = $this->db->loadObject();
		if(!is_object($row)) {
			return false;
		}
		return $this->bind($row);
	}

	public function save() {
	}

	public function delete($erase = false) {
	}

}
