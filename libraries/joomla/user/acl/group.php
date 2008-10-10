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

class JACLGroup EXTENDS JObject {
	public $id = 0;
	public $name = '';
	public $value = '';
	public $parent_id = 0;
	public $lft = 0;
	public $rgt = 0;
	public $valid = false;

	protected $db = null;
	protected $type = '';

	public function __construct($id = 0, $type = '') {
		$this->db = JFactory::getDBO();
		$this->id = $id;
		switch(strtolower($type)) {
			case 'aro':
			case 'axo':
				$this->type = strtolower($type);
				break;
			default:
				JError::raiseError(500, 'JACLGroup::__construct() Invalid Type', $type);
		}
		if($id != 0) {
			$this->valid = $this->load();
		}
	}

	public function load() {
		if($this->id == 0) return;
		$sql = 'SELECT id, name, parent_id, value, lft, rgt FROM #__core_acl_'.$this->type.'_groups WHERE id = '.(int) $this->id;
		$this->db->setQuery($sql);
		$row = $this->db->loadObject();
		if(!is_object($row)) {
			return false;
		}
		return $this->bind($row);
	}

	public function save() {
		if(empty($this->name)) {
			$this->setError('Section Value field must not be empty');
			return false;
		} elseif(empty($this->name)) {
			$this->setError('Name field must not be empty');
			return false;
		}
		if($this->id == 0) {
			//Adding Group!
			//TODO: Impliment Adding of new groups
		} else {
			//Editing Group
			$old = new JACLGroup($this->id, $this->type);
			if(!$old->valid) {
				//Invalid ID!!!
				$this->id = 0;
				return $this->save();
			}
			$set = array();
			if($old->name != $this->name) {
				$set['name'] = $this->name;
			}
			if($old->parent_id != $this->parent_id) {
				$set['parent_id'] = $this->parent_id;
			}
			if($old->value != $this->value) {
				$set['value'] = $this->value;
			}

			if(!empty($this->parent_id)) {
				if($this->id == $this->parent_id) {
					$this->setError('Groups cannot be a parent to themselves');
					return false;
				}
				$children = $this->getChildren(true);
				if(!empty($children) && @in_array($this->parent_id, $children)) {
					$this->setError('Groups cannot be re-parented to their own children');
					return false;
				}
				unset($children);

				$parent = new JACLGroup($this->parent_id, $this->type);
				if(!$parent->valid) {
					$this->setError('Parent group doesn\'t exist');
				}
				unset($parent);
			}

			if(empty($set)) {
				return true;
			}

			$sql = 'UPDATE #__core_acl_'.$this->type.'_groups SET ';
			$sep = '';
			foreach($set AS $k => $v) {
				$sql .= $sep . $k .' = '. $this->db->quote($v);
				$sep = ', ';
			}
			$sql .= ' WHERE id = '.(int) $this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			if(isset($set['parent_id'])) {
				//Parent ID changed, so rebuild the tree!!!
				return JACLGroup::rebuild($this->type);
			}
			return true;
		}
	}

	public function delete($erase = false) {
		if($this->id == 0) {
			$this->setError('Cannot Delete Non-Existant Object');
			return false;
		}
	}

	public static function rebuild($type, $left = 1) {

	}

	protected static function rebuildTree($type, $id, $left = 1) {

	}

	protected static function getByValue($value, $type) {
		switch(strtolower($type)) {
			case 'aro':
			case 'axo':
				$type = strtolower($type);
				break;
			default:
				return false;
		}
		$db = JFactory::getDBO();
		$sql = 'SELECT g.id
				FROM #__core_acl_'.$type.'_groups AS g
				INNER JOIN #__core_acl_groups_'.$type.'_map AS gm ON gm.group_id = g.id
				INNER JOIN #__core_acl_'.$type.' AS ao ON ao.id = gm.'.$type.'_id
				WHERE ao.value='.$db->Quote($value)
		);
		$id = $db->loadResult;
		if($id) {
			$row = new JACLGroup($id, $type);
		} else {
			$row = false;
		}
		return $row;
	}
}
