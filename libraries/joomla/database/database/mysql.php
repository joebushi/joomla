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
 * MySQL database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
class JDatabaseMySQL EXTENDS JDatabase IMPLEMENTS JDatabaseInterface
{
	/**
	 * The database driver name
	 *
	 * @protected string
	 */
	protected $name			= 'mysql';

	/**
	 *  The null/zero date string
	 *
	 * @protected string
	 */
	protected $nullDate		= '0000-00-00 00:00:00';

	/**
	 * Quote for named objects
	 *
	 * @protected string
	 */
	protected $nameQuote		= '`';

	/**
	* Database object constructor.  This must be called from JDatabase::getInstance
	*
	* @access	protected
	* @param	array	List of options used to configure the connection
	* @since	1.5
	* @see		JDatabase
	*/
	protected function __construct( $options )
	{
		$host		= array_key_exists('host', $options)	? $options['host']		: 'localhost';
		$user		= array_key_exists('user', $options)	? $options['user']		: '';
		$password	= array_key_exists('password',$options)	? $options['password']	: '';
		$database	= array_key_exists('database',$options)	? $options['database']	: '';
		$prefix		= array_key_exists('prefix', $options)	? $options['prefix']	: 'jos_';
		$select		= array_key_exists('select', $options)	? $options['select']	: true;

		// perform a number of fatality checks, then return gracefully
		if (!function_exists( 'mysql_connect' )) {
			$this->errorNum = 1;
			$this->errorMsg = 'The MySQL adapter "mysql" is not available.';
			return;
		}

		// connect to the server
		if (!($this->resource = @mysql_connect( $host, $user, $password, true ))) {
			$this->errorNum = 2;
			$this->errorMsg = 'Could not connect to MySQL';
			return;
		}

		// finalize initialization
		parent::__construct($options);

		// select the database
		if ( $select ) {
			$this->select($database);
		}
	}

	/**
	 * Database object destructor
	 *
	 * @return boolean
	 * @since 1.5
	 */
	public function __destruct()
	{
		$return = false;
		if (is_resource($this->resource)) {
			$return = mysql_close($this->resource);
		}
		return $return;
	}

	/**
	 * Test to see if the MySQL connector is available
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	public static function test()
	{
		return (function_exists( 'mysql_connect' ));
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @access	public
	 * @return	boolean
	 * @since	1.5
	 */
	public function connected()
	{
		if(is_resource($this->resource)) {
			return mysql_ping($this->resource);
		}
		return false;
	}

	/**
	 * Select a database for use
	 *
	 * @access	public
	 * @param	string $database
	 * @return	boolean True if the database has been successfully selected
	 * @since	1.5
	 */
	public function select($database)
	{
		if ( ! $database )
		{
			return false;
		}

		if ( !mysql_select_db( $database, $this->resource )) {
			throw new JException('Could Not Select Databasae', 500, E_ERROR, array('message'=>$this->errorMsg, 'number'=>$this->errorNum));
		}

		// if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
		if ( strpos( $this->getVersion(), '5' ) === 0 ) {
			$this->setQuery( "SET sql_mode = 'MYSQL40'" );
			$this->query();
		}

		return true;
	}

	/**
	 * Determines UTF support
	 *
	 * @access	public
	 * @return boolean True - UTF is supported
	 */
	public function hasUTF()
	{
		$verParts = explode( '.', $this->getVersion() );
		return ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int)$verParts[2] >= 2));
	}

	/**
	 * Custom settings for UTF support
	 *
	 * @access	public
	 */
	public function setUTF()
	{
		mysql_query( "SET NAMES 'utf8'", $this->resource );
	}

	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 * @access	public
	 * @abstract
	 */
	public function getEscaped( $text, $extra = false )
	{
		$result = mysql_real_escape_string( $text, $this->resource );
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
	}

	/**
	 * Execute the query
	 *
	 * @access	public
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function query()
	{
		if (!is_resource($this->resource)) {
			throw new JException('Database Driver Not Connected', 500, E_ERROR);
		}

		if ($this->limit > 0 || $this->offset > 0) {
			$this->sql .= ' LIMIT '.$this->offset.', '.$this->limit;
		}
		if ($this->debug) {
			$this->ticker++;
			$this->log[] = $this->sql;
		}
		$this->errorNum = 0;
		$this->errorMsg = '';
		$this->cursor = mysql_query( $this->sql, $this->resource );

		if (!$this->cursor)
		{
			$this->errorNum = mysql_errno( $this->resource );
			$this->errorMsg = mysql_error( $this->resource )." SQL=$this->sql";
			throw new JException('Database Error', 500, E_ERROR, array('message'=>$this->errorMsg, 'number'=>$this->errorNum));
		}
		return $this->cursor;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	public function getAffectedRows()
	{
		return mysql_affected_rows( $this->resource );
	}

	/**
	 * Execute a batch query
	 *
	 * @access	public
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function queryBatch( $abort_on_error=true, $p_transaction_safe = false)
	{
		$this->errorNum = 0;
		$this->errorMsg = '';
		if ($p_transaction_safe) {
			$this->sql = rtrim($this->sql, '; \t\r\n\0');
			$si = $this->getVersion();
			preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
			if ($m[1] >= 4) {
				$this->sql = 'START TRANSACTION;' . $this->sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 19) {
				$this->sql = 'BEGIN WORK;' . $this->sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 17) {
				$this->sql = 'BEGIN;' . $this->sql . '; COMMIT;';
			}
		}
		$query_split = $this->splitSql($this->sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			if ($command_line != '') {
				$this->cursor = mysql_query( $command_line, $this->resource );
				if (!$this->cursor) {
					$error = 1;
					$this->errorNum .= mysql_errno( $this->resource ) . ' ';
					$this->errorMsg .= mysql_error( $this->resource )." SQL=$command_line <br />";
					if ($abort_on_error) {
						throw new JException('Database Error', 500, E_ERROR, array('message'=>$this->errorMsg, 'number'=>$this->errorNum));
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Diagnostic function
	 *
	 * @access	public
	 * @return	string
	 */
	public function explain()
	{
		$temp = $this->sql;
		$this->sql = "EXPLAIN {$this->sql}";

		$cur = $this->query();
		
		$first = true;

		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">'.$this->getQuery().'</td></tr>';
		while ($row = mysql_fetch_assoc( $cur )) {
			if ($first) {
				$buffer .= '<tr>';
				foreach ($row as $k=>$v) {
					$buffer .= '<th>'.$k.'</th>';
				}
				$buffer .= '</tr>';
				$first = false;
			}
			$buffer .= '</thead><tbody><tr>';
			foreach ($row as $k=>$v) {
				$buffer .= '<td>'.$v.'</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
		mysql_free_result( $cur );

		$this->sql = $temp;

		return $buffer;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return int The number of rows returned from the most recent query.
	 */
	public function getNumRows( $cur=null )
	{
		return mysql_num_rows( $cur ? $cur : $this->cursor );
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @access	public
	 * @return The value returned in the query or null if the query failed.
	 */
	public function loadResult()
	{
		$cur = $this->query();
		$ret = null;
		if ($row = mysql_fetch_row( $cur )) {
			$ret = $row[0];
		}
		mysql_free_result( $cur );
		return $ret;
	}

	/**
	 * Load an array of single field results into an array
	 *
	 * @access	public
	 */
	public function loadResultArray($numinarray = 0)
	{
		$cur = $this->query();
		$array = array();
		while ($row = mysql_fetch_row( $cur )) {
			$array[] = $row[$numinarray];
		}
		mysql_free_result( $cur );
		return $array;
	}

	/**
	* Fetch a result row as an associative array
	*
	* @access	public
	* @return array
	*/
	public function loadAssoc()
	{
		$cur = $this->query();

		$ret = array();
		if ($array = mysql_fetch_assoc( $cur )) {
			$ret = $array;
		}
		mysql_free_result( $cur );
		return $ret;
	}

	/**
	* Load a assoc list of database rows
	*
	* @access	public
	* @param string The field name of a primary key
	* @return array If <protected>key</protected> is empty as sequential list of returned records.
	*/
	public function loadAssocList( $key='' )
	{
		$cur = $this->query();

		$array = array();
		while ($row = mysql_fetch_assoc( $cur )) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result( $cur );
		return $array;
	}

	/**
	* This global function loads the first row of a query into an object
	*
	* @access	public
	* @return 	object
	*/
	public function loadObject( )
	{
		$cur = $this->query();
		$ret = new StdClass();
		if ($object = mysql_fetch_object( $cur )) {
			$ret = $object;
		}
		mysql_free_result( $cur );
		return $ret;
	}

	/**
	* Load a list of database objects
	*
	* If <protected>key</protected> is not empty then the returned array is indexed by the value
	* the database key.  Returns <protected>null</protected> if the query fails.
	*
	* @access	public
	* @param string The field name of a primary key
	* @return array If <protected>key</protected> is empty as sequential list of returned records.
	*/
	public function loadObjectList( $key='' )
	{
		$cur = $this->query();
		
		$array = array();
		while ($row = mysql_fetch_object( $cur )) {
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result( $cur );
		return $array;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return The first row of the query.
	 */
	public function loadRow()
	{
		$cur = $this->query();
		$ret = array();
		if ($row = mysql_fetch_row( $cur )) {
			$ret = $row;
		}
		mysql_free_result( $cur );
		return $ret;
	}

	/**
	* Load a list of database rows (numeric column indexing)
	*
	* @access public
	* @param string The field name of a primary key
	* @return array If <protected>key</protected> is empty as sequential list of returned records.
	* If <protected>key</protected> is not empty then the returned array is indexed by the value
	* the database key.  Returns <protected>null</protected> if the query fails.
	*/
	public function loadRowList( $key=null )
	{
		$cur = $this->query();
		$array = array();
		while ($row = mysql_fetch_row( $cur )) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result( $cur );
		return $array;
	}

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @access	public
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	public function insertObject( $table, &$object, $keyName = NULL )
	{
		$fmtsql = "INSERT INTO $table ( %s ) VALUES ( %s ) ";
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->nameQuote( $k );
			$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		$this->query();

		$id = $this->insertid();
		if ($keyName && $id) {
			$object->$keyName = $id;
		}
		return true;
	}

	/**
	 * Description
	 *
	 * @access public
	 * @param [type] $updateNulls
	 */
	public function updateObject( $table, &$object, $keyName, $updateNulls=true )
	{
		$fmtsql = "UPDATE $table SET %s WHERE %s";
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v)
		{
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === null)
			{
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
			}
			$tmp[] = $this->nameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}

	/**
	 * Description
	 *
	 * @access public
	 */
	public function insertid()
	{
		return mysql_insert_id( $this->resource );
	}

	/**
	 * Description
	 *
	 * @access public
	 */
	public function getVersion()
	{
		return mysql_get_server_info( $this->resource );
	}

	/**
	 * Assumes database collation in use by sampling one text field in one table
	 *
	 * @access	public
	 * @return string Collation in use
	 */
	public function getCollation ()
	{
		if ( $this->hasUTF() ) {
			$this->setQuery( 'SHOW FULL COLUMNS FROM #__content' );
			$array = $this->loadAssocList();
			return $array['4']['Collation'];
		} else {
			return "N/A (mySQL < 4.1.2)";
		}
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return array A list of all the tables in the database
	 */
	public function getTableList()
	{
		$this->setQuery( 'SHOW TABLES' );
		return $this->loadResultArray();
	}

	/**
	 * Shows the CREATE TABLE statement that creates the given tables
	 *
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @return 	array A list the create SQL for the tables
	 */
	public function getTableCreate( $tables )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
			$rows = $this->loadRowList();
			foreach ($rows as $row) {
				$result[$tblval] = $row[1];
			}
		}

		return $result;
	}

	/**
	 * Retrieves information about the given tables
	 *
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @param	boolean			Only return field types, default true
	 * @return	array An array of fields by table
	 */
	public function getTableFields( $tables, $typeonly = true )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval)
		{
			$this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
			$fields = $this->loadObjectList();

			if($typeonly)
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
				}
			}
			else
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = $field;
				}
			}
		}

		return $result;
	}
}
