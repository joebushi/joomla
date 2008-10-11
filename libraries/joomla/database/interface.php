<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Database
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

/**
 * Database connector Interface
 *
 * @abstract
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.6
 */
INTERFACE JDatabaseInterface
{

	/**
	 * Test to see if the MySQLi connector is available
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	public static function test();

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @access      public
	 * @return      boolean
	 * @since       1.5
	 */
	public function connected();

	/**
	 * Determines UTF support
	 *
	 * @abstract
	 * @access public
	 * @return boolean
	 * @since 1.5
	 */
	public function hasUTF();

	/**
	 * Custom settings for UTF support
	 *
	 * @abstract
	 * @access public
	 * @since 1.5
	 */
	public function setUTF();

	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 * @access	public
	 * @abstract
	 */
	public function getEscaped( $text, $extra = false );

	/**
	 * Execute the query
	 *
	 * @abstract
	 * @access public
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function query();

	/**
	 * Get the affected rows by the most recent query
	 *
	 * @abstract
	 * @access public
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	public function getAffectedRows();

	/**
	* Execute a batch query
	*
	* @abstract
	* @access public
	* @return mixed A database resource if successful, FALSE if not.
	*/
	public function queryBatch( $abort_on_error=true, $p_transaction_safe = false);

	/**
	 * Diagnostic function
	 *
	 * @abstract
	 * @access public
	 */
	public function explain();

	/**
	 * Get the number of rows returned by the most recent query
	 *
	 * @abstract
	 * @access public
	 * @param object Database resource
	 * @return int The number of rows
	 */
	public function getNumRows( $cur=null );

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @abstract
	 * @access public
	 * @return The value returned in the query or null if the query failed.
	 */
	public function loadResult();

	/**
	 * Load an array of single field results into an array
	 *
	 * @abstract
	 */
	public function loadResultArray($numinarray = 0);

	/**
	* Fetch a result row as an associative array
	*
	* @abstract
	*/
	public function loadAssoc();

	/**
	 * Load a associactive list of database rows
	 *
	 * @abstract
	 * @access public
	 * @param string The field name of a primary key
	 * @return array If key is empty as sequential list of returned records.
	 */
	public function loadAssocList( $key='' );

	/**
	 * This global function loads the first row of a query into an object
	 *
	 *
	 * @abstract
	 * @access public
	 * @param object
	 */
	public function loadObject( );

	/**
	* Load a list of database objects
	*
	* @abstract
	* @access public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.

	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*/
	public function loadObjectList( $key='' );

	/**
	 * Load the first row returned by the query
	 *
	 * @abstract
	 * @access public
	 * @return The first row of the query.
	 */
	public function loadRow();

	/**
	* Load a list of database rows (numeric column indexing)
	*
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*
	* @abstract
	* @access public
	* @param string The field name of a primary key
	* @return array
	*/
	public function loadRowList( $key='' );

	/**
	 * Inserts a row into a table based on an objects properties
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	public function insertObject( $table, &$object, $keyName = NULL );

	/**
	 * Update ab object in the database
	 *
	 * @abstract
	 * @access public
	 * @param string
	 * @param object
	 * @param string
	 * @param boolean
	 */
	public function updateObject( $table, &$object, $keyName, $updateNulls=true );

	/**
	 * Get the ID generated from the previous INSERT operation
	 *
	 * @abstract
	 * @access public
	 * @return mixed
	 */
	public function insertid();

	/**
	 * Get the database collation
	 *
	 * @abstract
	 * @access public
	 * @return string Collation in use
	 */
	public function getCollation();

	/**
	 * Get the version of the database connector
	 *
	 * @abstract
	 */
	public function getVersion();

	/**
	 * List tables in a database
	 *
	 * @abstract
	 * @access public
	 * @return array A list of all the tables in the database
	 */
	public function getTableList();

	/**
	 * Shows the CREATE TABLE statement that creates the given tables
	 *
	 * @abstract
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @return 	array A list the create SQL for the tables
	 */
	public function getTableCreate( $tables );

	/**
	 * Retrieves information about the given tables
	 *
	 * @abstract
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @param	boolean			Only return field types, default true
	 * @return	array An array of fields by table
	 */
	public function getTableFields( $tables, $typeonly = true );
}
