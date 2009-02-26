<?php
/**
 * @version		$Id: nbs.interface.php 494 2007-08-20 09:16:43Z friesengeist $
 * @package		Joomla.Framework
 * @subpackage	Database.Table
 * @license		GNU General Public License
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Node Based Scheme Interface
 *
 * @author		Louis Landry <louis.landry@joomla.org>
 * @author		Aaron Stone <aaron@serendipity.cx>
 * @author		Enno Klasing <friesengeist@googlemail.com>
 * @package		Joomla.Framework
 * @subpackage	Database.Table
 * @since		2.0
 */
interface JNBSTableInterface
{
	/**
	 * Use the previous sibling for a specified operation
	 */
	const BEFORE = 1;

	/**
	 * Use the next sibling for a specified operation
	 */
	const AFTER = 2;

	/**
	 * Use the first child node for a specified operation
	 */
	const FIRST_CHILD = 3;

	/**
	 * Use the last child node for a specified operation
	 */
	const LAST_CHILD = 4;

	/**
	 * Indicates that two nodes are not related
	 */
	const UNRELATED = 0x00;

	/**
	 * Indicates that one node is the parent of another node
	 */
	const PARENT = 0x01;

	/**
	 * Indicates that one node is the child of another node
	 */
	const CHILD = 0x02;

	/**
	 * Indicates that two nodes are the same
	 */
	const SAME = 0x03;

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
	 *								   'lft' and 'rgt' fields are not public, the key will be added
	 *								   automatically. Default: array() [*]
	 * @todo	Check if the params marked with [*] are really needed in the constructor. It should
	 *			be enough if each child class specifies them in their protected properties (or
	 *			sets them in their own constructor, like 'nbsFields').
	 * @since	2.0
	 */
	public function __construct(array $settings);

	/**
	 * Determines if the given node is a leaf node
	 *
	 * @access	public
	 * @param	int		Node Id number [optional]
	 * @return	boolean	True if the node is a leaf
	 * @since	2.0
	 */
	public function isLeafNode($node=null);

	/**
	 * Returns the path of nodes to a given node
	 *
	 * @access	public
	 * @param	int		Id of the node to get the path for [optional]
	 * @return	array	Node objects in path order
	 * @since	2.0
	 */
	public function getPath($node=null);

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
	public function getTree($maxDepth=0, $node=null, $depth='depth');

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
	public function getRelation($node2, $node1=null);

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
	public function insertNode($parent, $where=self::AFTER);

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
	public function deleteNode($removeChildren=false, $node=null);

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
	public function moveNode($parent, $where=self::AFTER, $node=null);

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
	public function copyTree($parent, $where=self::AFTER, $node=null);
}
