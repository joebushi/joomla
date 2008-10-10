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

class JACLObjectSection EXTENDS JObject {
	public $id = 0;
	public $name = '';
	public $value = 0;
	public $order = 0;
	public $hidden = 0;
	
	protected $db = null;
	protected $type = '';

	public function __construct($id = 0, $type = '') {
		$this->db = JFactory::getDBO();
		$this->id = $id;
		switch(strtolower($type)) {
			case 'aco':
			case 'aro':
			case 'axo':
			case 'acl'
				$this->type = strtolower($type);
				break;
			default:
				JError::raiseError(500, 'JACLObjectSection::__construct() Invalid Type', $type);
		}
		if($id != 0) {
			$this->load();
		}
	}

	public function load() {
		if($this->id == 0) return;
		$sql = 'SELECT id, value, order_value AS order, name, hidden FROM #__core_acl_'.$this->type.'_sections WHERE id = '.(int) $this->id;
		$this->db->setQuery($sql);
		$row = $this->db->loadObject();
		if(!is_object($row)) {
			return false;
		}
		return $this->bind($row);
	}

	public function save() {
		if(empty($this->name)) {
			$this->setError('Name field must not be empty');
			return false;
		}
		if($this->id == 0) {
			$sql = 'INSERT INTO #__core_acl_'.$this->type.'_sections 
						(value, order_value, name, hidden)
					VALUES
						('.$this->db->quote($this->value).','.(int) $this->order.','.$this->db->quote($this->name).','.(int) $this->hidden.')';
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			$this->id = $this->db->insertid();
			return true;
		} else {
			$old = new JACLObjectSection($this->id, $this->type);
			$sql = 'UPDATE #__core_acl_'.$this->type.'_sections SET
						value = '.$this->db->quote($this->value).',
						order_value = '.(int) $this->order.',
						name = '.$this->db->quote($this->name).',
						hidden = '.(int) $this->hidden.'
					WHERE id = '.$this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}

			if($old->value != $this->value) {
				$sql = 'UPDATE #__core_acl_'.$this->type.' SET
							section_value = '.$this->db->quote($this->section_value).'
						WHERE 
							section_value = '.$this->db->quote($old->section_value);
				$this->db->setQuery($sql);
				if(!$this->db->query()) {
					$this->setError($this->db->getErrorMsg());
					$sql = 'UPDATE #__core_acl_'.$this->type.'_sections SET
								value = '.$this->db->quote($old->value).',
								order_value = '.(int) $old->order.',
								name = '.$this->db->quote($old->name).',
								hidden = '.(int) $old->hidden.'
							WHERE id = '.$this->id;
					$this->db->setQuery($sql);
					if(!$this->db->query()) {
						$this->setError($this->db->getErrorMsg());
						return false;
					}
					return false;
				} else {
					if($this->type != 'acl') {
						$sql = 'UPDATE #__core_acl_'.$this->type.'_map SET
									section_value = '.$this->db->quote($this->section_value).'
								WHERE 
									section_value = '.$this->db->quote($old->section_value);
						$this->db->setQuery($sql);
						if(!$this->db->query()) {
							$this->setError($this->db->getErrorMsg());
							$sql = 'UPDATE #__core_acl_'.$this->type.' SET
										section_value = '.$this->db->quote($old->section_value).'
									WHERE 
										section_value = '.$this->db->quote($this->section_value);
							$this->db->setQuery($sql);
							if(!$this->db->query()) {
								$this->setError($this->db->getErrorMsg());
								return false;
							}
							$sql = 'UPDATE #__core_acl_'.$this->type.'_sections SET
										value = '.$this->db->quote($old->value).',
										order_value = '.(int) $old->order.',
										name = '.$this->db->quote($old->name).',
										hidden = '.(int) $old->hidden.'
									WHERE id = '.$this->id;
							$this->db->setQuery($sql);
							if(!$this->db->query()) {
								$this->setError($this->db->getErrorMsg());
								return false;
							}
							return false;
						} 
					}
				}
			}
			return true;
		}	
	}

	public function delete($erase = false) {
		if($this->id == 0) {
			$this->setError('Cannot Delete Non-Existant Section');
			return false;
		}
		if(empty($this->value)) {
			$this->load();
		}

		$objects = JACLObject::get($this->value, 1, $this->type);
		if(!empty($objects)) {
			if($erase) {
				foreach($objects AS $object) {
					if(!$object->delete()) {
						$this->setError($object->getError());
						return false;
					}
				}

			} else {
				$this->setError('Cannot delete non-empty Section');
				return false;
			}
		} 
		$sql = 'DELETE FROM #__core_acl_'.$this->type.'_sections WHERE id = '.(int) $this->id;
		$this->db->setQuery($sql);
		if(!$this->db->query()) {
			$this->setError($this->db->getErrorMsg());
			return false;
		}
		return true;
	}

	public static function get($args = array(), $type = '') {
		switch(strtolower($type)) {
			case 'aco':
			case 'aro':
			case 'axo':
			case 'acl':
				$type = strtolower($type);
				break;
			default:
				return false;
		}

		if(!isset($args['name']) && !isset($args['value'])) {
			return false;
		}
		$sql = 'SELECT id FROM #__core_acl_'.$this->type.'_sections';
		$where = array();
		if(isset($args['name'])) {
			$where[] = 'name = '.$db->quote($args['name']);
		}
		if(isset($args['value'])) {
			$where[] = 'value = '.$db->quote($args['value']);
		}

		if(!empty($where)) {
			$sql .= ' WHERE '. implode(' AND ', $where);
		}
		$db->setQuery($sql);
		$rows = $db->loadResultList();
		if(!empty($rows)) {
			foreach($rows AS $id) {
				$return[] = new JACLObjectSection($id, $type);
			}
		}
		return $return;
	}	

}
