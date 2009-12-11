<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	Database
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Database connector class
 *
 * @abstract
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
abstract class JDatabase
{
	/**
	 * The name of the database driver.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The current SQL statement to execute.
	 *
	 * @var string
	 **/
	protected $_sql;

	/**
	 * The common database table prefix.
	 *
	 * @var string
	 */
	protected $_table_prefix;

	/**
	 * The database connection resource.
	 *
	 * @var resource
	 */
	protected $_resource;

	/**
	 * The database connection cursor from the last query.
	 *
	 * @var resource
	 */
	protected $_cursor;

	/**
	 * The database driver debugging level.
	 *
	 * @var integer
	 */
	protected $_debug = 0;

	/**
	 * The affected row limit for the current SQL statement.
	 *
	 * @var integer
	 */
	protected $_limit = 0;

	/**
	 * The affected row offset to apply for the current SQL statement.
	 *
	 * @var integer
	 */
	protected $_offset = 0;

	/**
	 * The number of SQL statements executed by the database driver.
	 *
	 * @var integer
	 */
	protected $_count = 0;

	/**
	 * The log of executed SQL statements by the database driver.
	 *
	 * @var array
	 */
	protected $_log = array();

	/**
	 * The null or zero representation of a timestamp for the database driver.  This
	 * should be defined in child classes to hold the appropriate value for the engine.
	 *
	 * @var string
	 */
	protected $_nullDate;

	/**
	 * The character(s) used to quote SQL statement names such as table names or field
	 * names, etc.  The child classes should define this as necessary.  If a single
	 * character string the same character is used for both sides of the quoted name,
	 * else the first character will be used for the opening quote and the second for
	 * the closing quote.
	 *
	 * @var string
	 */
	protected $_nameQuote;

	/**
	 * Does the database engine support UTF-8 character encoding.
	 *
	 * @var boolean
	 */
	protected $_utf = false;

	/**
	 * Joomla Database Connection Object Constructor.
	 *
	 * @param	array	Array of options used to configure the connection.
	 * @return	void
	 * @since	1.5
	 */
	protected function __construct($options)
	{
		$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : 'jos_';

		// Determine whether or not the database engine supports UTF-8 character encoding.
		$this->_utf = $this->hasUTF();

		// Set the character set for the connection if UTF-8 is supported.
		if ($this->_utf) {
			$this->setUTF();
		}

		// Initialize instance properties.
		$this->_table_prefix	= $prefix;
		$this->_count			= 0;
		$this->_log				= array();
	}

	/**
	 * Joomla Database Connection Object Destructor.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function __destruct() {}

	/**
	 * Magic method to provide method alias support for quote() and nameQuote().
	 *
	 * @param	string	The called method.
	 * @param	array	The array of arguments passed to the method.
	 * @return	mixed	The aliased method's return value or boolean false on failure.
	 * @since	1.6
	 */
	public function __call($method, $args)
	{
		if (empty($args)) {
			return;
		}

		switch ($method)
		{
			case 'q':
				return $this->quote($args[0], isset($args[1]) ? $args[1] : true);
				break;
			case 'nq':
				return $this->nameQuote($args[0]);
				break;
		}
	}

	/**
	 * Returns a reference to the global Database object, only creating it
	 * if it doesn't already exist.
	 *
	 * The 'driver' entry in the parameters array specifies the database driver
	 * to be used (defaults to 'mysql' if omitted). All other parameters are
	 * database driver dependent.
	 *
	 * @param	array	Configuration options for the database driver.
	 * @return	object	JDatabase driver object.
	 * @since	1.5
	*/
	public static function getInstance($name, $options = array())
	{
		// Initialize the static variable.
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Only create the instance if it doesn't already exist.
		if (empty($instances[$name]))
		{
			// Get the options for the database driver.
			$driver		= isset($options['driver']) ? $options['driver'] : 'mysql';
			$select		= isset($options['select']) ? $options['select'] : true;
			$database	= isset($options['database']) ? $options['database'] : null;

			// Sanitize the driver name and build the class name.
			$driver	= preg_replace('/[^A-Z0-9_\.-]/i', '', $driver);
			$class	= 'JDatabase'.$driver;

			// Attempt to load the class file.
			$path = dirname(__FILE__).'/database/'.$driver.'.php';
			if (file_exists($path)) {
				require_once $path;
			} else {
				jexit(JText::_('Unable to load the database driver: ').$driver);
			}

			// Attempt to instantiate the object.
			try {
				$instance = new $class($options);
			}
			catch (JException $e)
			{
				jexit(JText::_('Unable to connect to the database: ').$e->getMessage());
			}

			// Set the instance to the static array.
			$instances[$name] = $instance;
		}

		return $instances[$name];
	}

	/**
	 * Method to quote and optionally escape a string to database requirements
	 * for insertion into the database.
	 *
	 * @param	string	The string to quote.
	 * @param	boolean	True to escape the string, false to leave it unescaped.
	 * @return	string
	 * @since	1.0
	 */
	public function quote($text, $escaped = true)
	{
		return '\''.($escaped ? $this->getEscaped($text) : $text).'\'';
	}

	/**
	 * Wrap an SQL statement identifier name such as field, table or database names in
	 * quotes to prevent injection risks and reserved word conflicts.
	 *
	 * @param	string	The identifier name to wrap in quotes.
	 * @return	string	The quote wrapped name.
	 * @since	1.0
	 */
	public function nameQuote($name)
	{
		// Don't quote names with dot-notation.
		if (strpos($name, '.') !== false) {
			return $name;
		}
		else {
			$q = $this->_nameQuote;
			if (strlen($q) == 1) {
				return $q.$name.$q;
			} else {
				return $q{0}.$name.$q{1};
			}
		}
	}

	/**
	 * Sets the SQL statement string for later execution.
	 *
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @param	string	The SQL statement to set.
	 * @param	integer	The affected row offset to set.
	 * @param	integer	The maximum affected rows to set.
	 * @param	string	The common table prefix to be replaced.
	 * @return	void
	 * @since	1.0
	 */
	public function setQuery($sql, $offset = 0, $limit = 0, $prefix = '#__')
	{
		$this->_sql		= $this->_prepareQuery((string) $sql, $prefix);
		$this->_limit	= (int) $limit;
		$this->_offset	= (int) $offset;
	}

	/**
	 * Get the current SQL statement.
	 *
	 * @return	string	The current set SQL statement string.
	 * @since	1.0
	 */
	public function getQuery()
	{
		return $this->_sql;
	}

	/**
	 * Sets the database debugging level for the driver.
	 *
	 * @param	integer	0 for off and 1 for on.
	 * @return	integer	The old debugging level.
	 * @since	1.0
	 */
	public function setDebug($level)
	{
		$previous = $this->_debug;
		$this->_debug = (int) $level;
		return $previous;
	}

	/**
	 * Determine whether or not the database engine supports UTF-8 character encofing.
	 *
	 * @return	boolean	True if the database engine supports UTF-8 character encoding.
	 * @since	1.5
	 */
	public function getUTFSupport()
	{
		return $this->_utf;
	}

	/**
	 * Get the database driver SQL statement log.
	 *
	 * @return	array	SQL statements executed by the database driver.
	 * @since	1.0
	 */
	public function getLog()
	{
		return $this->_log;
	}

	/**
	 * Get the total number of SQL statements executed by the database driver.
	 *
	 * @return	integer
	 * @since	1.6
	 */
	public function getCount()
	{
		return $this->_count;
	}

	/**
	 * Get the common table prefix for the database driver.
	 *
	 * @return	string	The common database table prefix.
	 * @since	1.0
	 */
	public function getPrefix()
	{
		return $this->_table_prefix;
	}

	/**
	 * Get the null or zero representation of a timestamp for the database driver.
	 *
	 * @return	string	Null or zero representation of a timestamp.
	 * @since	1.0
	 */
	public function getNullDate()
	{
		return $this->_nullDate;
	}

	/**
	 * This method replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @param	string	The SQL statement to prepare.
	 * @param	string	The common table prefix.
	 * @return	string	The prepared SQL statement.
	 * @since	1.6
	 */
	protected function _prepareQuery($sql, $prefix = '#__')
	{
		// Initialize variables.
		$escaped = false;
		$startPos = 0;
		$quoteChar = '';
		$literal = '';

		$sql = trim($sql);
		$n = strlen($sql);

		while ($startPos < $n)
		{
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);
			if (($k !== false) && (($k < $j) || ($j === false))) {
				$quoteChar	= '"';
				$j			= $k;
			} else {
				$quoteChar	= "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace($prefix, $this->_table_prefix,substr($sql, $startPos, $j - $startPos));
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (true)
			{
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\')
				{
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j	= $k+1;
					continue;
				}
				break;
			}
			if ($k === false) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr($sql, $startPos, $n - $startPos);
		}
		return $literal;
	}

	/**
	 * Get the version of the database connector
	 *
	 * @abstract
	 */
	public function getVersion()
	{
		return 'Not available for this connector';
	}

	/**
	 * Test to see if the database driver is available.
	 *
	 * @return	boolean	True on success.
	 * @since	1.5
	 */
	public static function test()
	{

	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param	string	The string to be escaped.
	 * @param	boolean	Optional parameter to provide extra escaping.
	 * @return	string	The escaped string.
	 * @since	1.0
	 */
	abstract public function getEscaped($text, $extra = false);

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return	boolean	True if connected to the database engine.
	 * @since	1.5
	 */
	abstract public function connected();

	/**
	 * Determines if the database engine supports UTF-8 character encoding.
	 *
	 * @return	boolean	True if supported.
	 * @since	1.5
	 */
	abstract public function hasUTF();

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return	boolean	True on success.
	 * @since	1.5
	 */
	abstract public function setUTF();

	/**
	 * Execute the SQL statement.
	 *
	 * @throws	JException
	 * @return	mixed	A database cursor resource on success, boolean false on failure.
	 * @since	1.0
	 */
	abstract public function query();

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return	integer	The number of affected rows.
	 * @since	1.0.5
	 */
	abstract public function getAffectedRows();

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param	resource	An optional database cursor resource to extract the row count from.
	 * @return	integer		The number of returned rows.
	 * @since	1.0
	 */
	abstract public function getNumRows($cursor = null);

	/**
	 * Method to get the first field of the first row of the result set from
	 * the database query.
	 *
	 * @throws	JException
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadResult();

	/**
	 * Method to get an array of values from the <var>$offset</var> field
	 * in each row of the result set from the database query.
	 *
	 * @throws	JException
	 * @param	integer	The row offset to use to build the result array.
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadResultArray($offset = 0);

	/**
	 * Method to get the first row of the result set from the database query
	 * as an associative array of ['field_name' => 'row_value'].
	 *
	 * @throws	JException
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadAssoc();

	/**
	 * Method to get an array of the result set rows from the database query where each
	 * row is an associative array of ['field_name' => 'row_value'].  The array of rows
	 * can optionally be keyed by a field name, but defaults to a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be generally avoided.
	 *
	 * @throws	JException
	 * @param	string	The name of a field to key the result array on.
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadAssocList($key = null);

	/**
	 * Method to get the first row of the result set from the database query
	 * as an object.
	 *
	 * @throws	JException
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadObject();

	/**
	 * Method to get an array of the result set rows from the database query where each
	 * row is an object.  The array of objects can optionally be keyed by a field name, but
	 * defaults to a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be generally avoided.
	 *
	 * @throws	JException
	 * @param	string	The name of a field to key the result array on.
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadObjectList($key = null);

	/**
	 * Method to get the first row of the result set from the database query
	 * as an array.  Columns are indexed numerically so the first column in the
	 * result set would be accessible via <var>$row[0]</var>, etc.
	 *
	 * @throws	JException
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadRow();

	/**
	 * Method to get an array of the result set rows from the database query where each
	 * row is an array.  The array of objects can optionally be keyed by a field offset, but
	 * defaults to a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field can result in unwanted
	 * behavior and should be generally avoided.
	 *
	 * @throws	JException
	 * @param	string	The offset of a field to key the result array on.
	 * @return	mixed	The return value or null if the query failed.
	 * @since	1.0
	 */
	abstract public function loadRowList($offset = null);

	/**
	 * Inserts a row into a table based on an object's properties.
	 *
	 * @param	string	The name of the database table to insert into.
	 * @param	object	An object whose public properties match the table fields.
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	abstract public function insertObject($table, &$object, $pk = null);

	/**
	 * Updates a row in a table based on an object's properties.
	 *
	 * @param	string	The name of the database table to insert into.
	 * @param	object	An object whose public properties match the table fields.
	 * @param	string	The name of the primary key for the table.
	 * @param	boolean	True to update null fields or false to ignore them.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	abstract public function updateObject($table, &$object, $pk, $updateNulls=true);

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return	integer	The value of the auto-increment field from the last inserted row.
	 * @since	1.0
	 */
	abstract public function insertid();

	/**
	 * Method to get the database collation in use by sampling a text field of a
	 * table in the database.
	 *
	 * @return	mixed	The collation in use by the database or boolean false if not supported.
	 * @since	1.5
	 */
	abstract public function getCollation();

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @throws	JException
	 * @return	array	An array of all the tables in the database.
	 * @since	1.0
	 */
	abstract public function getTableList();

	/**
	 * Shows the CREATE TABLE statement that creates the given tables.
	 *
	 * @throws	JException
	 * @param 	mixed	A table name or a list of table names
	 * @return	array	A list the create SQL for the tables
	 * @since	1.0
	 */
	abstract public function getTableCreate($tables);

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @throws	JException
	 * @param 	mixed	A table name or a list of table names
	 * @param	boolean	True to only return field types.
	 * @return	array	An array of fields by table.
	 * @since	1.0
	 */
	abstract public function getTableFields($tables, $typeonly = true);

	/**
	 * Method to initialize a transaction.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	abstract public function startTransaction();

	/**
	 * Method to roll back a transaction.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	abstract public function rollbackTransaction();

	/**
	 * Method to commit a transaction.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	abstract public function commitTransaction();
}


class JDatabaseException extends Exception
{

}
