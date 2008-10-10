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

class JACL EXTENDS JObject {
	protected static $instance = null;
	const GROUP_SWITCH = '_group_';

	//Prevent instansicating object
	private function __construct() {
	}
	
	public static function check($args) {
		$result = self::query($args);
		return (bool) $result->allow;
	}

	public static function return_value($args) {
		$result = self::query($args);
		return $result->return_value;
	}

	public static function checkArray($args) {
		if(!isset($args['aro_array']) || !is_array($args['aro_array'])) {
			$this->setError('aro_array MUST be an array of ids to check');
			return false;
		} elseif(!isset($args['aco_section_value'])) {
			$this->setError('Must include aco_section_value in arguments');
			return false;
		} elseif(!isset($args['aco_value'])) {
			$this->setError('Must include aco_value in arguments');
			return false;
		}

		$return = array();
		foreach($args['aro_array'] AS $aro_section => $aro_value_array) {
			if(!is_array($aro_value_array)) continue;
			foreach($aro_value_array AS $aro_value) {
				$newArgs = array(
						'aco_section_value'=>$args['aco_section_value'],
						'aco_value'=>$args['aco_value'],
						'aro_section_value'=>$aro_section,
						'aro_value' => $aro_value,
						);
				if(self::check($newArgs)) {
					if(!isset($return[$aro_section])) $return[$aro_section] = array();
					$return[$aro_section][] = $aro_value;
				}
			}
		}
		return $return;
	}

	public static function query($args) {
		ksort($args);
		$id = 'acl_'.serialize($args);
		$user = JFactory::getUser();
		if($args['aro_value'] == $user->id) {
			$session = JFactory::getSession();
			$result = $session->get($id, '', 'acl');
			if($result) {
				$row = @unserialize($result);
				if(is_object($row)) {
					return $row;
				}
			}
		}

		if(!isset($args['aco_section_value']) || !isset($args['aco_value']) || !isset($args['aro_section_value']) || !isset($args['aro_value'])) {
			return false;
		}

		$db = JFactory::getDBO();
		//Preform Query!!!
		$sql_aro_group_ids = '';
		$sql_axo_group_ids = '';

		$aro_group_ids = self::getGroups($args, 'aro');
		if(!empty($aro_group_ids)) {
			$sql_aro_group_ids = implode(', ', $aro_group_ids);
		}
		$axo_group_ids = self::getGroups($args, 'axo');
		if(!empty($axo_group_ids)) {
			$sql_axo_group_ids = implode(', ', $axo_group_ids);
		}

		$order = array();
		$sql = 'SELECT a.id, a.allow, a.return_value
				FROM #__core_acl_acl AS a
				LEFT JOIN #__core_acl_aco_map AS ac ON ac.acl_id = a.id';
		if($args['aro_section_value'] != self::GROUP_SWITCH) {
			$sql .= '
				LEFT JOIN #__core_acl_aro_map AS ar ON ar.acl_id = a.id';
		}
		if(isset($args['axo_section_value']) && $args['axo_section_value'] != self::GROUP_SWITCH) {
			$sql .= '
				LEFT JOIN #__core_acl_axo_map AS ax ON ax.acl_id = a.id';
		}
		if(!empty($sql_aro_group_ids)) {
			$sql .= '
				LEFT JOIN #__core_acl_aro_groups_map AS arg ON arg.acl_id = a.id
				LEFT JOIN #__core_acl_aro_groups AS rg ON rg.id = arg.group_id';
		}
		$sql .= '
				LEFT JOIN #__core_acl_axo_groups_map AS axg ON axg.acl_id = a.id';
		if(!empty($sql_axo_group_ids)) {
			$sql .= '
				LEFT JOIN #__core_acl_axo_groups AS xg ON xg.id = axg.group_id';
		}
		$sql .= '
				WHERE a.enabled = 1
					AND (ac.section_value = '.$db->quote($args['aco_section_value']).' AND ac.value = '.$db->quote($args['aco_value']).')';
		if($args['aro_section_value'] == self::GROUP_SWITCH) {
			if(empty($sql_aro_group_ids)) {
				return false;
			}

			$sql .= '
					AND rg.id IN ('.$sql_aro_group_ids.')';
			$order[] = '(rg.rgt - rg.lft) ASC';
		} else {
			$sql .= '
					AND (
						(ar.section_value = '.$db->quote($args['aro_section_value']).' AND ar.value = '.$db->quote($args['aro_value']).')';
			if(!empty($sql_aro_group_ids)) {
				$sql .= ' 
							OR rg.id IN ('.$sql_aro_group_ids.')';
				$order[] = '(CASE WHEN ar.value IS NULL THEN 0 ELSE 1 END) DESC';
				$order[] = '(rg.rgt - rg.lft) ASC';
			}
			$sql .= '
						)';
		}

		if($args['axo_section_value'] == self::GROUP_SWITCH) {
			if(empty($sql_axo_group_ids)) {
				return false;
			}

			$sql .= '
					AND xg.id IN ('.$sql_axo_group_ids.')';
			$order[] = '(xg.rgt - xg.lft) ASC';
		} else {
			if(isset($args['axo_section_value']) && !empty($args['axo_section_value']) && isset($args['axo_value']) && !empty($args['axo_value'])) {
				$sql .= '
					AND (
						(ax.section_value = '.$db->quote($args['axo_section_value']).' AND ax.value = '.$db->quote($args['axo_value']).')';
			} else {
				$sql .= '
					AND (
						(ax.section_value IS NULL AND ax.value IS NULL)';
			}
			if(!empty($sql_axo_group_ids)) {
				$sql .= ' 
							OR xg.id IN ('.$sql_axo_group_ids.')';
				$order[] = '(CASE WHEN ax.value IS NULL THEN 0 ELSE 1 END) DESC';
				$order[] = '(xg.rgt - xg.lft) ASC';
			} else {
				$sql .= '
							AND axg.group_id IS NULL';
			}
			$sql .= '
						)';
		}
		$order[] = 'a.updated_date DESC';
		$sql .= '
				ORDER BY '.implode(',', $order).'
				';
		$db->setQuery($sql, 0, 1);
		$row = $db->loadObject();
		if(!is_object($row)) {
			$row = new stdclass();
			$row->id = null;
			$row->allow = false;
			$row->return_value = null;
		} else {
			$row->allow = (bool) $row->allow;
		}

		if($args['aro_value'] == $user->id) {
			$session->set($id, serialize($row), 'acl');
		}

		return $row;
	}

	public static function getGroups($args, $type = 'aro') {
		if(strtolower($type) != 'axo') {
			$type = 'aro';
		} else {
			$type = 'axo';
		}
		if(!isset($args[$type.'_section_value']) || !isset($args[$type.'_value'])) {
			return array();
		}

		$db = JFactory::getDBO();
		$sql = 'SELECT DISTINCT g2.id';
		if($args[$type.'_section_value'] == self::GROUP_SWITCH) {
			$sql .= '
				FROM #__core_acl_'.$type.'_groups AS g1
				JOIN #__core_acl_'.$type.'_groups AS g2';
			$where = '
				WHERE g1.value = '.$db->quote($args[$type.'_value']);
		} else {
			$sql .= '
				FROM #__core_acl_'.$type.' AS o
				JOIN #__core_acl_groups_'.$type.'_map AS gm ON gm.'.$type.'_id = o.id
				JOIN #__core_acl_'.$type.'_groups AS g1 ON g1.id = gm.group_id = g1.id
				JOIN #__core_acl_'.$type.'_groups AS g2';
			$where = '
				WHERE (o.section_value = '.$db->quote($args[$type.'_section_value']).' AND o.value = '.$db->quote($args[$type.'_value']).')';
		}
		if(isset($args['root_'.$type.'_group']) && !empty($args['root_'.$type.'_group'])) {
			$sql .= '
				JOIN #__core_acl_'.$type.' AS g3';
			$where .= '
				AND g3.value = '.$db->quote($args['root_'.$type.'_group']).'
				AND ((g2.lft BETWEEN g3.lft AND g1.lft) AND (g2.rgt BETWEEN g1.rgt AND g3.rgt))';
		} else {
			$where .= '
				AND (g2.lft <= g1.lft AND g2.rgt >= g1.rgt)';
		}

		$sql .= $where;
		$db->setQuery($sql);
		$rows = $db->loadResultList();
		if(!is_array($rows)) {
			$rows = array();
		}
		return $rows;
	}

}
