<?php
/**
 * @version		$Id: hardlinked.php 554 2007-11-07 16:16:12Z friesengeist $
 * @package		Joomla.Framework
 * @subpackage	Database.Table
 * @license		GNU General Public License
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jregister('JBaseTable', 'joomla.database.table.base');
jimport('joomla.database.query');
require_once dirname(__FILE__).DS.'nbs.interface.php';

/**
 * Hardlinked Nested Sets database table library
 *
 * Hardlinked Nested Sets offer the possibility to have the same subtree in multiple places in one
 * NBS table. For reading data, nothing changes. Writing data will be done to all hardlinked copies
 * automatically, invisible to the user
 *
 * @abstract
 * @author		Enno Klasing <friesengeist@googlemail.com>
 * @package		Joomla.Framework
 * @subpackage	Database.Table
 * @since		2.0
 */
abstract class JHardlinkedTable extends JBaseTable implements JNBSTableInterface
{
	/**
	 * NBS adjunct table
	 */
	protected $_nbsTable;

	/**
	 * Primary key of the NBS table
	 */
	protected $_nbsKey;

	/**
	 * Name of the "Hardlinked Id" field of the NBS table
	 */
	protected $_nbsKeyHL;

	/**
	 * Name of the field which maps to the primary key of the data table
	 */
	protected $_nbsDataKey;

	/**
	 * Name of the 'lft' field on the NBS table
	 */
	protected $_nbsLft;

	/**
	 * Name of the 'rgt' field on the NBS table
	 */
	protected $_nbsRgt;

	/**
	 * Object with additional (public!) fields in the NBS table. The 'lft' and 'rgt' fields are not
	 * public, the key will be added automatically by the constructor
	 */
	protected $_nbsObject;

	/**
	 * Constructor to set class properties, like the table name, key field etc.
	 *
	 * @access	public
	 * @param	array	Array with the class properties which should be set. Valid settings include
	 *					the following keys (child classes may specify more private settings):
	 *					'db'		=> JDatabase object [required]
	 *					'table'		=> Name of the DB table [required] [*]
	 *					'key'		=> Name of the primary key field in the table [required] [*]
	 *					'nbsTable'	=> Name of the NBS tree structure table. Default: table.'_nbs' [*]
	 *					'nbsKey'	=> Name of the primary key field of the NBS table. Default: id [*]
	 *					'nbsKeyHardlinked'
	 *								=> Name of the "Hardlinked Id" field. Default: ref_id [*]
	 *					'nbsDataKey'
	 *								=> Name of the field which maps to the primary key of the data
	 *								   table. Default: data_id [*]
	 *					'nbsLft'	=> Name of the 'lft' field on the NBS table. Default: lft [*]
	 *					'nbsRgt'	=> Name of the 'rgt' field on the NBS table. Default: rgt [*]
	 *					'nbsFields'	=> Array of additional (public!) fields in the NBS table. The
	 *								   'lft' and 'rgt' fields are not public, the nbsKey and the
	 *								   nbsDataKey will be added automatically. Default: array() [*]
	 * @todo	Check if the params marked with [*] are really needed in the constructor. It should
	 *			be enough if each child class specifies them in their protected properties (or
	 *			sets them in their own constructor, like 'nbsFields').
	 * @since	2.0
	 */
	public function __construct(array $settings)
	{
		parent::__construct($settings);

		$this->_nbsTable =
			!empty($settings['nbsTable']) ? $settings['nbsTable'] : $settings['table'].'_nbs'
		;
		$this->_nbsKey =
			!empty($settings['nbsKey']) ? $settings['nbsKey'] : 'id'
		;
		$this->_nbsKeyHL =
			!empty($settings['nbsKeyHardlinked']) ? $settings['nbsKeyHardlinked'] : 'ref_id'
		;
		$this->_nbsDataKey =
			!empty($settings['nbsDataKey']) ? $settings['nbsDataKey'] : 'data_id'
		;
		$this->_nbsLft =
			!empty($settings['nbsLft']) ? $settings['nbsLft'] : 'lft'
		;
		$this->_nbsRgt =
			!empty($settings['nbsRgt']) ? $settings['nbsRgt'] : 'rgt'
		;

		$this->_nbsObject = new stdClass;
		$this->_nbsObject->{$this->_nbsKeyHL} = null;
		$this->_nbsObject->{$this->_nbsDataKey} = null;
		if (isset($settings['nbsFields']) && is_array($settings['nbsFields'])) {
			foreach ($settings['nbsFields'] as $key) {
				$this->_nbsObject->$key = null;
			}
		}
	}

	/**
	 * Resets the values of all DB fields of this table and it's NBS table to the default values
	 *
	 * @access	public
	 * @return	void
	 * @since	2.0
	 */
	public function reset()
	{
		/*
		 * Reset all fields of the data table. Since the called funcion only resets *public*
		 * properties, we do not have to worry about _nbs* properties being reset
		 */
		parent::reset();

		// Additionaly, reset the primary key of the data table (always null, no default value)
		$this->{$this->_tbl_key} = null;

		// Reset all fields of the NBS table
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if ($key != $this->_nbsKeyHL) {
				$this->_nbsObject->$key = null;
			}
		}
	}

	/**
	 * Binds an array or object to this object
	 *
	 * @access	public
	 * @param	mixed	An associative array or object to bind to the public data fields
	 * @param	mixed	An associative array or object to bind to the public NBS fields [optional]
	 * @param	array	An array of data fields which must not be bound. The primary key of the
	 *					data table will always be ignored, and actually be reset to null [optional]
	 * @param	array	An array of NBS fields which must not be bound. The field which points to
	 *					the primary key of the data table will be ignored, and actually be reset to
	 *					null. To change the data entry which is referenced by a node, use the
	 *					function JNSTableInterface::TODO [optional]
	 * @return	boolean
	 * @since	2.0
	 */
	public function bind($dataFields, $nbsFields=array(), array $dataIgnore=array(), array $nbsIgnore=array())
	{
		if ($dataFields !== null && !is_array($dataFields) && !is_object($dataFields)) {
			return false;
		}
		if ($nbsFields !== null && !is_array($nbsFields) && !is_object($nbsFields)) {
			return false;
		}
		$dataFields	= (object) $dataFields;
		$nbsFields	= (object) $nbsFields;

		// Bind all data fields which are not in the ignore list
		foreach ($this->getPublicProperties() as $key => $value) {
			if (!in_array($key, $dataIgnore) && isset($dataFields->$key)) {
				$this->$key = $dataFields->$key;
			}
		}

		// Bind all NBS fields which are not in the ignore list
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if (!in_array($key, $nbsIgnore) && isset($nbsFields->$key)) {
				$this->_nbsObject->$key = $nbsFields->$key;
			}
		}

		/*
		 * Add the primary key of the data table and the field which references this key of the NBS
		 * table to the ignore list
		 */
		$this->{$this->_tbl_key} = null;
		$this->_nbsObject->{$this->_nbsDataKey} = null;

		return true;
	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	int		Key of the node in the NBS table
	 * @return	boolean	True on success
	 * @since	2.0
	 */
	public function load($node)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (!$node = (int) $node) {
			return false;
		}

		/*
		 * Select the NBS entry and the data entry in one query. A little overhead, but better than
		 * looking and unlooking the DB and doing two separate queries
		 */
		$query = new JQuery;
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			$query->select('nbs.'.$key.' AS nbs_'.$key);
		}
		foreach($this->getPublicProperties() as $key => $value) {
			$query->select('data.'.$key.' AS data_'.$key);
		}
		$query->from($this->_nbsTable.' AS nbs');
		$query->join($this->_tbl.' AS data');
		$query->where($this->_nbsKey.' = '.(int) $node);
		$query->where('nbs.'.$this->_nbsDataKey.' = data.'.$this->_tbl_key); // Todo: JOIN instead of WHERE
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();

		// Do we have a result?
		if ($result) {
			$this->reset();

			// Bind the result to this object
			foreach (get_object_vars($result) as $key => $value) {
				list($table, $property) = explode('_', $key, 2);
				if ($table == 'nbs') {
					$this->_nbsObject->$property = $value;
				} else {
					$this->$property = $value;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * JNBSTableInterface::store() does nothing, use update() instead
	 *
	 * @access	public
	 * @param	boolean	Parameter for compatibility with JBaseTableInterface (once we have it ;) )
	 * @return	boolean	False (always)
	 * @since	2.0
	 */
	final public function store($updateNulls=false)
	{
		return false;
	}

	/**
	 * Updates a node in the NBS table and it's associated entry in the data table
	 *
	 * @access	public
	 * @param	boolean	True to also update 'NULL' value fields [optional]
	 * @return	boolean	True on success
	 * @since	2.0
	 */
	public function update($updateNulls=false)
	{
		$node = $this->_nbsObject->{$this->_nbsKeyHL};
		if (!$node = (int) $node) {
			return false;
		}

		// Construct query to update all fields on both tables in one single UPDATE statement
		$query = new JQuery;
		$query->update($this->_nbsTable.' AS nbs');
		$query->update($this->_tbl.' AS data');
		$query->where('nbs.'.$this->_nbsKeyHL.' = ?node');
		$query->where('nbs.'.$this->_nbsDataKey.' = data.'.$this->_tbl_key);
		$query->bind('?node', $node);
		$updatedFields = false;

		// Update the fields of the NBS table
		foreach (get_object_vars($this->_nbsObject) as $key => $value) {
			// Don't update the primary key or the id of the referenced data entry
			if ($key == $this->_nbsKeyHL || $key == $this->_nbsDataKey) {
				continue;
			}

			if ($value !== null) {
				$query->set('nbs.'.$key, $this->_db->Quote($value));
				$updatedFields = true;
			} elseif ($updateNulls) {
				$query->set('nbs.'.$key, 'NULL');
				$updatedFields = true;
			}
		}

		// Update the fields of the data table
		foreach($this->getPublicProperties() as $key => $value) {
			// Don't update the primary key
			if ($key == $this->_tbl_key) {
				continue;
			}

			if ($this->$key !== null) {
				$query->set('data.'.$key, $this->_db->Quote($this->$key));
				$updatedFields = true;
			} elseif ($updateNulls) {
				$query->set('data.'.$key, 'NULL');
				$updatedFields = true;
			}
		}

		// Execute the query if there is at least one field which needs updating
		if ($updatedFields) {
			$return1 = $this->_db->setQuery($query)->query();
			$return2 = $this->_db->getAffectedRows();
			return $return1 && $return2;
		} else {
			return false;
		}
	}

	/**
	 * Determines if the given node is a leaf node
	 *
	 * @access	public
	 * @param	int		Node Id number [optional]
	 * @return	boolean	True if the node is a leaf
	 * @since	2.0
	 */
	public function isLeafNode($node=null)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (!$node = (int) $node) {
			return false;
		}

		$query = new JQuery;
		$query->select($this->_nbsKey);
		$query->from($this->_nbsTable);
		$query->where($this->_nbsRgt.' = '.$this->_nbsLft.' + 1');
		$query->where($this->_nbsKeyHL.' = ?node');
		$query->bind('?node', $node);
		$this->_db->setQuery($query)->query();

		return (boolean) $this->_db->getNumRows();
	}

	/**
	 * Returns the path of nodes to a given node
	 *
	 * @access	public
	 * @param	int		Id of the node to get the path for [optional]
	 * @param	boolean	True to return an array of paths for hardlinked nodes. Each array element
	 *					will contain an array with the nodes of one path. Otherwise, the path to the
	 *					first node which is found is returned [optional]
	 * @return	array	Node objects in path order
	 * @since	2.0
	 */
	public function getPath($node=null, $allPaths=false)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (!$node = (int) $node) {
			return array();
		}

		$query = new JQuery;
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			$query->select('parent.'.$key);
		}
		// Field which does most likely not exist in the NBS table ;)
		$query->select('nodes.'.$this->_nbsLft.' AS tempname2839w45792');
		$query->from($this->_nbsTable.' AS nodes');
		$query->join($this->_nbsTable.' AS parent');
		$query->where('nodes.'.$this->_nbsLft
			.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
		);
		$query->where('nodes.'.$this->_nbsKeyHL.' = ?node');
		$query->order('nodes.'.$this->_nbsLft);
		$query->order('parent.'.$this->_nbsLft);
		$query->bind('?node', $node);
		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();

		// Order the paths into arrays, one for each path
		$return			= array();
		$currNodeLft	= null;
		$currPath		= -1;
		foreach ($result as $pathNode) {
			$lft = $pathNode->tempname2839w45792;
			unset($pathNode->tempname2839w45792);

			if ($currNodeLft !== $lft) {
				if (!$allPaths && $currNodeLft !== null) {
					break;
				}
				$currNodeLft = $lft;
				$currPath++;
			}

			$return[$currPath][] = $pathNode;
			
		}

		if (!$allPaths && isset($return[0])) {
			return $return[0];
		} else {
			return $return;
		}
	}

	/**
	 * Returns an array of all the nodes in the tree starting from the given root
	 *
	 * @access	public
	 * @param	int		Maximum tree depth [optional]
	 * @param	int		Id of the root node to get the tree for [optional]
	 * @param	string	Name of the 'depth' field in the result set
	 * @return	array	Node objects in preorder traversal order
	 * @since	2.0
	 */
	public function getTree($maxDepth=0, $node=null, $depth='depth')
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (($node !== 0) && (!$node = (int) $node)) {
			return array();
		}
		$maxDepth = (int) $maxDepth;
		if (!preg_match('/^[a-z]+[a-z0-9]*$/i', $depth)) {
			$depth = 'depth';
		}

		$query = new JQuery;

		// Build the where statement depending upon the root node Id
		if ($node) {
			$lft = new JQuery;
			$lft->from($this->_nbsTable);
			$lft->where($this->_nbsKeyHL.' = ?node');
			$lft->order($this->_nbsLft);
			$lft->limit(1);
			$lft->bind('?node', $node);
			$rgt = clone $lft;
			$lft->select($this->_nbsLft);
			$rgt->select($this->_nbsRgt);

			$query->where('parent.'.$this->_nbsLft
				.' BETWEEN ('.$lft->toString().') AND ('.$rgt->toString().')'
			);
			$query->bind('?node', $node);
		}

		// Build the having statement depending upon $maxDepth
		if ($maxDepth) {
			$query->having($depth.' <= ?depth');
			$query->bind('?depth', $maxDepth);
		}

		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			$query->select('nodes.'.$key);
		}
		$query->select('(COUNT(parent.'.$this->_nbsKey.') - 1) AS '.$depth);
		$query->from($this->_nbsTable.' AS nodes');
		$query->join($this->_nbsTable.' AS parent');
		$query->where('nodes.'.$this->_nbsLft
			.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
		);
		$query->group('nodes.'.$this->_nbsKey);
		$query->order('nodes.'.$this->_nbsLft);
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	/**
	 * Finds out the relative position of two nodes to each other
	 *
	 * Returns JNBSTable::CHILD, JNBSTable::PARENT, JNBSTable::SAME or JNBSTable::UNRELATED to
	 * indicate the relation of the node given in the first parameter towards either the current
	 * node in this object, or the node in the second parameter (if given).
	 * For example, JNBSTable::CHILD means that the current node is an (indirect) child of the
	 * node given in the first parameter.
	 *
	 * @access	public
	 * @param	int		Id of the node towards which the relation should be checked
	 * @param	int		Id of the node from which the relation should be checked [optional]
	 * @return	mixed	One of the possible relation constants (integer), or false on failure
	 * @since	2.0
	 */
	public function getRelation($node2, $node1=null)
	{
		if ($node1 === null) {
			$node1 = $this->{$this->_tbl_key};
		}
		if ((!$node1 = (int) $node1) || (!$node2 = (int) $node2)) {
			return false;
		}

		// Same node? Save a query
		if ($node1 == $node2) {
			return self::SAME;
		}

		// Query information about the relationship
		$query = new JQuery;
		$query->select('node1.'.$this->_nbsLft
			.' BETWEEN node2.'.$this->_nbsLft.' AND node2.'.$this->_nbsRgt.' AS child'
		);
		$query->select('node2.'.$this->_nbsLft
			.' BETWEEN node1.'.$this->_nbsLft.' AND node1.'.$this->_nbsRgt.' AS parent'
		);
		$query->from($this->_nbsTable.' AS node1');
		$query->from($this->_nbsTable.' AS node2');
		$query->where('node1.'.$this->_nbsKeyHL.' = ?node1');
		$query->where('node2.'.$this->_nbsKeyHL.' = ?node2');
		$query->bind('?node1', $node1);
		$query->bind('?node2', $node2);
		$this->_db->setQuery($query);

		$relations = $this->_db->loadObjectList();

		// Do we have a result? Otherwise, at least one of the nodes does not exist
		if (count($relations) < 1) {
			return false;
		}

		// Calculate the correct relationship, in case multiple rows were returned
		$relation = array(self::CHILD=>false, self::PARENT=>false);
		foreach ($relations as $currRelation) {
			$relation[self::CHILD]	|= $currRelation->child;
			$relation[self::PARENT]	|= $currRelation->parent;
		}

		// Return the correct relationship
		return ($relation[self::CHILD] * self::CHILD) + ($relation[self::PARENT] * self::PARENT);
	}

	/**
	 * Inserts a new node at a given position into the tree
	 *
	 * The location of the new node relative to the given parent node can be specified by using
	 * one of the following constants for the $where parameter:
	 * JNBSTable::BEFORE, JNBSTable::AFTER, JNBSTable::FIRST_CHILD, JNBSTable::LAST_CHILD
	 *
	 * @access	public
	 * @param	int		Id of the parent node
	 * @param	int		Position for the new node relative to the parent node
	 * @return	mixed	Id of the new node (int), or false on failure
	 * @since	2.0
	 */
	public function insertNode($parent, $where=self::AFTER)
	{
		if (($parent !== 0) && (!$parent = (int) $parent)) {
			return false;
		}

		// Lock the table so that nothing gets messed with while we insert our node(s)
		$this->_db->lockTables(
			array($this->_nbsTable, $this->_nbsTable=>'parent', $this->_tbl), true
		);

		// Fetch information about the parent node
		$parents = $this->getTargetNodes($parent, $where);

		// Do we have at least one parent node?
		if ($parents === false) {
			$this->_db->unlockTables();
			return false;
		}

		// Construct query elements for the following two operations (creating gaps, adding nodes)
		$updateLft	= 'CASE';
		$updateRgt	= 'CASE';
		for ($i = count($parents) - 1; $i >= 0; $i--) {
			/*
			 * Close the gaps in the lft and rgt sequence Ids. Since SQL executes the first
			 * expression that matches, the nodes are handled in reversed order by this for() loop
			 */
			$updateLft .=
				' WHEN '.$this->_nbsLft.' >= '.(int) $parents[$i]->sequenceId.' THEN '.($i + 1) * 2
			;
			$updateRgt .=
				' WHEN '.$this->_nbsRgt.' >= '.(int) $parents[$i]->sequenceId.' THEN '.($i + 1) * 2
			;
		}
		$updateLft .= ' ELSE 0 END';
		$updateRgt .= ' ELSE 0 END';

		// Update the left and right values of all following nodes
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft.' + '.$updateLft);
		$query->set($this->_nbsRgt, $this->_nbsRgt.' + '.$updateRgt);
		$query->where($this->_nbsRgt.' >= ?insert');
		$query->bind('?insert', $parents[0]->sequenceId);
		$this->_db->setQuery($query)->query();

		// Insert the data object
		$this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		$this->_nbsObject->{$this->_nbsDataKey} = $this->{$this->_tbl_key};

		// Insert the NBS object(s)
		$fields = array($this->_nbsKey);
		$values = array(0);
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if ($value === null) {
				continue;
			}
			$fields[] = $this->_db->nameQuote($key);
			$values[] = $this->_db->Quote($value);
		}
		$fields[] = $this->_nbsLft;
		$fields[] = $this->_nbsRgt;
		$fields = implode(', ', $fields);
		$commonValues = implode(', ', $values);
		$values = array();
		for ($i = 0, $n = count($parents); $i < $n; $i++) {
			$lft = $parents[$i]->sequenceId + $i * 2;
			$values[] = $commonValues.', '.$lft.', '.($lft + 1);
		}
		$query = 'INSERT INTO '.$this->_nbsTable.' ('.$fields.') '
			.' VALUES ('.implode('), (', $values).')'
		;
		$this->_db->setQuery($query)->query();
		$this->_nbsObject->{$this->_nbsKeyHL} = $this->_db->insertid();

		// Set the ref_id of the new node to the actual Id of the new node
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsKeyHL, $this->_nbsObject->{$this->_nbsKeyHL});
		$query->where($this->_nbsKeyHL.' = 0');
		$this->_db->setQuery($query)->query();

		// Finished the transaction, lets unlock the table
		$this->_db->unlockTables();

		return $this->_nbsObject->{$this->_nbsKeyHL};
	}

	/**
	 * Delete a node (and optionally all of it's children) from the tree
	 *
	 * Currently, this function also deletes the associated data rows. This should be made
	 * configurable in later versions of this class, so that multiple NBS tables can reference the
	 * same data table. Only one NBS table must be declared as the 'master' table in this case, and
	 * only this table class may then delete the data rows
	 *
	 * @access	public
	 * @param	boolean	True to remove children, otherwise move all children up to the level of the
	 *					deleted node [optional]
	 * @param	int		The node Id of the node to delete [optional]
	 * @param	boolean	True to remove all hardlinked copies of this node. This will be done
	 *					automatically if the parent of the node to delete is hardlinked itself.
	 *					Otherwise, the operation will fail if this is not set to true [optional]
	 * @return	boolean	True on success
	 * @since	2.0
	 */
	public function deleteNode($removeChildren=false, $node=null, $removeHardlinks=false)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (!$node = (int) $node) {
			return false;
		}

		// Lock the table so that nothing gets messed with while we delete our node(s)
		$this->_db->lockTables(
			array($this->_nbsTable, $this->_nbsTable=>'parent', $this->_tbl), true
		);

		// Fetch information about the node(s) which are about to be deleted
		$query = new JQuery;
		$query->select($this->_nbsLft.' AS lft');
		$query->select($this->_nbsRgt.' AS rgt');
		$query->from($this->_nbsTable);
		$query->where($this->_nbsKeyHL.' = ?node');
		$query->order('lft');
		$query->bind('?node', $node);
		$this->_db->setQuery($query);

		$delNodes = $this->_db->loadObjectList();

		// Do we have at least one node to delete?
		if (count($delNodes) < 1) {
			$this->_db->unlockTables();
			return false;
		}

		/*
		 * Special handling for deleting hardlinked nodes: if their parent nodes are not hardlinked
		 * as well, this functions fails (for now), since we would not know which one of the
		 * possible nodes to delete. Also, it is being checked that the delete operation does not
		 * create a tree with two hardlinked nodes beneath the same direct parent node
		 */
		if (!$removeHardlinks || !$removeChildren) {
			// Find out the ref_id(s) of the direct parent of each node
			$query = new JQuery;
			$query->select($this->_nbsTable.'.'.$this->_nbsLft.' AS lft');
			$query->select('parent.'.$this->_nbsKeyHL.' AS parent_id');
			$query->select('parent.'.$this->_nbsLft.' AS parent_lft');
			$query->select('parent.'.$this->_nbsRgt.' AS parent_rgt');
			$query->from($this->_nbsTable);
			$query->join($this->_nbsTable.' AS parent');
			$query->where($this->_nbsTable.'.'.$this->_nbsLft
				.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
			);
			$query->where($this->_nbsTable.'.'.$this->_nbsKeyHL.' = ?node');
			$query->order($this->_nbsTable.'.'.$this->_nbsLft);
			$query->order('parent.'.$this->_nbsLft);
			$query->bind('?node', $node);
			$this->_db->setQuery($query);
			$parents = $this->_db->loadObjectList();

			// Find all direct parent nodes for the subject
			$targetParents	= array();
			$directTargetParentLft = 0;
			$directTargetParentRgt = 0;
			$currNodeLft	= false;
			foreach($parents as $currParent) {
				// Are we building the path to a new node, or still to the same node as before?
				if ($currNodeLft != $currParent->lft) {
					// Switch to the new path
					$currNodeLft = $currParent->lft;

					// Add the 'root' node as the first element to the parent nodes
					$targetParents[$currNodeLft] = 0;
				}

				if ($currParent->parent_id != $node) {
					$targetParents[$currNodeLft] = $currParent->parent_id;
					$directTargetParentLft = $currParent->parent_lft;
					$directTargetParentRgt = $currParent->parent_rgt;
				}
			}

			// Different parent nodes? Fail for now...
			if (count($delNodes) > 1 && !$removeHardlinks) {
				$lastTargetParent = array_pop($targetParents);
				foreach ($targetParents as $targetParent) {
					if ($targetParent != $lastTargetParent) {
						$this->_db->unlockTables();
						return false;
					}
				}
			}

			// Query for all siblings and all nodes which are one level below the 'deleted' node
			$query = new JQuery;
			$query->select($this->_nbsTable.'.'.$this->_nbsKeyHL.' AS ref_id');
			$query->select('(COUNT(parent.'.$this->_nbsKey.') - 1) AS depth');
			$query->from($this->_nbsTable);
			$query->join($this->_nbsTable.' AS parent');
			$query->where($this->_nbsTable.'.'.$this->_nbsLft
				.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
			);
			if ($directTargetParentLft && $directTargetParentRgt) {
				$query->where('parent.'.$this->_nbsLft
					.' BETWEEN '.($directTargetParentLft + 1).' AND '.($directTargetParentRgt - 1)
				);
			}
			$query->group($this->_nbsTable.'.'.$this->_nbsKey);
			$query->order($this->_nbsTable.'.'.$this->_nbsLft);
			$query->having('depth <= 1');
			$this->_db->setQuery($query);
			$siblingsAndChildren = $this->_db->loadObjectList();

			// Find out the ref_id(s) of all siblings and all direct children of the 'deleted' node
			$siblings = array();
			$children = array();
			$childNodesActive = false;
			foreach($siblingsAndChildren as $currNode) {
				if ($currNode->depth == 0) {
					if ($currNode->ref_id == $node) {
						$childNodesActive = true;
						continue;
					} else {
						$childNodesActive = false;
					}
				}

				if ($childNodesActive) {
					$children[] = $currNode->ref_id;
				} elseif ($currNode->depth == 0) {
					$siblings[] = $currNode->ref_id;
				}
			}

			// Do siblings and children have a _common_ node?
			foreach ($siblings as $sibling) {
				foreach ($children as $child) {
					if ($sibling === $child) {
						$this->_db->unlockTables();
						return false;
					}
				}
			}
		}

		// Construct query elements for the following two operations (removing nodes, closing gaps)
		$gap		= $removeChildren ? ($delNodes[0]->rgt - $delNodes[0]->lft + 1) : 2;
		$updateLft	= 'CASE';
		$updateRgt	= 'CASE';
		$deleteCond	= array();
		for ($i = count($delNodes) - 1; $i >= 0; $i--) {
			$delNode = $delNodes[$i];

			// Delete the node (including children, if requested)
			if ($removeChildren) {
				$deleteCond[] = $this->_nbsLft
					.' BETWEEN '.(int) $delNode->lft.' AND '.(int) $delNode->rgt
				;
			} else {
				$deleteCond[] = $this->_nbsLft.' = '.(int) $delNode->lft;
			}

			/*
			 * Close the gaps in the lft and rgt sequence Ids. Since SQL executes the first
			 * expression that matches, the nodes are handled in reversed order by this for() loop
			 */
			$updateLft .=
				' WHEN '.$this->_nbsLft.' >= '.(int) $delNode->rgt.' THEN '.($i + 1) * $gap
			;
			$updateRgt .=
				' WHEN '.$this->_nbsRgt.' >= '.(int) $delNode->rgt.' THEN '.($i + 1) * $gap
			;
			if (!$removeChildren) {
				$updateLft .=
					' WHEN '.$this->_nbsLft.' >= '.(int) $delNode->lft.' THEN '.($i * $gap + 1)
				;
				$updateRgt .=
					' WHEN '.$this->_nbsRgt.' >= '.(int) $delNode->lft.' THEN '.($i * $gap + 1)
				;
			}
		}
		$updateLft .= ' ELSE 0 END';
		$updateRgt .= ' ELSE 0 END';
		$deleteCond = implode(' OR ', $deleteCond);

		// Delete the node (including children, if requested)
		$query = new JQuery;
		$query->delete($this->_nbsTable);
		$query->delete($this->_tbl);
		// TODO: Ugly, I know. Fix this when JQuery is more capable of multi-table deletes
		$query->using($this->_nbsTable.' LEFT JOIN '.$this->_tbl
			.' ON '.$this->_nbsTable.'.'.$this->_nbsDataKey.' = '.$this->_tbl.'.'.$this->_tbl_key
		);
		$query->where($deleteCond);
		$this->_db->setQuery($query)->query();

		// Update the left and right values of all previous children and all following nodes
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft.' - '.$updateLft);
		$query->set($this->_nbsRgt, $this->_nbsRgt.' - '.$updateRgt);
		$query->where($this->_nbsRgt.' > ?lft');
		$query->bind('?lft', $delNodes[0]->lft);
		$this->_db->setQuery($query)->query();

		// Finished the transaction, lets unlock the tables
		$this->_db->unlockTables();
		return true;
	}

	/**
	 * Moves a node to a given position in the tree
	 *
	 * The location of the node which is being moved can be specified relative to the given parent
	 * node by using one of the following constants for the $where parameter:
	 * JNBSTable::BEFORE, JNBSTable::AFTER, JNBSTable::FIRST_CHILD, JNBSTable::LAST_CHILD
	 *
	 * @access	public
	 * @param	int		Id of the parent node
	 * @param	int		Position for the new node relative to the parent node
	 * @param	int		Id of the node which is about to be moved [optional]
	 * @return	boolean	True on success
	 * @since	2.0
	 */
	public function moveNode($parent, $where=self::AFTER, $node=null)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (!$node = (int) $node) {
			return false;
		}
		if (($parent !== 0) && (!$parent = (int) $parent)) {
			return false;
		}

		// Lock the table so that nothing gets messed with while we move our node(s)
		$this->_db->lockTables(array($this->_nbsTable, $this->_nbsTable=>'parent'), true);

		// Fetch information about the subject node(s)
		$query = new JQuery;
		$query->select($this->_nbsLft.' AS lft');
		$query->select($this->_nbsRgt.' AS rgt');
		$query->from($this->_nbsTable);
		$query->where($this->_nbsKeyHL.' = ?node');
		$query->order($this->_nbsLft);
		$query->bind('?node', $node);
		$this->_db->setQuery($query);

		$subject = $this->_db->loadObjectList();

		// Do we have at least one subject node?
		if (count($subject) < 1) {
			$this->_db->unlockTables();
			return false;
		}

		// Fetch information about the target node(s)
		$query = new JQuery;
		$query->from($this->_nbsTable);
		if ($parent) {
			// Find out the lft sequece Id _after/beneath_ which the tree is being moved/copied
			if ($where == self::BEFORE) {
				$query->select($this->_nbsLft.' AS sequenceId');
			} elseif ($where == self::FIRST_CHILD) {
				$query->select($this->_nbsLft.' + 1 AS sequenceId');
			} elseif ($where == self::LAST_CHILD) {
				$query->select($this->_nbsRgt.' AS sequenceId');
			} else {
				$query->select($this->_nbsRgt.' + 1 AS sequenceId');
			}
			$query->where($this->_nbsKeyHL.' = ?target');
			$query->order($this->_nbsLft);
			$query->bind('?target', $parent);
			$this->_db->setQuery($query);
			$target = $this->_db->loadObjectList();
		} else {
			// Find the last node on the root level for inserting at the end, or 1 for the beginning
			if ($where == self::BEFORE || $where == self::FIRST_CHILD) {
				$target[0]->sequenceId = 1;
			} else {
				$query->select('MAX('.$this->_nbsRgt.') + 1');
				$this->_db->setQuery($query);
				$target[0]->sequenceId = $this->_db->loadResult();
			}
		}

		// Do we have at least one target node, and also the same count of subject and target nodes?
		if (count($target) != count($subject)) {
			$this->_db->unlockTables();
			return false;
		}

		// Does the source stay at the same position? Do nothing
		if ($target[0]->sequenceId == $subject[0]->lft ||
			$target[0]->sequenceId == $subject[0]->rgt + 1
		) {
			$this->_db->unlockTables();
			return true;
		}

		// Is the source being moved into itself? Stupid idea.
		if ($subject[0]->lft < $target[0]->sequenceId &&
			$target[0]->sequenceId <= $subject[0]->rgt
		) {
			$this->_db->unlockTables();
			return false;
		}

		/*
		 * Special handling for moving hardlinked branches: all source and target nodes must have a
		 * common (indirect) parent node, and all these parent nodes itself must be hardlinked.
		 */
		if (count($subject) > 1 || count($target) > 1) {
			// Find out the ref_id(s) of the parents of each source and target node
			$query = new JQuery;
			$query->select($this->_nbsTable.'.'.$this->_nbsKeyHL.' AS ref_id');
			$query->select($this->_nbsTable.'.'.$this->_nbsLft.' AS lft');
			$query->select('parent.'.$this->_nbsKeyHL.' AS parent_id');
			$query->from($this->_nbsTable);
			$query->join($this->_nbsTable.' AS parent');
			$query->where($this->_nbsTable.'.'.$this->_nbsLft
				.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
			);
			$query->where($this->_nbsTable.'.'.$this->_nbsKeyHL.' IN (?target, ?source)');
			$query->order($this->_nbsTable.'.'.$this->_nbsLft.' DESC');
			$query->order('parent.'.$this->_nbsLft.' DESC');
			$query->bind('?target', $parent);
			$query->bind('?source', $node);
			$this->_db->setQuery($query);
			$parents = $this->_db->loadObjectList();

			// Find all common parent nodes for the subject and target
			$sourceParents	= array();
			$targetParents	= array();
			$currNodeLft	= false;
			foreach($parents as $currParent) {
				// Are we building the path to a new node, or still to the same node as before?
				if ($currNodeLft != $currParent->lft) {
					// Add the 'root' node as the last element to the current path
					if ($currNodeLft !== false && $currParents[$level] !== false) {
						$currParents[$level + 1] = false;
						for ($i = $level + 2, $n = count($currParents); $i < $n; $i++) {
							unset($currParents[$i]);
						}
					}
					// Switch to the new path
					$level			= 0;
					$currNodeLft	= $currParent->lft;
					if ($currParent->ref_id == $node) {
						$currParents =& $sourceParents;
					} else {
						$currParents =& $targetParents;
					}
				} else {
					if ($currParents[$level] === false) {
						continue;
					}
					$level++;
				}

				// Set the common parent Id, or invalidate it if it has 'wrongly' been set before
				if (isset($currParents[$level])) {
					if ($currParents[$level] != $currParent->parent_id) {
						$currParents[$level] = false;
						for ($i = $level + 1, $n = count($currParents); $i < $n; $i++) {
							unset($currParents[$i]);
						}
					}
				} else {
					$currParents[$level] = $currParent->parent_id;
				}
			}

			// Delete the source and target nodes itself from the common _parents_
			array_shift($sourceParents);
			if ($where == self::AFTER || $where == self::BEFORE) {
				array_shift($targetParents);
			}

			// Delete the 'root' node from the path of common parent nodes
			unset($sourceParents[count($sourceParents)-1]);
			unset($targetParents[count($targetParents)-1]);

			// Do source and target have a _common_ parent node?
			$commonParent = false;
			foreach ($sourceParents as $sourceParent) {
				foreach ($targetParents as $targetParent) {
					if ($sourceParent === $targetParent) {
						$commonParent = true;
						break 2;
					}
				}
			}

			if (!$commonParent) {
				$this->_db->unlockTables();
				return false;
			}
		}

		// Construct query elements to update the lft and rgt sequence Ids of all affected nodes
		$updateLft	= 'CASE';
		$where		= array();
		if ($target[0]->sequenceId > $subject[0]->rgt + 1) {
			// Tree is being moved towards the end
			for ($i = count($target) - 1; $i >= 0; $i--) {
				$updateLft .=
					 ' WHEN '.$this->_nbsLft
					.' BETWEEN '.(int) $subject[$i]->lft.' AND '.(int) $subject[$i]->rgt
					.' THEN '.($target[$i]->sequenceId - $subject[$i]->rgt - 1)
					.' WHEN '.$this->_nbsLft
					.' BETWEEN '.($subject[$i]->rgt + 1).' AND '.($target[$i]->sequenceId - 1)
					.' THEN '.($subject[0]->lft - $subject[0]->rgt - 1)
				;
				$where[] = $this->_nbsLft
					.' BETWEEN '.(int) $subject[$i]->lft.' AND '.($target[$i]->sequenceId - 1)
				;
			}
		} else {
			// Tree is being moved towards the beginning
			for ($i = count($target) - 1; $i >= 0; $i--) {
				$updateLft .=
					 ' WHEN '.$this->_nbsLft
 					.' BETWEEN '.(int) $target[$i]->sequenceId.' AND '.($subject[$i]->lft - 1)
					.' THEN '.($subject[$i]->rgt - $subject[$i]->lft + 1)
					.' WHEN '.$this->_nbsLft
					.' BETWEEN '.(int) $subject[$i]->lft.' AND '.(int) $subject[$i]->rgt
					.' THEN '.($target[$i]->sequenceId - $subject[$i]->lft)
				;
				$where[] = $this->_nbsLft
					.' BETWEEN '.(int) $target[$i]->sequenceId.' AND '.(int) $subject[$i]->rgt
				;
			}
		}
		$updateLft	.= ' ELSE 0 END';
		$updateRgt	 = str_replace($this->_nbsLft, $this->_nbsRgt, $updateLft);
		$where		 = implode(' OR ', $where);
		$where		.= ' OR '.str_replace($this->_nbsLft, $this->_nbsRgt, $where);

		// Assemble and execute the query
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft.' + '.$updateLft);
		$query->set($this->_nbsRgt, $this->_nbsRgt.' + '.$updateRgt);
		$query->where($where);
		$this->_db->setQuery($query)->query();

		// Finished the transaction, lets unlock the table
		$this->_db->unlockTables();

		return true;
	}

	/**
	 * Copies a node or a whole subtree to a specified position in the tree
	 *
	 * @access	public
	 * @param	int		Id of the parent node
	 * @param	int		Position of the copied node relative to the parent node
	 * @param	int		Id of the tree to copy [optional]
	 * @param	boolean	True to create a hardlinked copy of the original [optional]
	 * @return	mixed	Id of the copied tree (int), or false on failure
	 * @since	2.0
	 */
	public function copyTree($parent, $where=self::AFTER, $node=null, $hardlinked=false)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}

		// Lock the table so that nothing gets messed with while we copy our tree
		$this->_db->lockTables(array($this->_nbsTable, $this->_nbsTable=>'parent'), true);

		// Fetch information about the subect and parent node
		$nodes = $this->getSubjectAndTargetNodes($node, $parent, $where);

		// Is the copy being put into it's own subtree, as a hardlink? Stupid idea...
		if (($nodes === false) || ($hardlinked
			&& $nodes->target[0]->sequenceId > $nodes->subject->lft
			&& $nodes->target[0]->sequenceId <= $nodes->subject->rgt)
		) {
			$this->_db->unlockTables();
			return false;
		}

		/*
		 * Avoid creating a hardlinked copy of a branch directly beneath the same direct parent
		 * @TODO: One query in getTargetNodes can be saved. Needs refactoring!
		 */
		if ($hardlinked) {
			// Find out the ref_id(s) of the direct parents of each subject/target node
			$query = new JQuery;
			$query->select($this->_nbsTable.'.'.$this->_nbsKeyHL.' AS ref_id');
			$query->select($this->_nbsTable.'.'.$this->_nbsLft.' AS lft');
			$query->select('parent.'.$this->_nbsKeyHL.' AS parent_id');
			$query->from($this->_nbsTable);
			$query->join($this->_nbsTable.' AS parent');
			$query->where($this->_nbsTable.'.'.$this->_nbsLft
				.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
			);
			$query->where($this->_nbsTable.'.'.$this->_nbsKeyHL.' IN (?target, ?source)');
			$query->order($this->_nbsTable.'.'.$this->_nbsLft);
			$query->order('parent.'.$this->_nbsLft);
			$query->bind('?target', $parent);
			$query->bind('?source', $node);
			$this->_db->setQuery($query);
			$parents = $this->_db->loadObjectList();

			// Find all direct parent nodes for the subject and target
			$sourceParents	= array();
			$targetParent	= 0;
			$currNodeLft	= false;
			foreach($parents as $currParent) {
				// Are we building the path to a new node, or still to the same node as before?
				if ($currNodeLft != $currParent->lft) {
					// Switch to the new path
					$currNodeLft	= $currParent->lft;

					// Add the 'root' node as the first element to the parent nodes
					if ($currParent->ref_id == $node) {
						$sourceParents[$currNodeLft] = 0;
					} else {
						$targetParent = 0;
					}
				}

				if ($currParent->ref_id == $node && $currParent->parent_id != $node) {
					$sourceParents[$currNodeLft] = $currParent->parent_id;
				}
				if ($currParent->ref_id == $parent &&
					($currParent->parent_id != $parent ||
					$where == self::FIRST_CHILD || $where == self::LAST_CHILD)
				) {
					$targetParent = $currParent->parent_id;
				}
			}

			// Is the target node also a direct parent of one of the source nodes?
			foreach ($sourceParents as $sourceParent) {
				if ($sourceParent == $targetParent) {
					$this->_db->unlockTables();
					return false;
				}
			}
		}

		/*
		 * Collect some data about the branch which is being copied: the gap per copy, and the lft
		 * and rgt values which it will have after the gaps for the copies have been created
		 */
		$gap				= $nodes->subject->rgt - $nodes->subject->lft + 1;
		$adjustedSourceLft	= $nodes->subject->lft;
		$adjustedSourceRgt	= $nodes->subject->rgt;

		// Construct query elements for the following two operations (adding gaps, and copying)
		$updateLft = 'CASE';
		$updateRgt = 'CASE';
		$selectJoin = '';
		for ($i = count($nodes->target) - 1; $i >= 0; $i--) {
			$curParent = $nodes->target[$i];

			/*
			 * Add the gap to the lft and rgt sequence Ids of the existing tree, to create multiple
			 * gaps for the new copies. Since SQL executes the first expression that matches, the
			 * nodes are handled in reversed order by this for() loop
			 */
			$updateLft .=
				' WHEN '.$this->_nbsLft.' >= '.(int) $curParent->sequenceId.' THEN '.($i + 1) * $gap
			;
			$updateRgt .=
				' WHEN '.$this->_nbsRgt.' >= '.(int) $curParent->sequenceId.' THEN '.($i + 1) * $gap
			;

			/*
			 * Keep track of how the lft and rgt values of the source branch are being modified by
			 * inserting the gaps. After all, we need to be able to select the source branch lateron
			 */
			$adjustedSourceLft += ($nodes->subject->lft >= $curParent->sequenceId) * $gap;
			$adjustedSourceRgt += ($nodes->subject->rgt >= $curParent->sequenceId) * $gap;

			// Adjust the lft and rgt sequence Ids for each copy which will be created
			$adjustSourceLftRgt = $curParent->sequenceId - $nodes->subject->lft + ($i * $gap);
			if ($i) {
				$selectJoin = ' UNION SELECT '.(int) $adjustSourceLftRgt.$selectJoin;
			} else {
				$selectJoin = 'SELECT '.(int) $adjustSourceLftRgt.' AS adjust'.$selectJoin;
			}
		}
		$updateLft .= ' ELSE 0 END';
		$updateRgt .= ' ELSE 0 END';

		// Make room for the new subtree(s)
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft.' + '.$updateLft);
		$query->set($this->_nbsRgt, $this->_nbsRgt.' + '.$updateRgt);
		$query->where($this->_nbsRgt.' >= ?gapStart');
		$query->bind('?gapStart', $nodes->target[0]->sequenceId);
		$this->_db->setQuery($query)->query();

		// Select the subtree...
		$query = new JQuery;
		if ($hardlinked) {
			$query->select($this->_nbsKeyHL);
			$query->select($this->_nbsLft.' + subquery.adjust - ?sourceMoved');
			$query->select($this->_nbsRgt.' + subquery.adjust - ?sourceMoved');
		} else {
			$query->select('0');
			// Special handling when the branch is being moved into itself
			if ($nodes->target[0]->sequenceId > $nodes->subject->lft &&
				$nodes->target[0]->sequenceId <= $nodes->subject->rgt
			) {
				$query->select($this->_nbsLft.' + subquery.adjust - ?sourceMoved'
					.' - CASE WHEN '.$this->_nbsLft.' >= ?adjustedSequenceId THEN ?gap ELSE 0 END'
				);
				$query->select($this->_nbsRgt.' + subquery.adjust - ?sourceMoved'
					.' - CASE WHEN '.$this->_nbsRgt.' >= ?adjustedSequenceId THEN ?gap ELSE 0 END'
				);
				$query->bind('?adjustedSequenceId',
					$nodes->target[0]->sequenceId + $adjustedSourceRgt - $nodes->subject->rgt
				);
				$query->bind('?gap', $gap);
			} else {
				$query->select($this->_nbsLft.' + subquery.adjust - ?sourceMoved');
				$query->select($this->_nbsRgt.' + subquery.adjust - ?sourceMoved');
			}
		}
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if ($key != $this->_nbsKeyHL) {
				$query->select('parent.'.$key);
			}
		}
		$query->from($this->_nbsTable.' AS parent');
		$query->join('('.$selectJoin.') AS subquery');
		$query->where($this->_nbsLft.' BETWEEN ?adjustedSourceLft AND ?adjustedSourceRgt');
		$query->bind('?adjustedSourceLft', $adjustedSourceLft);
		$query->bind('?adjustedSourceRgt', $adjustedSourceRgt);
		$query->bind('?sourceMoved', $adjustedSourceLft - $nodes->subject->lft);
		$subSelect = $query->toString();

		// ...and insert it into the tree   TODO: Use JQuery completely when possible
		$insertFields = array($this->_nbsKeyHL, $this->_nbsLft, $this->_nbsRgt);
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if ($key != $this->_nbsKeyHL) {
				$insertFields[] = $key;
			}
		}
		$insertQuery = 'INSERT INTO '.$this->_nbsTable
			.' ('.implode(', ', $insertFields).') '.$subSelect
		;
		$this->_db->setQuery($insertQuery)->query();
		$newId = $this->_db->insertid();

		// Update the ref_id of the new branch(es), in case it is not a hardlinked tree
		if (!$hardlinked) {
			$query = new JQuery;
			$query->update($this->_nbsTable);
			$query->set($this->_nbsKeyHL, '(('.$this->_nbsKey.' - ?newId) MOD ?numAddedRows) + ?newId');
			$query->where($this->_nbsKeyHL.' = 0');
			$query->bind('?newId', $newId);
			$query->bind('?numAddedRows', $gap / 2);
			$this->_db->setQuery($query)->query();
		}

		// Finished the transaction, lets unlock the table
		$this->_db->unlockTables();

		return $newId;
	}

	/**
	 * Finds out the sequence Ids of the subject node and the target sequence Ids for any operation
	 *
	 * Converts the $subject and $target parameters to integer
	 *
	 * @access	protected
	 * @param	int		Id of the subject node
	 * @param	int		Id of the target node
	 * @param	int		Position of the sequence Ids relative to the target node
	 * @return	mixed	False on failure, an object consisting of the class members 'subject' and
	 *					'parent' (which is an array) otherwise
	 * @since	2.0
	 */
	protected function getSubjectAndTargetNodes(&$subject, &$target, $where)
	{
		if (!$subject = (int) $subject) {
			return false;
		}

		// Prepare result
		$result = new stdClass;

		// Fetch information about the subject node...
		$query = new JQuery;
		$query->select($this->_nbsLft.' AS lft');
		$query->select($this->_nbsRgt.' AS rgt');
		$query->from($this->_nbsTable);
		$query->where($this->_nbsKeyHL.' = ?subject');
		$query->order($this->_nbsLft);
		$query->bind('?subject', $subject);
		$this->_db->setQuery($query, 0, 1);

		$result->subject = $this->_db->loadObject();

		// Do we have a subject node?
		if (!is_object($result->subject)) {
			return false;
		}

		// ... and fetch information about the target node
		$result->target = $this->getTargetNodes($target, $where);

		// Do we have at least one target node?
		if ($result->target === false) {
			return false;
		}

		return $result;
	}

	/**
	 * Finds out the sequence Ids of the target node(s)
	 *
	 * Converts the $target parameter to integer
	 *
	 * @access	protected
	 * @param	int		Id of the parent node
	 * @param	int		Position of the sequence Id relative to the target node
	 * @return	mixed	False on failure, an array with the target sequence Ids otherwise
	 * @since	2.0
	 */
	protected function getTargetNodes(&$target, $where)
	{
		if (($target !== 0) && (!$target = (int) $target)) {
			return false;
		}

		// Fetch information about the target node(s)
		$query = new JQuery;
		$query->from($this->_nbsTable);
		if ($target) {
			// Find out the lft sequence Id _after/beneath_ which the tree is being moved/copied
			if ($where == self::BEFORE) {
				$query->select($this->_nbsLft.' AS sequenceId');
			} elseif ($where == self::FIRST_CHILD) {
				$query->select($this->_nbsLft.' + 1 AS sequenceId');
			} elseif ($where == self::LAST_CHILD) {
				$query->select($this->_nbsRgt.' AS sequenceId');
			} else {
				$query->select($this->_nbsRgt.' + 1 AS sequenceId');
			}
			$query->where($this->_nbsKeyHL.' = ?target');
			$query->order($this->_nbsLft);
			$query->bind('?target', $target);
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
		} else {
			// Find the last node on the root level for inserting at the end, or 1 for the beginning
			if ($where == self::BEFORE || $where == self::FIRST_CHILD) {
				$result[0]->sequenceId = 1;
			} else {
				$query->select('MAX('.$this->_nbsRgt.') + 1');
				$this->_db->setQuery($query);
				$result[0]->sequenceId = $this->_db->loadResult();
			}
		}

		// Do we have at least one target node?
		if (count($result) < 1) {
			return false;
		}

		// Are we operating on an emtpy table?
		if (!$target && $result[0]->sequenceId === null) {
			$result[0]->sequenceId = 1;
		}

		/*
		 * Special handling for copying/moving a branch BEFORE or AFTER a node: the target must not
		 * be hardlinked (unless it is the child of another hardlinked node), since we would not
		 * know which one of the possible targets to choose
		 */
		if (($where == self::AFTER || $where == self::BEFORE) && count($result) > 1) {
			/*
			 * Find out the ref_id of the direct parent of each target node
			 */
			$query = new JQuery;
			$query->select('parent.'.$this->_nbsKeyHL.' AS ref_id');
			$query->select($this->_nbsTable.'.'.$this->_nbsLft.' AS child_lft');
			$query->from($this->_nbsTable);
			$query->join($this->_nbsTable.' AS parent');
			$query->where('parent.'.$this->_nbsLft.' < '.$this->_nbsTable.'.'.$this->_nbsLft);
			$query->where($this->_nbsTable.'.'.$this->_nbsLft.' < parent.'.$this->_nbsRgt);
			$query->where($this->_nbsTable.'.'.$this->_nbsKeyHL.' = ?target');
			$query->order('parent.'.$this->_nbsLft);
			$query->bind('?target', $target);
			$this->_db->setQuery($query);

			/*
			 * Since it is not possible to LIMIT and ORDER within each GROUP, this has to be done in
			 * PHP. This means that too many rows are returned, but only the last one is used for
			 * each target node
			 */
			$targetParents = $this->_db->loadAssocList('child_lft');
			$lastTargetParent = array_pop($targetParents);
			$refId = $lastTargetParent['ref_id'];
			foreach ($targetParents as $targetParent) {
				if ($targetParent['ref_id'] != $refId) {
					return false;
				}
			}
		}

		return $result;
	}
}
