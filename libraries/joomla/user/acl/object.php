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

require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'acl'.DS.'rule.php');

class JACLObject EXTENDS JObject {
	public $id = 0;
	public $section_value = '';
	public $value = '';
	public $order = '';
	public $name = '';
	public $hidden = false;
	
	protected $db = null;
	protected $type = '';

	public function __construct($id = 0, $type = '') {
		$this->db = JFactory::getDBO();
		$this->id = $id;
		switch(strtolower($type)) {
			case 'aco':
			case 'aro':
			case 'axo':
				$this->type = strtolower($type);
				break;
			default:
				JError::raiseError(500, 'JACLObject::__construct() Invalid Type', $type);
		}
		if($id != 0) {
			$this->load();
		}
	}

	public function __get($var) {
		if($var == 'type') {
			return $this->type;
		}
		return null;
	}

	public function load() {
		if($this->id == 0) return;
		$sql = 'SELECT section_value, value, order_value AS order, name, hidden FROM #__core_acl_'.$this->type.' WHERE id = '.(int) $this->id;
		$this->db->setQuery($sql);
		$row = $this->db->loadObject();
		if(!is_object($row)) {
			return false;
		}
		$this->bind($row);
		return true;
	}

	public function save() {
		if(empty($this->section_value)) {
			$this->setError('Section Value field must not be empty');
			return false;
		} elseif(empty($this->name)) {
			$this->setError('Name field must not be empty');
			return false;
		}
		if($this->id == 0) {
			//Adding Object!
			$sql = 'SELECT CASE WHEN o.id IS NULL THEN 0 ELSE 1 END AS object_exists, o.id
					FROM #__core_acl_'.$this->type.'_sections AS s
					LEFT JOIN #__core_acl_'.$this->type.' AS o ON (s.value = o.section_value AND o.value = '.$this->db->quote($this->value).')
					WHERE s.value = '.$this->db->quote($this->section_value);
			$this->db->setQuery($sql);
			$check = $this->db->loadAssoc();
			if(!is_array($check)) {
				$this->setError('Object Type does not exist', $this->section_value);
				return false;
			} elseif($check['object_exists']) {
				$this->id = $check['id'];
				//Didn't load it prior to saving... Force save again
				return $this->save();
			}
			$sql = 'INSERT INTO #__core_acl_'.$this->type.' 
						(section_value, value, order_value, name, hidden)
					VALUES
						('.$this->db->quote($this->section_value).','.$this->db->quote($this->value).','.(int) $this->order.','.$this->db->quote($this->name).','.(int) $this->hidden.')';
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			$this->id = $this->db->insertid();
			return true;
		} else {
			$old = new JACLObject($this->id, $this->type);
			$sql = 'UPDATE #__core_acl_'.$this->type.' SET
						value = '.$this->db->quote($this->value).',
						section_value = '.$this->db->quote($this->section_value).',
						order_value = '.(int) $this->order.',
						name = '.$this->db->quote($this->name).',
						hidden = '.(int) $this->hidden.'
					WHERE id = '.$this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}

			if($old->value != $this->value || $old->section_value != $this->section_value) {
				$sql = 'UPDATE #__core_acl_'.$this->type.'_map SET
							value = '.$this->db->quote($this->value).',
							section_value = '.$this->db->quote($this->section_value).'
						WHERE 
							section_value = '.$this->db->quote($old->section_value).'
						AND
							value = '.$this->db->quote($old->value);
				$this->db->setQuery($sql);
				if(!$this->db->query()) {
					$this->setError($this->db->getErrorMsg());
					$sql = 'UPDATE #__core_acl_'.$this->type.' SET
								value = '.$this->db->quote($old->value).',
								section_value = '.$this->db->quote($old->section_value).',
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
			return true;
		}
	}

	public function delete($erase = false) {
		if($this->id == 0) {
			$this->setError('Cannot Delete Non-Existant Object');
			return false;
		}
		if(empty($this->value) || empty($this->section_value)) {
			$this->load();
		}

		$sql = 'SELECT acl_id FROM #__core_acl_'.$this->type.'_map WHERE value = '.$this->db->quote($this->value).' AND section_value = '.$this->db->quote($this->section_value);
		$this->db->setQuery($sql);
		$acl_ids = $this->db->loadResultList();
		if($erase) {
			if($this->type == 'aro' || $this->type = 'axo') {
				$sql = 'DELETE FROM #__core_acl_'.$this->type.'_map WHERE '.$this->type.'_id = '.$this->id;
				$this->db->setQuery($sql);
				if(!$this->db->query()) {
					$this->setError($this->db->getErrorMsg());
					return false;
				}
			}
			if(!empty($acl_ids)) {
				if($this->type == 'aco') {
					$orphan_acl_ids = $acl_ids;
				} else {
					$sql = 'DELETE FROM #__core_acl_'.$this->type.'_map WHERE section_value = '.$this->db->quote($this->section_value).' AND value = '.$this->db->quote($this->value);
					$this->db->setQuery($sql);
					if(!$this->db->query()) {
						$this->setError($this->db->getErrorMsg());
						return false;
					}
					$sql = 'SELECT a.id
							FROM #__core_acl_acl AS a
							LEFT JOIN #__core_acl_'.$this->type.'_map AS b ON a.id = b.acl_id
							LEFT JOIN #__core_acl_'.$this->type.'_groups_map AS c ON a.id = c.acl_id
							WHERE b.value IS NULL
								AND b.section_value IS NULL
								AND c.group_id IS NULL
								AND a.id IN ('.implode(',', $acl_ids).')';
					$this->db->setQuery($sql);
					$orphan_acl_ids = $this->db->loadResultList();
				}
				if(!empty($orphan_acl_ids)) {
					foreach($orphan_acl_ids AS $id) {
						$acl = new JACLRule($id);
						$acl->delete();
					}
				}
			}
			$sql = 'DELETE FROM #__core_acl_'.$this->type.' WHERE id = '.(int) $this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			return true;
		}
		$group_ids = false;
		if($this->type == 'axo' || $this->type == 'aro') {
			$sql = 'SELECT group_id FROM #__core_acl_'.$this->type.'_groups_map WHERE '.$this->type.'_id = '.(int) $this->id;
			$this->setQuery($sql);
			$group_ids = $this->db->loadResultList();
		}
		if(!empty($acl_ids) || !empty($group_ids)) {
			$this->setError('Object still referenced by GROUPS or ACLS', array('groups'=>$group_ids, 'acls'=>$acl_ids));
			return false;
		} else {
			$sql = 'DELETE FROM #__core_acl_'.$this->type.' WHERE id = '.(int) $this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			return true;
		}
	}

	public function getGroups($recurse = false) {
		if($this->type == 'aco' || $this->id == 0) {
			return false;
		}
	
		if($recurse) {
			$sql = 'SELECT DISTINCT g.id AS group_id
					FROM #__core_acl_groups_'.$this->type.'_map AS gm
					LEFT JOIN #__core_acl_'.$this->type.'_groups AS g1 ON g1.id = gm.group_id
					LEFT JOIN #__core_acl_'.$this->type.'_groups AS g ON g.lft <= g1.lft AND g.rgt >= g1.rgt';
		} else {
			$sql = 'SELECT gm.group_id
					FROM #__core_acl_groups_'.$this->type.'_map AS gm';
		}
		$sql .= '
					WHERE gm.'.$this->type.'_id = '.(int) $this->id;
		$this->db->setQuery($sql);
		return $this->db->loadResultList();
	}

	public static function get($args, $type = '') {
		switch(strtolower($type)) {
			case 'aco':
			case 'aro':
			case 'axo':
				$type = strtolower($type);
				break;
			default:
				return false;
		}
		$db = JFactory::getDBO();
		$return = array();
		$sql = 'SELECT id FROM #__core_acl_'.$type;
		$where = array();

		if(isset($args['name'])) {
			$where[] = 'name = '.$db->quote($args['name']);
		}
		if(isset($args['value'])) {
			$where[] = 'value = '.$db->quote($args['value']);
		}
		if(isset($args['section_value'])) {
			$where[] = 'section_value = '.$db->quote($args['section_value']);
		}
		if(!isset($args['return_hidden']) || !$args['return_hidden']) {
			$where[] = 'hidden = 0';
		}
		if(!empty($where)) {
			$sql .= ' WHERE '. implode(' AND ', $where);
		}
		$db->setQuery($sql);
		$rows = $db->loadResultList();
		if(!empty($rows)) {
			foreach($rows AS $id) {
				$return[] = new JACLObject($id, $type);
			}
		}
		return $return;
	}

	public static function getUngrouped($options, $type = '') {
		switch(strtolower($type)) {
			case 'aro':
			case 'axo':
				$type = strtolower($type);
				break;
			default:
				return false;
		}
		$db = JFactory::getDBO();
		$return = array();
		$sql = 'SELECT id FROM #__core_acl_'.$type.' AS a
					LEFT JOIN #__core_acl_groups_'.$type.'_map AS b ON a.id = b.'.$type.'_id';
		$where = array();
		if(!isset($args['return_hidden']) || !$args['return_hidden']) {
			$where[] = 'hidden = 0';
		}
		if(!empty($where)) {
			$sql .= ' WHERE '. implode(' AND ', $where);
		}
		$db->setQuery($sql);
		$rows = $db->loadResultList();
		if(!empty($rows)) {
			foreach($rows AS $id) {
				$return[] = new JACLObject($id, $type);
			}
		}
		return $return;
	}


}
