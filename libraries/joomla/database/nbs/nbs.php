<?php
/**
 * @version		$Id: nbs.php 554 2007-11-07 16:16:12Z friesengeist $
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
 * Node Based Scheme database table library
 *
 * Inspiration from http://dev.mysql.com/tech-resources/articles/hierarchical-data.html and
 * Joe Celko's "Trees and Hierarchies in SQL for Smarties"
 *
 * @abstract
 * @author		Louis Landry <louis.landry@joomla.org>
 * @author		Aaron Stone <aaron@serendipity.cx>
 * @author		Enno Klasing <friesengeist@googlemail.com>
 * @package		Joomla.Framework
 * @subpackage	Database.Table
 * @since		2.0
 */
abstract class JNBSTable extends JBaseTable implements JNBSTableInterface
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
		$this->_nbsObject->{$this->_nbsKey} = null;
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
			if ($key != $this->_nbsKey) {
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
		$node = $this->_nbsObject->{$this->_nbsKey};
		if (!$node = (int) $node) {
			return false;
		}

		// Construct query to update all fields on both tables in one single UPDATE statement
		$query = new JQuery;
		$query->update($this->_nbsTable.' AS nbs');
		$query->update($this->_tbl.' AS data');
		$query->where('nbs.'.$this->_nbsKey.' = ?node');
		$query->where('nbs.'.$this->_nbsDataKey.' = data.'.$this->_tbl_key);
		$query->bind('?node', $node);
		$updatedFields = false;

		// Update the fields of the NBS table
		foreach (get_object_vars($this->_nbsObject) as $key => $value) {
			// Don't update the primary key or the id of the referenced data entry
			if ($key == $this->_nbsKey || $key == $this->_nbsDataKey) {
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
		$query->where($this->_nbsKey.' = ?node');
		$query->bind('?node', $node);
		$this->_db->setQuery($query)->query();

		return (boolean) $this->_db->getNumRows();
	}

	/**
	 * Returns the path of nodes to a given node
	 *
	 * @access	public
	 * @param	int		Id of the node to get the path for [optional]
	 * @return	array	Node objects in path order
	 * @since	2.0
	 */
	public function getPath($node=null)
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
		$query->from($this->_nbsTable.' AS nodes');
		$query->join($this->_nbsTable.' AS parent');
		$query->where('nodes.'.$this->_nbsLft
			.' BETWEEN parent.'.$this->_nbsLft.' AND parent.'.$this->_nbsRgt
		);
		$query->where('nodes.'.$this->_nbsKey.' = ?node');
		$query->order('parent.'.$this->_nbsLft);
		$query->bind('?node', $node);
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
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
			$lft->where($this->_nbsKey.' = ?node');
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
		$query->where('node1.'.$this->_nbsKey.' = ?node1');
		$query->where('node2.'.$this->_nbsKey.' = ?node2');
		$query->bind('?node1', $node1);
		$query->bind('?node2', $node2);
		$this->_db->setQuery($query);

		$relation = $this->_db->loadObject();

		// Do we have a result? Otherwise, at least one of the nodes does not exist
		if (!is_object($relation)) {
			return false;
		}

		// Return the correct relationship
		return ($relation->child * self::CHILD) + ($relation->parent * self::PARENT);
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

		// Lock the table so that nothing gets messed with while we insert our node
		$this->_db->lockTables(array($this->_nbsTable, $this->_tbl), true);

		// Fetch information about the parent node
		$parents = $this->getTargetNodes($parent, $where);

		// Do we have a parent node?
		if ($parents === false) {
			$this->_db->unlockTables();
			return false;
		}

		// Update the left and right values of all following nodes
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft.' + CASE WHEN '.$this->_nbsLft.' >= ?insert THEN 2 ELSE 0 END');
		$query->set($this->_nbsRgt, $this->_nbsRgt.' + 2');
		$query->where($this->_nbsRgt.' >= ?insert');
		$query->bind('?insert', $parents[0]->sequenceId);
		$this->_db->setQuery($query)->query();

		// Insert the data object
		$this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

		// Insert the NBS object
		$this->_nbsObject->{$this->_nbsKey} = 0;
		$this->_nbsObject->{$this->_nbsDataKey} = $this->{$this->_tbl_key};
		$this->_nbsObject->{$this->_nbsLft} = $parents[0]->sequenceId;
		$this->_nbsObject->{$this->_nbsRgt} = $parents[0]->sequenceId + 1;
		$this->_db->insertObject($this->_nbsTable, $this->_nbsObject, $this->_nbsKey);
		unset($this->_nbsObject->{$this->_nbsLft});
		unset($this->_nbsObject->{$this->_nbsRgt});

		// Finished the transaction, lets unlock the table
		$this->_db->unlockTables();

		return $this->_nbsObject->{$this->_nbsKey};
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
	 * @return	boolean	True on success
	 * @since	2.0
	 */
	public function deleteNode($removeChildren=false, $node=null)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}
		if (!$node = (int) $node) {
			return false;
		}

		// Lock the table so that nothing gets messed with while we delete our node
		$this->_db->lockTables(array($this->_nbsTable, $this->_tbl), true);

		// Fetch information about the node which is about to be deleted
		$query = new JQuery;
		$query->select($this->_nbsLft.' AS lft');
		$query->select($this->_nbsRgt.' AS rgt');
		$query->from($this->_nbsTable);
		$query->where($this->_nbsKey.' = ?node');
		$query->bind('?node', $node);
		$this->_db->setQuery($query);

		$delNodes = $this->_db->loadObjectList();

		// Do we have a node to delete?
		if (count($delNodes) < 1) {
			$this->_db->unlockTables();
			return false;
		}

		// Delete the node (including children, if requested)
		$query = new JQuery;
		$query->delete($this->_nbsTable);
		$query->delete($this->_tbl);
		// TODO: Ugly, I know. Fix this when JQuery is more capable of multi-table deletes
		$query->using($this->_nbsTable.' LEFT JOIN '.$this->_tbl
			.' ON '.$this->_nbsTable.'.'.$this->_nbsDataKey.' = '.$this->_tbl.'.'.$this->_tbl_key
		);
		if ($removeChildren) {
			$query->where($this->_nbsLft.' BETWEEN ?lft AND ?rgt');
			$query->bind('?rgt', $delNodes[0]->rgt);
		} else {
			$query->where($this->_nbsLft.' = ?lft');
		}
		$query->bind('?lft', $delNodes[0]->lft);
		$this->_db->setQuery($query)->query();

		// Update the left and right values of all previous children and all following nodes
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft
			.' - CASE WHEN '.$this->_nbsLft.' > ?rgt THEN ?gap '
			.'WHEN '.$this->_nbsLft.' > ?lft THEN 1 ELSE 0 END'
		);
		$query->set($this->_nbsRgt, $this->_nbsRgt
			.' - CASE WHEN '.$this->_nbsRgt.' > ?rgt THEN ?gap ELSE 1 END'
		);
		$query->where($this->_nbsRgt.' > ?lft');
		$query->bind('?lft', $delNodes[0]->lft);
		$query->bind('?rgt', $delNodes[0]->rgt);
		$query->bind('?gap', $removeChildren ? ($delNodes[0]->rgt - $delNodes[0]->lft + 1) : 2);
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

		// Lock the table so that nothing gets messed with while we move our node
		$this->_db->lockTables($this->_nbsTable, true);

		// Fetch information about the subect and parent node
		$nodes = $this->getSubjectAndTargetNodes($node, $parent, $where);

		if ($nodes === false) {
			$this->_db->unlockTables();
			return false;
		}

		// Construct query to update the lft and rgt values of all affected nodes
		$query = new JQuery;

		// Is the tree being moved towards the end?
		if ($nodes->target[0]->sequenceId > $nodes->subject->rgt + 1) {
			$query->bind('?rangeStart', $nodes->subject->lft);
			$query->bind('?rangeEnd', $nodes->target[0]->sequenceId - 1);
			$query->bind('?restStart', $nodes->subject->rgt + 1);
			$query->bind('?restEnd', $nodes->target[0]->sequenceId - 1);
			$query->bind('?modifyTree', $nodes->target[0]->sequenceId - $nodes->subject->rgt - 1);
			$query->bind('?modifyRest', $nodes->subject->lft - $nodes->subject->rgt - 1);
		}

		// ... or is it being moved towards the beginning?
		elseif ($nodes->target[0]->sequenceId < $nodes->subject->lft) {
			$query->bind('?rangeStart', $nodes->target[0]->sequenceId);
			$query->bind('?rangeEnd', $nodes->subject->rgt);
			$query->bind('?restStart', $nodes->target[0]->sequenceId);
			$query->bind('?restEnd', $nodes->subject->lft - 1);
			$query->bind('?modifyTree', $nodes->target[0]->sequenceId - $nodes->subject->lft);
			$query->bind('?modifyRest', $nodes->subject->rgt - $nodes->subject->lft + 1);
		}

		// ... does it stay at the same position?
		elseif (($nodes->target[0]->sequenceId == $nodes->subject->lft) ||
			($nodes->target[0]->sequenceId == $nodes->subject->rgt + 1)
		) {
			$this->_db->unlockTables();
			return true;
		}

		// ... or perhaps being moved into itself? Stupid idea.
		else {
			$this->_db->unlockTables();
			return false;
		}

		// Assemble and execute the query
		$query->update($this->_nbsTable);
		$modifySequenceLft = 'CASE'
			.' WHEN '.$this->_nbsLft.' BETWEEN ?movedStart AND ?movedEnd'
			.' THEN ?modifyTree'
			.' WHEN '.$this->_nbsLft.' BETWEEN ?restStart AND ?restEnd'
			.' THEN ?modifyRest'
			.' ELSE 0 END'
		;
		$modifySequenceRgt = str_replace('lft', 'rgt', $modifySequenceLft);
		$query->set($this->_nbsLft, $this->_nbsLft.' + '.$modifySequenceLft);
		$query->set($this->_nbsRgt, $this->_nbsRgt.' + '.$modifySequenceRgt);
		$query->where($this->_nbsLft.' BETWEEN ?rangeStart AND ?rangeEnd'
			.' OR '.$this->_nbsRgt.' BETWEEN ?rangeStart AND ?rangeEnd'
		);
		$query->bind('?movedStart', $nodes->subject->lft);
		$query->bind('?movedEnd', $nodes->subject->rgt);
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
	 * @return	mixed	Id of the copied tree (int), or false on failure
	 * @since	2.0
	 */
	public function copyTree($parent, $where=self::AFTER, $node=null)
	{
		if ($node === null) {
			$node = $this->{$this->_tbl_key};
		}

		// Lock the table so that nothing gets messed with while we copy our tree
		$this->_db->lockTables(array($this->_nbsTable, $this->_nbsTable=>'parent'), true);

		// Fetch information about the subect and parent node
		$nodes = $this->getSubjectAndTargetNodes($node, $parent, $where);

		if ($nodes === false) {
			$this->_db->unlockTables();
			return false;
		}

		/*
		 * Collect some data about the branch which is being copied: the gap per copy, and the lft
		 * and rgt values which it will have after the gaps for the copies have been created
		 */
		$gap				= $nodes->subject->rgt - $nodes->subject->lft + 1;
		$adjust				= $nodes->target[0]->sequenceId - $nodes->subject->lft;
		$adjustedSourceLft	= $nodes->subject->lft;
		$adjustedSourceRgt	= $nodes->subject->rgt;

		/*
		 * Keep track of how the lft and rgt values of the source branch are being modified by
		 * inserting the gaps. After all, we need to be able to select the source branch lateron
		 */
		$adjustedSourceLft += ($nodes->subject->lft >= $nodes->target[0]->sequenceId) * $gap;
		$adjustedSourceRgt += ($nodes->subject->rgt >= $nodes->target[0]->sequenceId) * $gap;

		// Make room for the new subtree
		$query = new JQuery;
		$query->update($this->_nbsTable);
		$query->set($this->_nbsLft, $this->_nbsLft
			.' + CASE WHEN '.$this->_nbsLft.' >= ?gapStart THEN ?gap ELSE 0 END'
		);
		$query->set($this->_nbsRgt, $this->_nbsRgt.' + ?gap');
		$query->where($this->_nbsRgt.' >= ?gapStart');
		$query->bind('?gapStart', $nodes->target[0]->sequenceId);
		$query->bind('?gap', $gap);
		$this->_db->setQuery($query)->query();

		// Select the subtree...
		$query = new JQuery;
		$query->select($this->_nbsLft.' + ?adjust -'
			.' CASE WHEN '.$this->_nbsLft.' >= ?gapStart THEN ?gap ELSE 0 END'
		);
		$query->select($this->_nbsRgt.' + ?adjust -'
			.' CASE WHEN '.$this->_nbsRgt.' >= ?gapStart THEN ?gap ELSE 0 END'
		);
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if ($key != $this->_nbsKey) {
				$query->select('parent.'.$key);
			}
		}
		$query->from($this->_nbsTable.' AS parent');
		$query->where($this->_nbsLft.' BETWEEN ?adjustedSourceLft AND ?adjustedSourceRgt');
		$query->bind('?gapStart', $nodes->target[0]->sequenceId);
		$query->bind('?gap', $gap);
		$query->bind('?adjustedSourceLft', $adjustedSourceLft);
		$query->bind('?adjustedSourceRgt', $adjustedSourceRgt);
		$query->bind('?adjust', $adjust);
		$subSelect = $query->toString();

		// ...and insert it into the tree   TODO: Use JQuery completely when possible
		$insertFields = array($this->_nbsLft, $this->_nbsRgt);
		foreach(get_object_vars($this->_nbsObject) as $key => $value) {
			if ($key != $this->_nbsKey) {
				$insertFields[] = $key;
			}
		}
		$insertQuery = 'INSERT INTO '.$this->_nbsTable
			.' ('.implode(', ', $insertFields).') '.$subSelect
		;
		$this->_db->setQuery($insertQuery)->query();
		$newId = $this->_db->insertid();

		// Finished the transaction, lets unlock the table
		$this->_db->unlockTables();

		return $newId;
	}

	/**
	 * Finds out the sequence Ids of the subject node and the target sequence Id for any operation
	 *
	 * Converts the $subject and $target parameters to integer
	 *
	 * @access	protected
	 * @param	int		Id of the subject node
	 * @param	int		Id of the target node
	 * @param	int		Position of the sequence Id relative to the parent node
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
		$query->where($this->_nbsKey.' = ?subject');
		$query->bind('?subject', $subject);
		$this->_db->setQuery($query, 0, 1);

		$result->subject = $this->_db->loadObject();

		// Do we have a subject node?
		if (!is_object($result->subject)) {
			return false;
		}

		// ... and fetch information about the target node
		$result->target = $this->getTargetNodes($target, $where);

		// Do we have a target node?
		if ($result->target === false) {
			return false;
		}

		return $result;
	}

	/**
	 * Finds out the sequence Ids of the target node
	 *
	 * Converts the $parent parameter to integer
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

		// Fetch information about the target node
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
			$query->where($this->_nbsKey.' = ?target');
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

		// Do we have a target node?
		if (count($result) < 1) {
			return false;
		}

		// Are we operating on an emtpy table?
		if (!$target && $result[0]->sequenceId === null) {
			$result[0]->sequenceId = 1;
		}

		return $result;
	}
}
