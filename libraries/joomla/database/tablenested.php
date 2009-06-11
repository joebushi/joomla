<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	Database
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.database.table');

/**
 * Table class supporting modified pre-order tree traversal behavior.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.6
 */
class JTableNested extends JTable
{
	/**
	 * Object property holding the primary key of the parent node.  Provides
	 * adjacency list data for nodes.
	 *
	 * @var integer
	 */
	public $parent_id = null;

	/**
	 * Object property holding the depth level of the node in the tree.
	 *
	 * @var integer
	 */
	public $level = null;

	/**
	 * Object property holding the left value of the node for managing its
	 * placement in the nested sets tree.
	 *
	 * @var integer
	 */
	public $left_id = null;

	/**
	 * Object property holding the right value of the node for managing its
	 * placement in the nested sets tree.
	 *
	 * @var integer
	 */
	public $right_id = null;

	/**
	 * Object property to hold the location type to use when storing the row.
	 * Possible values are: ['before', 'after', 'first-child', 'last-child'].
	 *
	 * @var string
	 */
	protected $_location = null;

	/**
	 * Object property to hold the primary key of the location reference node to
	 * use when storing the row.  A combination of location type and reference
	 * node describes where to store the current node in the tree.
	 *
	 * @var integer
	 */
	protected $_location_id = null;

	/**
	 * Method to get nodes from a given node to its root.
	 *
	 * @param	integer	Primary key of the node for which to get the path.
	 * @param	boolean	Only select diagnostic data for the nested sets.
	 * @return	mixed	Boolean false on failure or array of node objects on success.
	 * @since	1.6
	 */
	public function getPath($pk = null, $diagnostic = false)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the path from the node to the root.
		$select = ($diagnostic) ? 'SELECT p.'.$k.', p.parent_id, p.level, p.left_id, p.right_id' : 'SELECT p.*';
		$this->_db->setQuery(
			$select .
			' FROM `'.$this->_tbl.'` AS n, `'.$this->_tbl.'` AS p' .
			' WHERE n.left_id BETWEEN p.left_id AND p.right_id' .
			' AND n.'.$k.' = '.(int) $pk .
			' ORDER BY p.left_id'
		);
		$path = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $path;
	}

	/**
	 * Method to get a node and all child nodes.
	 *
	 * @param	integer	Primary key of the node for which to get the tree.
	 * @param	boolean	Only select diagnostic data for the nested sets.
	 * @return	mixed	Boolean false on failure or array of node objects on success.
	 * @since	1.6
	 */
	public function getTree($pk = null, $diagnostic = false)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the path from the node to the root.
		$select = ($diagnostic) ? 'SELECT n.'.$k.', n.parent_id, n.level, n.left_id, n.right_id' : 'SELECT n.*';
		$this->_db->setQuery(
			$select .
			' FROM `'.$this->_tbl.'` AS n, `'.$this->_tbl.'` AS p' .
			' WHERE n.left_id BETWEEN p.left_id AND p.right_id' .
			' AND p.'.$k.' = '.(int) $pk .
			' ORDER BY n.left_id'
		);
		$tree = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $tree;
	}

	/**
	 * Method to determine if a row is a leaf node in the tree (has no children).
	 *
	 * @param	integer	Primary key of the node to check.
	 * @return	boolean	True if a leaf node.
	 * @since	1.6
	 */
	public function isLeaf($pk = null)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the node by primary key.
		if (!$node = $this->_getNode($pk)) {
			// Error message set in getNode method.
			return false;
		}

		// The node is a leaf node.
		return (($node->right_id - $node->left_id) == 1);
	}

	/**
	 * Method to set the location of a node in the object.  This method does not
	 * save the new location to the database, but will set it in the object so
	 * that when the node is stored it will be stored in the new location.
	 *
	 * @param	integer	The primary key of the node to reference new location by.
	 * @param	string	Location type string. ['before', 'after', 'first-child', 'last-child']
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function setLocation($referenceId, $position = 'after')
	{
		// Make sure the location is valid.
		if (($position != 'before') && ($position != 'after') &&
			($position != 'first-child') && ($position != 'last-child')) {
			return false;
		}

		// Set the location properties.
		$this->_location = $position;
		$this->_location_id = $referenceId;

		return true;
	}

	/**
	 * Method to move a row and its children to a new location in the tree.
	 *
	 * @param	integer	The primary key of the node to reference new location by.
	 * @param	string	Location type string. ['before', 'after', 'first-child', 'last-child']
	 * @param	integer	The primary key of the node to move.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function move($referenceId, $position = 'after', $pk = null)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the node by id.
		if (!$node = $this->_getNode($pk)) {
			// Error message set in getNode method.
			return false;
		}

		// Get the ids of child nodes.
		$this->_db->setQuery(
			'SELECT `'.$k.'`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
		);
		$children = $this->_db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Cannot move the node to be a child of itself.
		if (in_array($referenceId, $children)) {
			$this->setError(JText::_('Invalid_Node_Recursion'));
			return false;
		}

		// Lock the table for writing.
		if (!$this->_lock()) {
			// Error message set in lock method.
			return false;
		}

		// Temporarily set the current tree to move to have negative right and left values during processing.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `left_id` = `left_id` * (-1), `right_id` = `right_id` * (-1)' .
			' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Compress the left values.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `left_id` = `left_id` - '.(int) $node->width .
			' WHERE `left_id` > '.(int) $node->right_id
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Compress the right values.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `right_id` = `right_id` - '.(int) $node->width .
			' WHERE `right_id` > '.(int) $node->right_id
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// We are moving the tree relative to a reference node.
		if ($referenceId)
		{
			// Get the reference node by primary key.
			if (!$reference = $this->_getNode($referenceId)) {
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}

			// Get the reposition data for shifting the tree and re-inserting the node.
			if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, $position)) {
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}

			// Create space in the tree at the new location for the moved subtree in right ids.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `right_id` = `right_id` + '.(int) $node->width .
				' WHERE '.$repositionData->right_where
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Create space in the tree at the new location for the moved subtree in left ids.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `left_id` = `left_id` + '.(int) $node->width .
				' WHERE '.$repositionData->left_where
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Reload the parent data.
			unset($reference);
			if (!$reference = $this->_getNode($referenceId)) {
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}
		}

		// We are moving the tree to be a new root node.
		else
		{
			// Get the last root node as the reference node.
			$this->_db->setQuery(
				'SELECT `'.$this->_tbl_key.'`, `parent_id`, `level`, `left_id`, `right_id`' .
				' FROM `'.$this->_tbl.'`' .
				' WHERE `parent_id` = 0' .
				' ORDER BY `left_id` DESC',
				0, 1
			);
			$reference = $this->_db->loadObject();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Get the reposition data for re-inserting the node after the found root.
			if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, 'after')) {
				// Error message set in getNode method.
				$this->_unlock();
				return false;
			}
		}

		/*
		 * Calculate the offset between where the node used to be in the tree and
		 * where it needs to be in the tree for left ids (also works for right ids).
		 */
		$offset = $repositionData->new_left_id + $node->left_id;
		$levelOffset = $repositionData->new_level - $node->level;

		// Move the nodes back into position in the tree using the calculated offsets.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `right_id` = '.(int) $offset.' - `right_id`,' .
			'	  `left_id` = '.(int) $offset.' - `left_id`,' .
			'	  `level` = `level` + '.(int) $levelOffset .
			' WHERE `left_id` < 0'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Set the correct parent id for the moved node if required.
		if ($node->parent_id != $repositionData->new_parent_id)
		{
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `parent_id` = '.(int) $repositionData->new_parent_id .
				' WHERE `'.$this->_tbl_key.'` = '.(int) $node->$k
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}
		}

		// Unlock the table for writing.
		$this->_unlock();

		// Set the object values.
		$this->parent_id = $repositionData->new_parent_id;
		$this->level = $repositionData->new_level;
		$this->left_id = $repositionData->new_left_id;
		$this->right_id = $repositionData->new_right_id;

		return true;
	}

	/**
	 * Method to delete a row [and optionally its child nodes] from the table.
	 *
	 * @param	integer	The primary key of the node to delete.
	 * @param	boolean	True to delete child nodes, false to move them up a level.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function delete($pk = null, $children = true)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Lock the table for writing.
		if (!$this->_lock()) {
			// Error message set in lock method.
			return false;
		}

		// Get the node by id.
		if (!$node = $this->_getNode($pk)) {
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Should we delete all children along with the node?
		if ($children)
		{
			// Delete the node and all of its children.
			$this->_db->setQuery(
				'DELETE FROM `'.$this->_tbl.'`' .
				' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Compress the left values.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `left_id` = `left_id` - '.(int) $node->width .
				' WHERE `left_id` > '.(int) $node->right_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Compress the right values.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `right_id` = `right_id` - '.(int) $node->width .
				' WHERE `right_id` > '.(int) $node->right_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}
		}

		// Leave the children and move them up a level.
		else
		{
			// Delete the node.
			$this->_db->setQuery(
				'DELETE FROM `'.$this->_tbl.'`' .
				' WHERE `left_id` = '.(int) $node->left_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Shift all node's children up a level.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `left_id` = `left_id` - 1,' .
				'	  `right_id` = `right_id` - 1,' .
				'	  `level` = `level` - 1' .
				' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Adjust all the parent values for direct children of the deleted node.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `parent_id` = '.(int) $node->parent_id .
				' WHERE `parent_id` = '.(int) $node->$k
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Shift all of the left values that are right of the node.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `left_id` = `left_id` - 2' .
				' WHERE `left_id` > '.(int) $node->right_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}

			// Shift all of the right values that are right of the node.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `right_id` = `right_id` - 2' .
				' WHERE `right_id` > '.(int) $node->right_id
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				$this->_unlock();
				return false;
			}
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to store a row in the database table.
	 *
	 * @param	boolean	True to update null values as well.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		// Initialize variables.
		$k = $this->_tbl_key;

		/*
		 * If the primary key is empty, then we assume we are inserting a new node into the
		 * tree.  From this point we would need to determine where in the tree to insert it.
		 */
		if (empty($this->$k))
		{
			/*
			 * We are inserting a node somewhere in the tree with a known reference
			 * node.  We have to make room for the new node and set the left and right
			 * values before we insert the row.
			 */
			if ($this->_location_id >= 0)
			{
				// Lock the table for writing.
				if (!$this->_lock()) {
					// Error message set in lock method.
					return false;
				}

				// We are inserting a node relative to the last root node.
				if ($this->_location == 0)
				{
					// Get the last root node as the reference node.
					$this->_db->setQuery(
						'SELECT `'.$this->_tbl_key.'`, `parent_id`, `level`, `left_id`, `right_id`' .
						' FROM `'.$this->_tbl.'`' .
						' WHERE `parent_id` = 0' .
						' ORDER BY `left_id` DESC',
						0, 1
					);
					$reference = $this->_db->loadObject();

					// Check for a database error.
					if ($this->_db->getErrorNum()) {
						$this->setError($this->_db->getErrorMsg());
						$this->_unlock();
						return false;
					}
				}

				// We have a real node set as a location reference.
				else
				{
					// Get the reference node by primary key.
					if (!$reference = $this->_getNode($this->_location_id)) {
						// Error message set in getNode method.
						$this->_unlock();
						return false;
					}
				}

				// Get the reposition data for shifting the tree and re-inserting the node.
				if (!$repositionData = $this->_getTreeRepositionData($reference, 2, $this->_location)) {
					// Error message set in getNode method.
					$this->_unlock();
					return false;
				}

				// Create space in the tree at the new location for the new node in right ids.
				$this->_db->setQuery(
					'UPDATE `'.$this->_tbl.'`' .
					' SET `right_id` = `right_id` + 2' .
					' WHERE '.$repositionData->right_where
				);
				$this->_db->query();

				// Check for a database error.
				if ($this->_db->getErrorNum()) {
					$this->setError($this->_db->getErrorMsg());
					$this->_unlock();
					return false;
				}

				// Create space in the tree at the new location for the new node in left ids.
				$this->_db->setQuery(
					'UPDATE `'.$this->_tbl.'`' .
					' SET `left_id` = `left_id` + 2' .
					' WHERE '.$repositionData->left_where
				);
				$this->_db->query();

				// Check for a database error.
				if ($this->_db->getErrorNum()) {
					$this->setError($this->_db->getErrorMsg());
					$this->_unlock();
					return false;
				}

				// Set the object values.
				$this->parent_id	= $repositionData->new_parent_id;
				$this->level		= $repositionData->new_level;
				$this->left_id		= $repositionData->new_left_id;
				$this->right_id		= $repositionData->new_right_id;
			}
			else
			{
				// Negative parent ids are invalid
				$this->setError(JText::_('Invalid_Parent'));
				return false;
			}
		}

		/*
		 * If we have a given primary key then we assume we are simply updating this
		 * node in the tree.  We should assess whether or not we are moving the node
		 * or just updating its data fields.
		 */
		else
		{
			// If the location has been set, move the node to its new location.
			if ($this->_location_id > 0)
			{
				if (!$this->move($this->_location_id, $this->_location, $this->$k)) {
					// Error message set in move method.
					return false;
				}
			}

			// Lock the table for writing.
			if (!$this->_lock()) {
				// Error message set in lock method.
				return false;
			}
		}

		// Store the row to the database.
		if (!parent::store()) {
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.  The method will now
	 * allow you to set a publishing state higher than any ancestor node and will
	 * not allow you to set a publishing state on a node with a checked out child.
	 *
	 * @param	mixed	An optional array of primary key values to update.  If not
	 * 					set the instance property value is used.
	 * @param	integer The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param	integer The user id of the user performing the operation.
	 * @return	boolean	True on success.
	 * @since	1.0.4
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialize variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('No_Rows_Selected'));
				return false;
			}
		}

		// Determine if there is checkout support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
			$checkoutSupport = false;
		}
		else {
			$checkoutSupport = true;
		}

		// Iterate over the primary keys to execute the publish action if possible.
		foreach ($pks as $pk)
		{
			// Get the node by primary key.
			if (!$node = $this->_getNode($pk)) {
				// Error message set in getNode method.
				return false;
			}

			// If the table has checkout support, verify no children are checked out.
			if ($checkoutSupport)
			{
				// Ensure that children are not checked out.
				$this->_db->setQuery(
					'SELECT COUNT('.$this->_tbl_key.')' .
					' FROM `'.$this->_tbl.'`' .
					' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id .
					' AND (checked_out <> 0 AND checked_out <> '.(int) $userId.')'
				);

				// Check for checked out children.
				if ($this->_db->loadResult()) {
					$this->setError('Child_Rows_Checked_Out');
					return false;
				}
			}

			// If any parent nodes have lower published state values, we cannot continue.
			if ($node->parent_id)
			{
				// Get any anscestor nodes that have a lower publishing state.
				$this->_db->setQuery(
					'SELECT p.'.$k .
					' FROM `'.$this->_tbl.'` AS n, `'.$this->_tbl.'` AS p' .
					' WHERE n.left_id BETWEEN p.left_id AND p.right_id' .
					' AND n.'.$k.' = '.(int) $pk .
					' AND p.parent_id > 0' .
					' AND p.published < '.(int) $state .
					' ORDER BY p.left_id DESC',
					1, 0
				);
				$rows = $this->_db->loadResultArray();

				// Check for a database error.
				if ($this->_db->getErrorNum()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				if (!empty($rows)) {
					$this->setError('Ancestor_Nodes_Lower_Published_State');
					return false;
				}
			}

			// Update the publishing state.
			$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `published` = '.(int) $state .
				' WHERE `'.$this->_tbl_key.'` = '.(int) $pk
			);
			$this->_db->query();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// If checkout support exists for the object, check the row in.
			if ($checkoutSupport) {
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->published = $state;
		}

		$this->_errors = array();
		return true;
	}

	/**
	 * Method to move a node one position to the left in the same level.
	 *
	 * @param	integer	Primary key of the node to move.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function orderUp($pk)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Lock the table for writing.
		if (!$this->_lock()) {
			// Error message set in lock method.
			return false;
		}

		// Get the node by primary key.
		if (!$node = $this->_getNode($pk)) {
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the left sibling node.
		if (!$sibling = $this->_getNode($node->left_id - 1, 'right')) {
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the primary keys of child nodes.
		$this->_db->setQuery(
			'SELECT `'.$this->_tbl_key.'`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
		);
		$children = $this->_db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the node and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `left_id` = `left_id` - '.(int) $sibling->width.', `right_id` = `right_id` - '.(int) $sibling->width.'' .
			' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the sibling and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `left_id` = `left_id` + '.(int) $node->width.', `right_id` = `right_id` + '.(int) $node->width .
			' WHERE `left_id` BETWEEN '.(int) $sibling->left_id.' AND '.(int) $sibling->right_id .
			' AND `'.$this->_tbl_key.'` NOT IN ('.implode(',', $children).')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to move a node one position to the right in the same level.
	 *
	 * @param	integer	Primary key of the node to move.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function orderDown($pk)
	{
		// Initialize variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Lock the table for writing.
		if (!$this->_lock()) {
			// Error message set in lock method.
			return false;
		}

		// Get the node by primary key.
		if (!$node = $this->_getNode($pk)) {
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the right sibling node.
		if (!$sibling = $this->_getNode($node->right_id + 1, 'left')) {
			// Error message set in getNode method.
			$this->_unlock();
			return false;
		}

		// Get the primary keys of child nodes.
		$this->_db->setQuery(
			'SELECT `'.$this->_tbl_key.'`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
		);
		$children = $this->_db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the node and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `left_id` = `left_id` + '.(int) $sibling->width.', `right_id` = `right_id` + '.(int) $sibling->width.'' .
			' WHERE `left_id` BETWEEN '.(int) $node->left_id.' AND '.(int) $node->right_id
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Shift left and right values for the sibling and it's children.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `left_id` = `left_id` - '.(int) $node->width.', `right_id` = `right_id` - '.(int) $node->width .
			' WHERE `left_id` BETWEEN '.(int) $sibling->left_id.' AND '.(int) $sibling->right_id .
			' AND `'.$this->_tbl_key.'` NOT IN ('.implode(',', $children).')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			$this->_unlock();
			return false;
		}

		// Unlock the table for writing.
		$this->_unlock();

		return true;
	}

	/**
	 * Method to get nested set properties for a node in the tree.
	 *
	 * @param	integer	Value to look up the node by.
	 * @param	string	Key to look up the node by.
	 * @return	mixed	Boolean false on failure or node object on success.
	 * @since	1.6
	 */
	protected function _getNode($id, $key = null)
	{
		// Determine which key to get the node base on.
		switch ($key)
		{
			case 'parent':
				$k = 'parent_id';
				break;
			case 'left':
				$k = 'left_id';
				break;
			case 'right':
				$k = 'right_id';
				break;
			default:
				$k = $this->_tbl_key;
				break;
		}

		// Get the node data.
		$this->_db->setQuery(
			'SELECT `'.$this->_tbl_key.'`, `parent_id`, `level`, `left_id`, `right_id`' .
			' FROM `'.$this->_tbl.'`' .
			' WHERE `'.$k.'` = '.(int) $id,
			0, 1
		);
		$row = $this->_db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Do some simple calculations.
		$row->numChildren = (int) ($row->right_id - $row->left_id - 1) / 2;
		$row->width = (int) $row->right_id - $row->left_id + 1;

		return $row;
	}

	/**
	 * Method to get various data necessary to make room in the tree at a location
	 * for a node and its children.  The returned data object includes conditions
	 * for SQL WHERE clauses for updating left and right id values to make room for
	 * the node as well as the new left and right ids for the node.
	 *
	 * @param	object	A node object with at least a 'left_id' and 'right_id' with
	 * 					which to make room in the tree around for a new node.
	 * @param	integer	The width of the node for which to make room in the tree.
	 * @param	string	The position relative to the reference node where the room
	 * 					should be made.
	 * @return	mixed	Boolean false on failure or data object on success.
	 * @since	1.6
	 */
	protected function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before')
	{
		// Make sure the reference an object with a left and right id.
		if (!is_object($referenceNode) && isset($referenceNode->left_id) && isset($referenceNode->right_id)) {
			return false;
		}

		// A valid node cannot have a width less than 2.
		if ($nodeWidth < 2) {
			return false;
		}

		// Initialize variables
		$k = $this->_tbl_key;
		$data = new stdClass;

		// Run the calculations and build the data object by reference position.
		switch ($position)
		{
			case 'first-child':
				$data->left_where = 'left_id > '.$referenceNode->left_id;
				$data->right_where = 'left_id >= '.$referenceNode->left_id;

				$data->new_left_id 		= $referenceNode->left_id + 1;
				$data->new_right_id		= $referenceNode->left_id + $nodeWidth;
				$data->new_parent_id	= $referenceNode->$k;
				$data->new_level		= $referenceNode->level + 1;
				break;

			case 'last-child':
				$data->left_where = 'right_id >= '.$referenceNode->right_id;
				$data->right_where = 'right_id > '.$referenceNode->right_id;

				$data->new_left_id		= $referenceNode->right_id - $nodeWidth;
				$data->new_right_id		= $referenceNode->right_id - 1;
				$data->new_parent_id	= $referenceNode->$k;
				$data->new_level		= $referenceNode->level + 1;
				break;

			case 'before':
				$data->left_where = 'left_id >= '.$referenceNode->left_id;
				$data->right_where = 'right_id >= '.$referenceNode->right_id;

				$data->new_left_id		= $referenceNode->left_id;
				$data->new_right_id 	= $referenceNode->left_id + $nodeWidth - 1;
				$data->new_parent_id	= $referenceNode->parent_id;
				$data->new_level		= $referenceNode->level;
				break;

			default:
			case 'after':
				$data->left_where = 'left_id > '.$referenceNode->left_id;
				$data->right_where = 'right_id > '.$referenceNode->right_id;

				$data->new_left_id 		= $referenceNode->right_id + 1;
				$data->new_right_id		= $referenceNode->right_id + $nodeWidth;
				$data->new_parent_id	= $referenceNode->parent_id;
				$data->new_level		= $referenceNode->level;
				break;
		}

		return $data;
	}
}
