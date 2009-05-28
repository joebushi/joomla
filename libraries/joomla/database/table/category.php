<?php
/**
 * @version		$Id: category.php 11852 2009-05-28 01:17:29Z robs $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.database.tableasset');

/**
 * Category table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableCategory extends JTableAsset
{
	/** @var int Primary key */
	var $id					= null;
	/** @var int */
	var $parent_id			= null;

	var $left_id;

	var $right_id;

	var $path;

	/** @var string The menu title for the category (a short name)*/
	var $title				= null;
	/** @var string The full name for the category*/
	var $name				= null;
	/** @var string The the alias for the category*/
	var $alias				= null;
	/** @var string */
	var $image				= null;
	/** @var string */
	var $section				= null;
	/** @var int */
	var $image_position		= null;
	/** @var string */
	var $description			= null;
	/** @var boolean */
	var $published			= null;
	/** @var boolean */
	var $checked_out			= 0;
	/** @var time */
	var $checked_out_time		= 0;
	/** @var int */
	var $ordering			= null;
	/** @var int */
	var $access				= null;
	/** @var string */
	var $params				= null;

	var $created_user_id	= null;
	var $created_time	= null;
	var $modified_user_id	= null;
	var $modified_time	= null;
	var $hits = null;

	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);

		$this->access	= (int)JFactory::getConfig()->getValue('access');
	}

	/**
	 * Method to return the access section name for the asset table.
	 *
	 * @access	public
	 * @return	string
	 * @since	1.6
	 */
	function getAssetSection()
	{
		return 'com_content';
	}

	/**
	 * Method to return the name prefix to use for the asset table.
	 *
	 * @access	public
	 * @return	string
	 * @since	1.6
	 */
	function getAssetNamePrefix()
	{
		return 'category';
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @access	public
	 * @return	string
	 * @since	1.0
	 */
	function getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		// Check for a title.
		if (trim($this->title) == '') {
			$this->setError(JText::sprintf('must contain a title', JText::_('Category')));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = strtolower($this->title);
		}

		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		// Check for an alias.
		if (empty($this->alias)) {
			$this->setError(JText::_('Category_CHECK_ALIAS FAILED'));
			return false;
		}

		// TODO: Check for path collision

		return true;
	}

	/**
	 * Method to recursively rebuild the nested set tree.
	 *
	 * @access	public
	 * @param	integer	The root of the tree to rebuild.
	 * @param	integer	The left id to start with in building the tree.
	 * @return	boolean	True on success
	 * @since	1.0
	 */
	function rebuild($parent_id = 0, $left = 0)
	{
		// get the database object
		$db = &$this->_db;

		// get all children of this node
		$db->setQuery(
			'SELECT id FROM '. $this->_tbl .
			' WHERE parent_id = '. (int)$parent_id .
			' ORDER BY parent_id, ordering, title'
		);
		$children = $db->loadResultArray();

		// the right value of this node is the left value + 1
		$right = $left + 1;

		// execute this function recursively over all children
		for ($i=0,$n=count($children); $i < $n; $i++)
		{
			// $right is the current right value, which is incremented on recursion return
			$right = $this->rebuild($children[$i], $right);

			// if there is an update failure, return false to break out of the recursion
			if ($right === false) {
				return false;
			}
		}

		// we've got the left value, and now that we've processed
		// the children of this node we also know the right value
		$db->setQuery(
			'UPDATE '. $this->_tbl .
			' SET left_id = '. (int)$left .', right_id = '. (int)$right .
			' WHERE id = '. (int)$parent_id
		);
		// if there is an update failure, return false to break out of the recursion
		if (!$db->query()) {
			return false;
		}

		// return the right value of this node + 1
		return $right + 1;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * @access	public
	 * @param	boolean		If false, null object variables are not updated
	 * @return	boolean 	True successful, false otherwise and an internal error message is set`
	 */
	function store($updateNulls = false)
	{
		if ($result = parent::store($updateNulls))
		{
			// Get the ordering values for the group.
			$this->_db->setQuery(
				'SELECT `id`' .
				' FROM `'.$this->_tbl.'`' .
				' WHERE `parent_id` = '.(int)$this->parent_id .
				' ORDER BY `ordering`, `title`'
			);
			$ordering = $this->_db->loadResultArray();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// If the ordering has not changed, return true.
			$offset = array_search($this->id, $ordering);
			if ($offset === (int)$this->ordering) {
				return true;
			}

			// The category is set to be ordered first.
			if ($this->ordering == -1)
			{
				// Remove the current item from the ordering array.
				if ($offset !== false) {
					unset($ordering[$offset]);
					$ordering = array_values($ordering);
				}
				array_unshift($ordering, $this->id);
			}
			// The category is set to be ordered last.
			elseif ($this->ordering == -2)
			{
				// Remove the current item from the ordering array.
				if ($offset !== false) {
					unset($ordering[$offset]);
					$ordering = array_values($ordering);
				}
				array_push($ordering, $this->id);
			}
			// Use the ordering value given for the particular item.
			else {
				// Setup the ordering array.
				$ordering = array_merge(array_slice($ordering, 0, $offset), array_slice($ordering, $offset+1, 1), (array)$this->id, array_slice($ordering, $offset+2));
			}

			// Iterate through the categories and set th ordering.
			foreach ($ordering as $k => $v)
			{
				// Set the ordering for the category.
				$this->_db->setQuery(
					'UPDATE `'.$this->_tbl.'`' .
					' SET `ordering` = '.(int)$k .
					' WHERE `id` = '.(int)$v
				);
				$this->_db->query();

				// Check for a database error.
				if ($this->_db->getErrorNum()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}

			// Rebuild the nested set tree.
			$this->rebuild();
		}

		return $result;
	}

	function buildPath($nodeId=null)
	{
		// get the node id
		$nodeId = (empty($nodeId)) ? $this->id : $nodeId;

		// get the database object
		$db = &$this->_db;

		// get all children of this node
		$db->setQuery(
			'SELECT parent.alias FROM '.$this->_tbl.' AS node, '.$this->_tbl.' AS parent' .
			' WHERE node.left_id BETWEEN parent.left_id AND parent.right_id' .
			' AND node.id='. (int) $nodeId .
			' ORDER BY parent.left_id'
		);
		$segments = $db->loadResultArray();

		// make sure the root node doesn't appear in the path
		if ($segments[0] == 'root') {
			array_shift($segments);
		}

		// build the path
		$path = trim(implode('/', $segments), ' /\\');

		$db->setQuery(
			'UPDATE '. $this->_tbl .
			' SET path='. $db->Quote($path) .
			' WHERE id='. (int) $nodeId
		);
		// if there is an update failure, return false to break out of the recursion
		if (!$db->query()) {
			return false;
		}

		return true;
	}

	/**
	 * Delete this object and it's dependancies
	 */
	function delete($oid = null)
	{
		$k = $this->_tbl_key;

		if ($oid) {
			$this->load($oid);
		}
		if ($this->id == 0) {
			return new JException(JText::_('Category not found'));
		}
		if ($this->parent_id == 0) {
			return new JException(JText::_('Root categories cannot be deleted'));
		}
		if ($this->left_id == 0 or $this->right_id == 0) {
			return new JException(JText::_('Left-Right data inconsistency. Cannot delete category.'));
		}

		$db = &$this->getDBO();

		// Select the category ID and it's children
		$db->setQuery(
			'SELECT c.id' .
			' FROM `'.$this->_tbl.'` AS c' .
			' WHERE c.left_id >= '.(int) $this->left_id.' AND c.right_id <= '.$this->right_id
		);
		$ids = $db->loadResultArray();
		if (empty($ids)) {
			return new JException(JText::_('Left-Right data inconsistency. Cannot delete category.'));
		}
		$ids = implode(',', $ids);

		// Delete the category and it's children
		$db->setQuery(
			'DELETE FROM `'.$this->_tbl.'`' .
			' WHERE id IN ('.$ids.')'
		);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Generic Publish/Unpublish function
	 *
	 * @access	public
	 * @param	array	An array of id numbers
	 * @param	integer	0 if unpublishing, 1 if publishing
	 * @param	integer	The id of the user performnig the operation
	 */
	function publish($cid = null, $publish = 1, $userId = 0)
	{
		JArrayHelper::toInteger($cid);
		$userId		= (int) $userId;
		$publish	= (int) $publish;
		$k			= $this->_tbl_key;
		$db			= &$this->getDBO();
		$this->setError('');

		if (count($cid) < 1)
		{
			if ($this->$k) {
				$cid = array($this->$k);
			}
			else {
				$this->setError('No items selected.');
				return false;
			}
		}

		$temp2 = clone($this);

		// If unpublishing or trashing, we need to cascade
		foreach ($cid as $id)
		{
			$this->load($id);

			// ensure that subcats are not checked out
			$db->setQuery(
				'SELECT COUNT(c.id)' .
				' FROM `'.$this->_tbl.'` AS c' .
				' WHERE ((c.left_id > '.(int) $this->left_id.' AND c.right_id <= '.$this->right_id .') OR id = '.$id.')'.
				' AND (checked_out <> 0 AND checked_out <> '.(int) $userId.')'
			);
			if ($db->loadResult()) {
				$this->setError('Cannot unpublish or trash because parts of tree are checked out.');
				return false;
			}

			// ensure that the parent is ok
			if ($this->parent_id) {
				$temp2->load($this->parent_id);
				if ($temp2->parent_id > 0 && $temp2->published < $publish) {
					$this->setError('Cannot published or unpublish because part of the tree higher up are unpublished or trashed.');
					return false;
				}
			}

			if ($publish < 1)
			{
				// we are clear to execute
				$db->setQuery(
					'UPDATE `'.$this->_tbl.'` AS c' .
					' SET published = ' . $publish .
					' WHERE (c.left_id > '.(int) $this->left_id.' AND c.right_id < '.$this->right_id.') OR c.id = '.$id
				);
				if (!$db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			else {
				$db->setQuery(
					'UPDATE '. $this->_tbl .
					' SET published = ' . $publish .
					' WHERE id = '. $id
				);
				if (!$db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Adjust the item ordering.
	 *
	 * @access	public
	 * @param	integer	Primary key of the item to adjust.
	 * @param	integer	Increment, usually +1 or -1
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function ordering($move, $id = null)
	{
		// Sanitize arguments.
		$id = (int) (empty($id)) ? $this->id : $id;
		$move = (int) $move;

		// Get the parent id for the item.
		$this->_db->setQuery(
			'SELECT `parent_id`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `id` = '.(int)$id
		);
		$parentId = (int) $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Get the ordering values for the group.
		$this->_db->setQuery(
			'SELECT `id`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `parent_id` = '.(int)$parentId .
			' ORDER BY `ordering`, `title`'
		);
		$ordering = $this->_db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Only run the move logic if we are actually moving the item.
		if ($move != 0)
		{
			// Build a new array with the modified ordering values.
			$tmp = array();
			$idx = array_search($id, $ordering);
			foreach ($ordering as $k => $v)
			{
				if ($k == $idx) {
					continue;
				}
				else {
					if ($move > 0) {
						$tmp[] = $v;
					}
					if (($idx + $move) == $k) {
						$tmp[] = $ordering[$idx];
					}
					if ($move < 0) {
						$tmp[] = $v;
					}
				}
			}

			// Iterate through the categories and set th ordering.
			foreach ($tmp as $k => $v)
			{
				// Set the ordering for the category.
				$this->_db->setQuery(
					'UPDATE `'.$this->_tbl.'`' .
					' SET `ordering` = '.(int)$k .
					' WHERE `id` = '.(int)$v
				);
				$this->_db->query();

				// Check for a database error.
				if ($this->_db->getErrorNum()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}

			// Rebuild the nested set tree.
			$this->rebuild();
		}

		return true;
	}
}
