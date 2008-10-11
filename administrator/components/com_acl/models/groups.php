<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'models'.DS.'_prototypelist.php');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 */
class AccessModelGroups extends AccessModelPrototypeList
{
	/**
	 * Valid types
	 */
	function isValidType( $type )
	{
		$types	= array( 'aro', 'axo' );
		return in_array( strtolower( $type ), $types );
	}

	/**
	 * Gets a list of objects
	 *
	 * @param	boolean	True to resolve foreign keys
	 *
	 * @return	string
	 */
	function _getListQuery($resolveFKs = false)
	{
		if (empty( $this->_list_sql ))
		{
			$db			= &$this->getDBO();
			$query		= new JQuery;
			$type		= strtolower( $this->getState( 'list.group_type' ) );
			$tree		= $this->getState( 'list.tree');
			$parentId	= $this->getState( 'list.parent_id');
			$select		= $this->getState( 'list.select', 'a.*');
			$search		= $this->getState( 'list.search');
			$where		= $this->getState( 'list.where');
			$orderBy	= $this->getState( 'list.order');

			// Dynamically determine the table
			$table		= '#__core_acl_'.$type.'_groups';

			$query->select( $select );
			$query->from( $table.' AS a' );

			// Add the level in the tree
			if ($tree) {
				$query->select( 'COUNT(DISTINCT c2.id) AS level' );
				$query->join( 'LEFT OUTER', $table.' AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt' );
				$query->group( 'a.id' );
			}

			// Get a subtree below the parent
			if ($parentId > 0) {
				$query->join( 'LEFT', $table.' AS p ON p.id = '.(int) $parentId );
				$query->where( 'a.lft > p.lft AND a.rgt < p.rgt' );
			}

			// Resolve associated data
			if ($resolveFKs)
			{
				// Count the objects in the user group
				if ($type == 'aro') {
					$query->select( 'COUNT(DISTINCT map.aro_id) AS object_count' );
					$query->join( 'LEFT', '#__core_acl_groups_'.$type.'_map AS map ON map.group_id=a.id' );
					$query->group( 'a.id' );
				}
				// Count the items in the access level
				else if ($type == 'axo') {
					$query->select( 'COUNT(DISTINCT map.axo_id) AS object_count' );
					$query->join( 'LEFT', '#__core_acl_groups_'.$type.'_map AS map ON map.group_id=a.id' );
					$query->group( 'a.id' );
				}
			}

			// Search in the group name
			if ($search) {
				$serach = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
				$query->where( 'a.name LIKE '.$serach );
			}

			// An abritrary where clause
			if ($where) {
				$query->where( $where );
			}

			if ($orderBy) {
				$query->order( $this->_db->getEscaped( $orderBy ) );
			}

			//echo nl2br( $query->toString() );
			$this->_list_sql = (string) $query;
		}

		return $this->_list_sql;
	}
}