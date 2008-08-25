<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ).DS.'_prototype.php' );

/**
 * @package		Users
 * @subpackage	com_users
 */
class UserModelGroup extends UserModelPrototype
{
	/**
	 * Overridden method to lazy load data from the request/session as necessary
	 *
	 * @access	public
	 * @param	string	$key		The key of the state item to return
	 * @param	mixed	$default	The default value to return if it does not exist
	 * @return	mixed	The requested value by key
	 * @since	1.0
	 */
	function getState( $key=null, $default=null )
	{
		if (empty($this->__state_set))
		{
			$app = &JFactory::getApplication();

			$cid	= JRequest::getVar( 'cid', array(0), '', 'array' );
			$id		= JRequest::getInt( 'id', $cid[0] );
			$this->setState( 'id', $id );

			$search = $app->getUserStateFromRequest( 'users.group.search', 'search' );
			$this->setState( 'search', $search );

			//$published 	= $app->getUserStateFromRequest( 'users.group.published', 'published', 1 );
			//$this->setState( 'published', ($published == '*' ? null : $published) );

			// List state information
			$limit 		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg( 'list_limit' ) );
			$this->setState( 'limit', $limit );

			$limitstart = $app->getUserStateFromRequest( 'users.group.limitstart', 'limitstart', 0 );
			$this->setState( 'limitstart', $limitstart );

			$orderCol	= $app->getUserStateFromRequest( 'users.group.ordercol', 'filter_order', 'a.lft' );
			$orderDirn	= $app->getUserStateFromRequest( 'users.group.orderdirn', 'filter_order_Dir', 'asc' );
			if ($orderCol) {
				$this->setState( 'order by',	$orderCol.' '.($orderDirn == 'asc' ? 'asc' : 'desc') );
			}
			$this->setState( 'orderCol',	$orderCol );
			$this->setState( 'orderDirn',	$orderDirn );

			$this->__state_set = true;
		}
		return parent::getState($key, $default);
	}

	/**
	 * Proxy for getTable
	 */
	function &getTable()
	{
		JTable::getInstance( 'Group', 'AclTable' );
		$table = new AclTableGroup( $this->getDBO(), 'aro' );
		return $table;
	}

	/**
	 * Gets a list of categories objects
	 *
	 * Filters may be fields|published|order by|searchName|where
	 * @param array Named array of field-value filters
	 * @param boolean True if foreign keys are to be resolved
	 */
	function _getListQuery( $filters, $resolveFKs=false )
	{
		$type		= $filters->get( 'type' );
		$parentId	= $filters->get( 'parent_id' );
		$select		= $filters->get( 'select' );
		$search		= $filters->get( 'search' );
		$where		= $filters->get( 'where' );
		$orderBy	= $filters->get( 'order by' );

		$tree		= $filters->get( 'show.tree' );

		$db		= &$this->getDBO();
		$query	= new JQuery;
		$table	= '#__core_acl_'.$type.'_groups';

		$query->select( $select !== null ? $select : 'a.*'  );
		$query->from( $table.' AS a' );

		if ($tree) {
			$query->select( 'COUNT(DISTINCT c2.id) AS level' );
			$query->join( 'LEFT OUTER', $table.' AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt' );
			$query->group( 'a.id' );
		}

		if ($parentId > 0) {
			$query->join( 'LEFT', $table.' AS p ON p.id = '.(int) $parentId );
			$query->where( 'a.lft > p.lft AND a.rgt < p.rgt' );
		}

		if ($resolveFKs)
		{
			if ($type == 'aro') {
				$query->select( 'COUNT(DISTINCT map.aro_id) AS object_count' );
				$query->join( 'LEFT', '#__core_acl_groups_'.$type.'_map AS map ON map.group_id=a.id' );
				$query->group( 'a.id' );
			}
			else if ($type == 'axo') {
				$query->select( 'COUNT(DISTINCT map.axo_id) AS object_count' );
				$query->join( 'LEFT', '#__core_acl_groups_'.$type.'_map AS map ON map.group_id=a.id' );
				$query->group( 'a.id' );
			}
		}

		if ($search) {
			$serach = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query->where( 'a.name LIKE '.$serach );
		}

		if ($where) {
			$query->where( $where );
		}

		if ($orderBy) {
			$query->order( $this->_db->getEscaped( $orderBy ) );
		}

		//echo nl2br( str_replace( '#__', $db->getPrefix(), (string) $query ) );
		return $query;
	}

	/**
	 * Save override
	 */
	function save( $input )
	{
		$result	= true;
		$user	= &JFactory::getUser();
		$table	= &$this->getTable();

		if (!$table->save( $input )) {
			$result	= JError::raiseWarning( 500, $table->getError() );
		}
		else {
			$table->rebuild();
		}
		// Set the new id (if new)
		$this->setState( 'id', $table->id );

		return $result;
	}

	/**
	 * Utility method to gets the level of a group
	 */
	function getLevel( $id = null, $type = 'aro' )
	{
		$model = new UserModelGroup( array( 'ignore_request' => 1 ));
		$model->setState( 'select', null );
		$model->setState( 'type', $type );
		$model->setState( 'show.tree', 1 );
		$model->setState( 'where', 'a.id = '.(int) $id );
		$result = $model->getItems ( 0 );
		return isset( $result[0] ) ? $result[0]->level : false;
	}
}