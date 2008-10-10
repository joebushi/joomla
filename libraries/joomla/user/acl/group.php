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

	public function __get($var) {
		if($var == 'type') {
			return $this->type;
		}
		return null;
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

	public function isChildOf($id) {
		$group = new JACLGroup($id, $this->type);
		if($group->lft < $this->lft && $group->rgt > $this->rgt) {
			return true;
		}
		return false;
	}

	public static function rebuild($type, $left = 1) {

	}

	protected static function rebuildTree($type, $id, $left = 1) {

	}

	public static function getByValue($value, $type) {
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

	protected static function getGroupList($type, $root_id = null, $root_name = null, $inclusive) {
		$db = JFactory::getDBO();
		switch(strtolower($type)) {
			case 'aro':
			case 'axo':
				$type = strtolower($type);
				break;
			default:
				return false;
		}
		if($root_id) {
			$sql = 'SELECT lft, rgt FROM #__core_acl_'.$type.'_groups WHERE id = '.(int)$root_id;
			$db->setQuery($sql);
			$root = $db->loadObject();
		} elseif($root_name) {
			$sql = 'SELECT lft, rgt FROM #__core_acl_'.$type.'_groups WHERE name = '.$db->quote($root_name);
			$db->setQuery($sql);
			$root = $db->loadObject();
		}

		if(!isset($root) || empty($root)) {
			$root = new stdclass();
			$root->lft = 0;
			$root->rgt = 0;
		}

		$where = '';
		if($root->lft + $root->rgt !== 0) {
			if($inclusive) {
				$where = ' WHERE g1.lft BETWEEN '.(int) $root->lft.' AND '.(int) $root->rgt;
			} else {
				$where = ' WHERE g1.lft > '.(int) $root->lft.' AND g1.lft < '.(int) $root->rgt;
			}
		}
		$sql = 'SELECT g1.id, g1.name, count(g2.name) AS level
				FROM #__core_acl_'.$type.'_groups AS g1
				INNER JOIN #__core_acl_'.$type.'_groups AS g2 ON g1.lft BETWEEN g2.lft AND g2.rgt
				'.$where.'
				GROUP BY g1.name
				ORDER BY g1.lft';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}


}
