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

require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'acl'.DS.'group.php');
require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'acl'.DS.'object.php');
require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'acl'.DS.'objectsection.php');

class JACLRule EXTENDS JObject {
	public $id = 0;
	public $aco = array();
	public $aro = array();
	public $axo = array();
	public $aro_groups = array();
	public $axo_groups = array();
	public $allow = 0;
	public $enabled = 0;
	public $return_value = '';
	public $note = '';

	protected $db = null;

	public function __construct($id = 0) {
		$this->db = JFactory::getDBO();
		$this->id = $id;
		return $this->load();
	}

	public function load() {
		if($this->id == 0) return;
		$sql = 'SELECT a.id, a.allow, a.enabled, a.return_value, a.note
				FROM #__core_acl_acl 
				WHERE id = '.(int) $this->id;
		$this->db->setQuery($sql);
		$row = $this->db->loadObject();
		if(!is_object($row)) {
			return false;
		}
		$types = array('aco','aro','axo');
		foreach($types as $type) {
			$row->$type = array();
			$sql = 'SELECT DISTINCT a.section_value, a.value, c.name AS section_name, b.name AS aco_name
					FROM #__core_acl_'.$type.'_map AS a 
					JOIN #__core_acl_'.$type.' AS b ON (a.section_value = b.section_value AND a.value = b.value)
					JOIN #__core_acl_'.$type.'_sections AS c ON b.section_value = c.value
					WHERE a.acl_id = '.(int) $this->id;
			$this->db->setQuery($sql);
			$values = $this->db->loadAssocList();
			if(!empty($values)) {
				foreach($values AS $value) {
					if(!isset($row->$type[$value['section_value']])) {
						$row->$type[$value['section_value']] = array();
					}
					$row->$type[$value['section_value']][] = $value;
				}
			}
		}

		$types = array('aro_groups', 'axo_groups');
		foreach($types AS $type) {
			$row->$type = array();
			$sql = 'SELECT DISTINCT group_id FROM #__core_acl_'.$type.'_map WHERE acl_id = '.(int) $this->id;
			$this->db->setQuery($sql);
			$values = $this->db->loadResultList();
			if(!empty($values)) {
				$row->$type = $values;
			}
		}

		return $this->bind($row);
	}

	public function check() {
		if(empty($this->aco)) {
			$this->setError('ACO List is empty');
			return false;
		} elseif(empty($this->aro)) {
			$this->setError('ARO List is empty');
			return false;
		}
		if(empty($this->allow)) {
			$this->allow = 1;
		} 
		if(empty($this->enabled)) {
			$this->enabled = 1;
		}
		$this->allow = (int) $this->allow;
		$this->enabled = (int) $this->enabled;
		if(!empty($this->section_value)) {
			$rows = JACLObjectSection::get(array('value'=>$this->section_value), 'acl');
			if(empty($rows)) {
				$this->setError('Section Value does not exist in the database');
				return false;
			}
		}
		$this->aro_groups = array_unique($this->aro_groups);
		$this->axo_groups = array_unique($this->axo_groups);
		return (bool) $this->isConflict();
	}

	public function save() {
		if(!$this->check()) {
			return false;
		}
		$this->consolidate();
		if($this->id == 0) {
			//Add New!
			if(empty($this->section_value)) {
				$this->section_value = 'system';
				$rows = JACLObjectSection::get(array('value'=>$this->section_value), 'acl');
				if(empty($rows)) {
					$sql = 'SELECT value FROM #__core_acl_acl_sections AS a
							WHERE order_value = (SELECT MIN(order_value) FROM #__core_acl_acl_sections)';
					$this->db->setQuery($sql, 0, 1);
					$value = $this->db->loadResult();
					if(empty($value)) {
						$this->setError('No Valid ACL Section Found');
						return false;
					} else {
						$this->section_value = $value;
					}
				}
			}
			$sql = 'INSERT INTO #__core_acl_acl 
						(section_value, allow, enabled, return_value, note, updated_date)
						VALUES
						('.$this->db->quote($this->section_value).','.$this->allow.','.$this->enabled.','.$this->db->quote($this->return_value).','.$this->db->quote($this->note).',NOW())';
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			$this->id = $this->db->insertid();
			return true;
		} else {
			//Update
			$old = new JACLRule($this->id);
			$sets = array();
			if($old->section_value != $this->section_value) {
				$sets[] = 'section_value = '.$this->db->quote($this->section_value);
			}
			if($old->allow != $this->allow) {
				$sets[] = 'allow = '.$this->allow;
			}
			if($old->enabled != $this->enabled) {
				$sets[] = 'enabled = '.$this->enabled;
			}
			if($old->return_value != $this->return_value) {
				$sets[] = 'return_value = '.$this->db->quote($this->return_value);
			}
			if($old->note != $this->note) {
				$sets[] = 'note = '.$this->db->quote($note);
			}
			$sets[] = 'updated_date = NOW()';
			$sql = 'UPDATE #__core_acl_acl 
					SET '.implode(', ', $sets).'
					WHERE id = '.$this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
			$mappings = array('aco_map','aro_map','axo_map','aro_groups_map','axo_groups_map');
			foreach($mappings AS $map) {
				$sql = 'DELETE FROM #__core_acl_'.$map.' WHERE acl_id = '.$this->id;
				$this->db->setQuery($sql);
				if(!$this->db->query()) {
					$this->setError($this->db->getErrorMsg());
					return false;
				}
			}
		}

		$types = array('aco','aro','axo');
		foreach($types AS $type) {
			if(empty($this->$type)) {
				continue;
			}
			foreach($this->$type AS $section_value => $value_array) {
				if(!is_array($value_array) || empty($value_array)) {
					continue;
				}
				$value_array = array_unique($value_array);
				foreach($value_array AS $value) {
					$obj = JACLObject::get(array('section_value'=>$section_value, 'value'=>$value, 'return_hidden' => 1), $type);
					if(empty($obj)) {
						continue;
					}
					$sql = 'INSERT INTO #__core_acl_'.$type.'_map (acl_id, section_value, value) VALUES ('.$this->id.', '.$this->db->quote($section_value).', '.$this->db->quote($value).')';
					$this->db->setQuery($sql);
					if(!$this->db->query()) {
						$this->setError($this->db->getErrorMsg());
						return false;
					}
				}
			}
		}

		$types = array('aro'=>'aro_groups', 'axo'=>'axo_groups');
		foreach($types AS $map => $type) {
			if(empty($this->$type)) {
				continue;
			}
			foreach($this->$type AS $group_id) {
				$group = new JACLGroup($group_id);
				if(!$group->valid) {
					continue;
				}
				$sql = 'INSERT INTO #__core_acl_'.$type.'_map (acl_id, group_id) VALUES ('.$this->id.', '.(int) $group_id.')';
				$this->db->setQuery($sql);
				if(!$this->db->query()) {
					$this->setError($this->db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}

	public function consolidate() {
		//TODO: Write This!
	}

	public function isConflict() {
		if(empty($this->aco)) {
			$this->setError('Empty ACO value list');
			return 2;
		} elseif(empty($this->aro)) {
			$this->setError('Empty ARO value list');
			return 2;
		}
		$sql = 'SELECT a.id 
			FROM #__core_acl_acl AS a
			LEFT JOIN #__core_acl_aco_map AS ac ON a.id = ac.acl_id
			LEFT JOIN #__core_acl_aro_map AS ar ON a.id = ar.acl_id
			LEFT JOIN #__core_acl_axo_map AS ax ON a.id = ax.acl_id
			LEFT JOIN #__core_acl_axo_groups_map AS axg.acl_id = a.id
			LEFT JOIN #__core_acl_axo_groups AS xg ON axg.group_id = xg.id
			';

		foreach($this->aco AS $aco_section_value => $aco_value) {
			if(empty($aco_value)) continue;
			$where = array(
				'ac2'=>'(ac.section_value = '.$this->db->quote($aco_section_value).' AND ac.value IN ('.$this->implodeArray($aco_value).')',
				);
			foreach($this->aro AS $aro_section_value => $aro_value) {
				if(empty($aro_value)) continue;
				$where['ar2'] = '(ar.section_value = '.$this->db->quote($aro_section_value).' AND ar.value IN ('.$this->implodeArray($aro_value).')';
				if(!empty($this->axo)) {
					foreach($this->axo AS $axo_section_value => $axo_value) {
						if(empty($axo_value)) continue;
						$where['ax1'] = 'ax.acl_id = a.id';
						$where['ax2'] = '(ax.section_value = '.$this->db->quote($axo_section_value).' AND ax.value IN ('.$this->implodeArray($axo_value).')';
						$this->db->setQuery($sql . 'WHERE '.implode(' AND ', $where));
						$conflict = $this->db->loadResultList();
						if(is_array($conflict) && !empty($conflict)) {
							$conflict = array_diff($conflict, array($this->id));
							if(!empty($conflict)) {
								return 1;
							}
						}
					}
				} else {
					$where['ax1'] = '(ax.section_value IS NULL AND ax.value IS NULL)';
					$where['ax2'] = 'xg.name IS NULL';
					$this->db->setQuery($sql . 'WHERE '.implode(' AND ', $where));
					$conflict = $this->db->loadResultList();
					if(is_array($conflict) && !empty($conflict)) {
						$conflict = array_diff($conflict, array($this->id));
						if(!empty($conflict)) {
							return 1;
						}
					}
				}
			}
		}
		return 0;
	}

	public function delete() {
		if($this->id == 0) {
			$this->setError('cannot delete non-existant rule');
			return false;
		}
		$mappings = array('aco_map','aro_map','axo_map','aro_groups_map','axo_groups_map');
		foreach($mappings AS $map) {
			$sql = 'DELETE FROM #__core_acl_'.$map.' WHERE acl_id = '.$this->id;
			$this->db->setQuery($sql);
			if(!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}
		}
		
		$sql = 'DELETE FROM #__core_acl_acl WHERE id = '.(int)$this->id;
		$this->db->setQuery($sql);
		if(!$this->db->query()) {
			$this->setError($this->db->getErrorMsg());
			return false;
		}
		return true;
	}

	public function append($obj) {
		if($obj INSTANCEOF JACLObject) {
			if($obj->id == 0) {
				return false;
			}
			$type = $obj->type;
			$section_value = $obj->section_value;
			$value = $obj->value;
			if(!isset($this->$type[$section_value])) {
				$this->$type[$section_value] = array();
			}
			$this->$type[$section_value][] = $value;
			return true;
		} elseif($obj INSTANCEOF JACLGroup) {
			if($obj->id == 0) {
				return false;
			}
			$type = $obj->type.'_groups';
			$id = $obj->id;
			$this->$type[] = $id;
			return true;
		} else {
			return false;
		}
	}

	public function remove($obj) {
		if($obj INSTANCEOF JACLObject) {
			if($obj->id == 0) {
				return false;
			}
			$type = $obj->type;
			$section_value = $obj->section_value;
			$value = $obj->value;
			if(!isset($this->$type[$section_value])) {
				return true;
			}
			$key = array_search($value, $this->$type[$section_value]);
			if($key) {
				unset($this->$type[$section_value][$key]);
			}
			return true;
		} elseif($obj INSTANCEOF JACLGroup) {
			if($obj->id == 0) {
				return false;
			}
			$type = $obj->type.'_groups';
			$id = $obj->id;
			$key = array_search($id, $this->$type);
			if($key) {
				unset($this->$type[$key]);
			}
			return true;
		} else {
			return false;
		}
	}

	public static function search($args) {
		$db = JFactory::getDBO();
		$sql = 'SELECT a.id 
				FROM #__core_acl_acl AS a';
		$where = array();

		$types = array('ac'=>'aco','ar'=>'aro','ax'=>'axo');
		foreach($types AS $alais => $type) {
			if((!isset($args[$type.'_section_value']) || $args[$type.'_section_value'] !== false) && (!isset($args[$type.'_value']) || $args[$type.'_value'] !== false)) {
				$sql .= '
					LEFT JOIN #__core_acl_'.$type.'_map AS '.$alais.' ON a.id = '.$alais.'.acl_id';
				if((!isset($args[$type.'_section_value']) || is_null($args[$type.'_section_value'])) && (!isset($args[$type.'_value']) || is_null($args[$type.'_value']))) {
					$where[] = '('.$alais.'.section_value IS NULL AND '.$alais.'.value IS NULL)';
				} else {
					$where[] = '('.$alais.'.section_value = '.$db->quote($args[$type.'_section_value']).' AND '.$alais.'.value = '.$db->quote($args[$type.'_value']).')';
				}
			}
		}

		$types = array('rg'=>'aro', 'xg'=>'axo');
		foreach($types AS $alais => $type) {
			if(!isset($args[$type.'_group_name']) || $args[$type.'_group_name'] !== false) {
				$sql .= '
					LEFT JOIN #__core_acl_'.$type.'_groups_map AS '.$alais.'g ON a.id = '.$alais.'g.acl_id
					LEFT JOIN #__core_acl_'.$type.'_groups AS '.$alais.' ON '.$alais.'g.group_id = '.$alais.'.id';
				if(!isset($args[$type.'_group_name']) || is_null($args[$type.'_group_name'])) {
					$where[] = '('.$alais.'.name IS NULL)';
				} else {
					$where[] = '('.$alais.'.name = '.$db->quote($args[$type.'_group_name']).')';
				}
			}
		}
		if(!isset($args['return_value']) || $args['return_value'] !== false) {
			if(!isset($args['return_value']) || is_null($args['return_value'])) {
				$where[] = '(a.return_value IS NULL)';
			} else {
				$where[] = '(a.return_value = '.$db->quote($args['return_value']).')';
			}
		}
		if(!empty($where)) {
			$sql .= implode(' AND ', $where);
		}
		$db->setQuery($sql);
		$rows = $db->loadResultList();
		if(!is_array($rows)) {
			$rows = array();
		}
		return $rows;
	}

}	
