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

require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'databaseinterface.php');

/**
 * Database connector class
 *
 * @abstract
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
abstract class JDatabase extends JObject
{
	/**
	 * The database driver name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The query sql string
	 *
	 * @var string
	 **/
	protected $sql = '';

	/**
	 * The database error number
	 *
	 * @var int
	 **/
	protected $errorNum = 0;

	/**
	 * The database error message
	 *
	 * @var string
	 */
	protected $errorMsg = '';

	/**
	 * The prefix used on all database tables
	 *
	 * @var string
	 */
	protected $table_prefix = '';

	/**
	 * The connector resource
	 *
	 * @var resource
	 */
	protected $resource = null;

	/**
	 * The last query cursor
	 *
	 * @var resource
	 */
	protected $cursor = null;

	/**
	 * Debug option
	 *
	 * @var boolean
	 */
	protected $debug = 0;

	/**
	 * The limit for the query
	 *
	 * @var int
	 */
	protected $limit = 0;

	/**
	 * The for offset for the limit
	 *
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * The number of queries performed by the object instance
	 *
	 * @var int
	 */
	protected $ticker = 0;

	/**
	 * A log of queries
	 *
	 * @var array
	 */
	protected $log = null;

	/**
	 * The null/zero date string
	 *
	 * @var string
	 */
	protected $nullDate = null;

	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	protected $nameQuote = null;

	/**
	 * UTF-8 support
	 *
	 * @var boolean
	 * @since	1.5
	 */
	protected $utf = 0;

	/**
	 * The fields that are to be quote
	 *
	 * @var array
	 * @since	1.5
	 */
	protected $quoted = null;

	/**
	 *  Legacy compatibility
	 *
	 * @var bool
	 * @since	1.5
	 */
	protected $hasQuoted = null;

	/**
	* Database object constructor
	*
	* @access	public
	* @param	array	List of options used to configure the connection
	* @since	1.5
	*/
	protected function __construct( $options )
	{
		$prefix		= array_key_exists('prefix', $options)	? $options['prefix']	: 'jos_';

		// Determine utf-8 support
		$this->utf = $this->hasUTF();

		//Set charactersets (needed for MySQL 4.1.2+)
		if ($this->utf){
			$this->setUTF();
		}

		$this->table_prefix	= $prefix;
		$this->ticker			= 0;
		$this->errorNum		= 0;
		$this->log				= array();
		$this->quoted			= array();
		$this->hasQuoted		= false;
	}

	/**
	 * Returns a reference to the global Database object, only creating it
	 * if it doesn't already exist.
	 *
	 * The 'driver' entry in the parameters array specifies the database driver
	 * to be used (defaults to 'mysql' if omitted). All other parameters are
	 * database driver dependent.
	 *
	 * @param array Parameters to be passed to the database driver
	 * @return JDatabase A database object
	 * @since 1.5
	*/
	public static function &getInstance( $options = array() )
	{
		static $instances;

		if (!isset( $instances )) {
			$instances = array();
		}

		$signature = serialize( $options );

		if (empty($instances[$signature]))
		{
			$driver		= array_key_exists('driver', $options) 		? $options['driver']	: 'mysql';
			$select		= array_key_exists('select', $options)		? $options['select']	: true;
			$database	= array_key_exists('database', $options)	? $options['database']	: null;

			$driver = preg_replace('/[^A-Z0-9_\.-]/i', '', $driver);
			$path	= dirname(__FILE__).DS.'database'.DS.$driver.'.php';

			if (file_exists($path)) {
				require_once($path);
			} else {
				throw new JException('Unable To Load Database Driver', 500, E_ERROR, get_class($instance));
				JError::setErrorHandling(E_ERROR, 'die'); //force error type to die
				$error = JError::raiseError( 500, JTEXT::_('Unable to load Database Driver:') .$driver);
				return $error;
			}

			$adapter = 'JDatabase'.$driver;
			$instance = new $adapter($options);

			if ( $error = $instance->getErrorMsg() )
			{
				throw new JException('Unable to connect to the database', 500, E_ERROR, $error);
			}

			if(!$instance INSTANCEOF JDatabaseInterface) {
				throw new JException('Invalid Database Driver', 500, E_ERROR, get_class($instance));
			}

			$instances[$signature] = & $instance;
		}

		return $instances[$signature];
	}

	/**
	 * Get the database connectors
	 *
	 * @access public
	 * @return array An array of available session handlers
	 */
	public static function getConnectors()
	{
		jimport('joomla.filesystem.folder');
		$handlers = JFolder::files(dirname(__FILE__).DS.'database', '.php$');

		$names = array();
		foreach($handlers as $handler)
		{
			$name = substr($handler, 0, strrpos($handler, '.'));
			$class = 'JDatabase'.ucfirst($name);

			if(!class_exists($class)) {
				require_once(dirname(__FILE__).DS.'database'.DS.$name.'.php');
			}

			if(call_user_func_array( array( trim($class), 'test' ), null)) {
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	 * Adds a field or array of field names to the list that are to be quoted
	 *
	 * @access public
	 * @param mixed Field name or array of names
	 * @since 1.5
	 */
	public function addQuoted( $quoted )
	{
		if (is_string( $quoted )) {
			$this->quoted[] = $quoted;
		} else {
			$this->quoted = array_merge( $this->quoted, (array)$quoted );
		}
		$this->hasQuoted = true;
	}

	/**
	 * Splits a string of queries into an array of individual queries
	 *
	 * @access public
	 * @param string The queries to split
	 * @return array queries
	 */
	public function splitSql( $queries )
	{
		$start = 0;
		$open = false;
		$open_char = '';
		$end = strlen($queries);
		$query_split = array();
		for($i=0;$i<$end;$i++) {
			$current = substr($queries,$i,1);
			if(($current == '"' || $current == '\'')) {
				$n = 2;
				while(substr($queries,$i - $n + 1, 1) == '\\' && $n < $i) {
					$n ++;
				}
				if($n%2==0) {
					if ($open) {
						if($current == $open_char) {
							$open = false;
							$open_char = '';
						}
					} else {
						$open = true;
						$open_char = $current;
					}
				}
			}
			if(($current == ';' && !$open)|| $i == $end - 1) {
				$query_split[] = substr($queries, $start, ($i - $start + 1));
				$start = $i + 1;
			}
		}

		return $query_split;
	}



	/**
	 * Checks if field name needs to be quoted
	 *
	 * @access public
	 * @param string The field name
	 * @return bool
	 */
	public function isQuoted( $fieldName )
	{
		if ($this->hasQuoted) {
			return in_array( $fieldName, $this->quoted );
		} else {
			return true;
		}
	}

	/**
	 * Sets the debug level on or off
	 *
	 * @access public
	 * @param int 0 = off, 1 = on
	 */
	public function debug( $level ) {
		$this->debug = intval( $level );
	}

	/**
	 * Get the database UTF-8 support
	 *
	 * @access public
	 * @return boolean
	 * @since 1.5
	 */
	public function getUTFSupport() {
		return $this->utf;
	}

	/**
	 * Get the error number
	 *
	 * @access public
	 * @return int The error number for the most recent query
	 */
	public function getErrorNum() {
		return $this->errorNum;
	}


	/**
	 * Get the error message
	 *
	 * @access public
	 * @return string The error message for the most recent query
	 */
	public function getErrorMsg($escaped = false)
	{
		if($escaped) {
			return addslashes($this->errorMsg);
		} else {
			return $this->errorMsg;
		}
	}

	/**
	 * Get a database error log
	 *
	 * @access public
	 * @return array
	 */
	public function getLog( )
	{
		return $this->log;
	}

	/**
	 * Get the total number of queries made
	 *
	 * @access public
	 * @return array
	 */
	public function getTicker( )
	{
		return $this->ticker;
	}

	/**
	 * Quote an identifier name (field, table, etc)
	 *
	 * @access public
	 * @param string The name
	 * @return string The quoted name
	 */
	public function nameQuote( $s )
	{
		$q = $this->nameQuote;
		if (strlen( $q ) == 1) {
			return $q . $s . $q;
		} else {
			return $q{0} . $s . $q{1};
		}
	}
	/**
	 * Get the database table prefix
	 *
	 * @access public
	 * @return string The database prefix
	 */
	public function getPrefix()
	{
		return $this->table_prefix;
	}

	/**
	 * Get the database null date
	 *
	 * @access public
	 * @return string Quoted null/zero date string
	 */
	public function getNullDate()
	{
		return $this->nullDate;
	}

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>table_prefix</var> class variable.
	 *
	 * @access public
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 * @param string The common table prefix
	 */
	public function setQuery( $sql, $offset = 0, $limit = 0, $prefix='#__' )
	{
		$this->sql		= $this->replacePrefix( $sql, $prefix );
		$this->limit	= (int) $limit;
		$this->offset	= (int) $offset;
	}

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>table_prefix</var> class variable.
	 *
	 * @access public
	 * @param string The SQL query
	 * @param string The common table prefix
	 */
	public function replacePrefix( $sql, $prefix='#__' )
	{
		$sql = trim( $sql );

		$escaped = false;
		$quoteChar = '';

		$n = strlen( $sql );

		$startPos = 0;
		$literal = '';
		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos( $sql, "'", $startPos );
			$k = strpos( $sql, '"', $startPos );
			if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
				$quoteChar	= '"';
				$j			= $k;
			} else {
				$quoteChar	= "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace( $prefix, $this->table_prefix,substr( $sql, $startPos, $j - $startPos ) );
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (TRUE) {
				$k = strpos( $sql, $quoteChar, $j );
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j	= $k+1;
					continue;
				}
				break;
			}
			if ($k === FALSE) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr( $sql, $startPos, $k - $startPos + 1 );
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr( $sql, $startPos, $n - $startPos );
		}
		return $literal;
	}

	/**
	 * Get the active query
	 *
	 * @access public
	 * @return string The current value of the internal SQL vairable
	 */
	public function getQuery()
	{
		return $this->sql;
	}

	/**
	 * Print out an error statement
	 *
	 * @param boolean If TRUE, displays the last SQL statement sent to the database
	 * @return string A standised error message
	 */
	public function stderr( $showSQL = false )
	{
		if ( $this->errorNum != 0 ) {
			return "DB function failed with error number $this->errorNum"
			."<br /><font color=\"red\">$this->errorMsg</font>"
			.($showSQL ? "<br />SQL = <pre>$this->sql</pre>" : '');
		} else {
			return "DB function reports no errors";
		}
	}

	/**
	* Get a quoted database escaped string
	*
	* @param	string	A string
	* @param	boolean	Default true to escape string, false to leave the string unchanged
	* @return	string
	* @access public
	*/
	public function Quote( $text, $escaped = true )
	{
		return '\''.($escaped ? $this->getEscaped( $text ) : $text).'\'';
	}

	/**
	 * ADODB compatability function
	 *
	 * @access	public
	 * @param	string SQL
	 * @since	1.5
	 */
	public function GetCol( $query )
	{
		$this->setQuery( $query );
		return $this->loadResultArray();
	}

	/**
	 * ADODB compatability function
	 *
	 * @access	public
	 * @param	string SQL
	 * @return	object
	 * @since	1.5
	 */
	public function Execute( $query )
	{
		jimport( 'joomla.database.recordset' );

		$query = trim( $query );
		$this->setQuery( $query );
		if (eregi( '^select', $query )) {
			$result = $this->loadRowList();
			return new JRecordSet( $result );
		} else {
			$result = $this->query();
			if ($result === false) {
				return false;
			} else {
				return new JRecordSet( array() );
			}
		}
	}

	/**
	 * ADODB compatability function
	 *
	 * @access public
	 * @since 1.5
	 */
	public function SelectLimit( $query, $count, $offset=0 )
	{
		jimport( 'joomla.database.recordset' );

		$this->setQuery( $query, $offset, $count );
		$result = $this->loadRowList();
		return new JRecordSet( $result );
	}

	/**
	 * ADODB compatability function
	 *
	 * @access public
	 * @since 1.5
	 */
	public function PageExecute( $sql, $nrows, $page, $inputarr=false, $secs2cache=0 )
	{
		jimport( 'joomla.database.recordset' );

		$this->setQuery( $sql, $page*$nrows, $nrows );
		$result = $this->loadRowList();
		return new JRecordSet( $result );
	}
	/**
	 * ADODB compatability function
	 *
	 * @access public
	 * @param string SQL
	 * @return array
	 * @since 1.5
	 */
	public function GetRow( $query )
	{
		$this->setQuery( $query );
		$result = $this->loadRowList();
		return $result[0];
	}

	/**
	 * ADODB compatability function
	 *
	 * @access public
	 * @param string SQL
	 * @return mixed
	 * @since 1.5
	 */
	public function GetOne( $query )
	{
		$this->setQuery( $query );
		$result = $this->loadResult();
		return $result;
	}

	/**
	 * ADODB compatability function
	 *
	 * @since 1.5
	 */
	public function BeginTrans()
	{
	}

	/**
	 * ADODB compatability function
	 *
	 * @since 1.5
	 */
	public function RollbackTrans()
	{
	}

	/**
	 * ADODB compatability function
	 *
	 * @since 1.5
	 */
	public function CommitTrans()
	{
	}

	/**
	 * ADODB compatability function
	 *
	 * @since 1.5
	 */
	public function ErrorMsg()
	{
		return $this->getErrorMsg();
	}

	/**
	 * ADODB compatability function
	 *
	 * @since 1.5
	 */
	public function ErrorNo()
	{
		return $this->getErrorNum();
	}

	/**
	 * ADODB compatability function
	 *
	 * @since 1.5
	 */
	public function GenID( $foo1=null, $foo2=null )
	{
		return '0';
	}
}
