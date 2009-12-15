<?php
/**
 * @version     $Id$
 * @package     Joomla.Framework
 * @subpackage  Database
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die();

/**
 * Oracle database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.5
 */
class JDatabaseOracle extends JDatabase
{
	/**
	 * The name of the database driver
	 *
	 * @var string
	 */
	public $name = 'oracle';

	/**
	 *  The null or zero representation of a timestamp for MySQL.
	 *
	 * @var string
	 */
	protected $_nullDate = '0000-00-00 00:00:00';

	/**
	 * The character used to quote SQL statement names such as table names or
     * field names, etc.
	 *
	 * @var string
	 */
	protected $_nameQuote		= '"';

	/**
	 * The parsed query sql string
	 *
	 * This will actually be an Oracle statement identifier,
	 * not a normal string
	 *
	 * @var resource
	 */
	protected $_prepared			= '';

    /**
     * The variables to be bound by oci_bind_by_name
     *
     * @var array
     */
    protected $_bounded            = '';
    
    /**
     * The number of rows affected by the previous 
     * INSERT, UPDATE, REPLACE or DELETE query executed
     * @var int
     */
    protected $_affectedRows       = '';
    
    /**
     * The number of rows returned by the previous 
     * SELECT query executed
     * @var int
     */
    protected $_numRows       = '';
    
    /**
     * Returns the current dateformat
     * 
     * @var mixed
     */
    protected $_dateformat    = '';
    
    /**
     * Returns the current character set
     * 
     * @var mixed
     */
    protected $_charset       = '';
    
    /**
     * Is used to decide whether a result set
     * should generate lowercase field names
     * 
     * @var boolean
     */
    protected $_tolower = true;
    
    /**
     * Is used to decide whether a result set
     * should return the LOB values or the LOB objects
     */
    protected $_returnlobs = true;
    
    /**
     * Is used to decide whether queries should
     * be auto-committed or transactional
     */
    protected $_commitMode = null;
    
    /*
	 * Oracle database driver constructor
	 *
     * @see        JDatabase
     * @throws    JException
     * @param    array    Array of options used to configure the connection.
     * @return    void
	 * @since	1.5
	 */
	protected function __construct( $options )
	{
		$host		= isset($options['host'])	? $options['host']		: 'localhost';
		$user		= isset($options['user'])	? $options['user']		: '';
		$password	= isset($options['password'])	? $options['password']	: '';
		$database	= isset($options['database'])	? $options['database']	: '';
		$prefix		= isset($options['prefix'])	? $options['prefix']	: 'jos_';
		$select		= isset($options['select'])	? $options['select']	: true;
        $port       = isset($options['port'])    ? $options['port']      : '1521';
        $dateformat = isset($options['dateformat']) ? $options['dateformat'] : 'RRRR-MM-DD HH24:MI:SS';

		// perform a number of fatality checks, then return gracefully
		if (!self::test()) {
			throw new JException('The Oracle adapter "oracle" is not available.', 1);
		}

		// connect to the server
		if (!($this->_resource = @oci_connect($user, $password, "//$host:$port/$database"))) {
			throw new JException('Could not connect to Oracle.', 2);
		}

        /**
        * Sets the default dateformat for the session
        * If the next line isn't executed on construction
        * then dates will be returned in the default 
        * Oracle Date Format of: DD-MON-RR
        */        
        $this->setDateFormat($dateformat);
        
        // Sets the default COMMIT mode
        $this->setCommitMode(OCI_COMMIT_ON_SUCCESS);

		// finalize initialization
		parent::__construct($options);
	}

	/**
	 * Oracle database driver object destructor.  Tidy up any residual
     * database connection resources.
	 *
	 * @return void
	 * @since 1.5
	 */
	public function __destruct()
	{
		if (is_resource($this->_resource)) {
			$return = oci_close($this->_resource);
		}
	}

	/**
	 * Test to see if the Oracle connector is available
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	public static function test()
	{
		return (function_exists('oci_connect'));
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
		if(is_resource($this->_resource)) {
			//return mysql_ping($this->_resource);
			// TODO See if there is a more elegant way to achieve this with Oracle DB
			return true;
		}
		return false;
	}

	/**
	 * Determines UTF support. Oracle versions 9.2+ will
     * return true
	 *
	 * @access	public
	 * @return boolean True - UTF is supported
	 */
	public function hasUTF()
	{
		$verParts = explode( '.', $this->getVersion() );
		return ($verParts[0] > 9 || ($verParts[0] == 9 && $verParts[1] == 2) );
	}

	/**
	 * Custom settings for UTF support
	 *
	 * @access	public
	 */
	public function setUTF()
	{
		return $this->setCharset();
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
	// TODO Figure out how to do this for Oracle...does oci_bind_by_name do the same thing?
	public function getEscaped($text, $extra = false)
	{
		/*
		$result = mysql_real_escape_string( $text, $this->_resource );
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
		*/
        return $text;
	}

	/**
	 * Execute the SQL statement.
     *
     * @throws    JException
     * @return    mixed    A database cursor resource on success, boolean false on failure.
     * @since    1.0
	 */
	public function query()
	{
        // If the database is not connected, return false.
		if (!is_resource($this->_resource)) {
			return false;
		}
        // Append the limit and offset if set.
		if ($this->_limit > 0 || $this->_offset > 0) {
			$this->_sql = "SELECT joomla2.*
            FROM (
                SELECT ROWNUM AS joomla_db_rownum, joomla1.*
                FROM (
                    " . $this->_sql . "
                ) joomla1
            ) joomla2
            WHERE joomla2.joomla_db_rownum BETWEEN " . ($this->_offset+1) . " AND " . ($this->_offset+$this->_limit);
            $this->setQuery($this->_sql);
            $this->bindVars();            
		}
        // If debugging is enabled, log the SQL statement.
		if ($this->_debug) {
			$this->_ticker++;
			$this->_log[] = $this->_sql;
		}
        
		// Execute the SQL statement.
		$this->_cursor = oci_execute($this->_prepared, $this->_commitMode);
        
        // If an error occurred, throw an exception.
		if (!$this->_cursor)
		{
            $level = ($this->_debug ? E_ERROR : E_NOTICE);
            $error = oci_error($this->_prepared);
            throw new JException($error['message'], $error['code'], $level, $sql);
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_affectedRows = oci_num_rows($this->_prepared);
		return $this->_prepared;
	}

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @access public
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 * @param string The common table prefix
	 */
	public function setQuery($sql, $offset = 0, $limit = 0, $prefix='#__')
	{
		$this->_sql		= $this->replacePrefix($sql, $prefix);
		$this->_prepared= oci_parse($this->_resource, $this->_sql);
		$this->_limit	= (int) $limit;
		$this->_offset	= (int) $offset;
	}

    /**
     * Adds a variable array to the bounded associative array.
     *
     * This method adds a new value to the bounded associative array 
     * using the placeholder variable as the key.
     *
     * @access public
     * @param string The Oracle placeholder in your SQL query
     * @param string The PHP variable you want to bind the placeholder to
     */
    public function setVar($placeholder, &$var, $maxlength=-1, $type=SQLT_CHR)
    {
        $this->_bounded[$placeholder] = array($var, (int)$maxlength, (int)$type);
    }
    
    /**
     * Binds all variables in the bounded associative array
     *
     * This method uses oci_bind_by_name to bind all entries in the bounded associative array. 
     *
     * @access public
     * @return boolean
     */
    public function bindVars()
    {
        if ($this->_bounded)
        {
            foreach($this->_bounded as $placeholder => $params)
            {
                $variable =& $params[0];
                $maxlength = $params[1];
                $type = $params[2];
                if(!oci_bind_by_name($this->_prepared, $placeholder, $variable, $maxlength, $type))
                {
                    $error = oci_error($this->_prepared);
                    $this->_errorNum = $error['code'];
                    $this->_errorMsg = $error['message']." BINDVARS=$placeholder, $variable, $maxlength, $type";

                    if ($this->_debug) 
                    {
                        JError::raiseError(500, 'JDatabaseOracle::query: '.$this->_errorNum.' - '.$this->_errorMsg );
                    }
                    return false;        
                }
            }
        }
        
        // Reset the bounded variable for subsequent queries
        $this->_bounded = '';
        return true;
    }
    
    public function defineVar($placeholder, &$variable, $type=SQLT_CHR)
    {
        if(!oci_define_by_name($this->_prepared, $placeholder, $variable, $type))
        {
            $error = oci_error($this->_prepared);
            $this->_errorNum = $error['code'];
            $this->_errorMsg = $error['message']." DEFINEVAR=$placeholder, $variable, $type";

            if ($this->_debug) 
            {
                JError::raiseError(500, 'JDatabaseOracle::query: '.$this->_errorNum.' - '.$this->_errorMsg);
            }
            return false;        
        }    
        
        return true;
    }
    
    /**
    * Sets the Oracle Date Format for the session
    * Default date format for Oracle is = DD-MON-RR
    * The default date format for this driver is:
    * 'RRRR-MM-DD HH24:MI:SS' since it is the format
    * that matches the MySQL one used within most Joomla
    * tables.
    * 
    * @param mixed $dateformat
    */
    public function setDateFormat($dateformat='DD-MON-RR')
    {
        $this->setQuery("alter session set nls_date_format = '$dateformat'");
        if (!$this->query()) {
            return false;
        }
        $this->_dateformat = $dateformat;
        return true;
    }
    
    /**
    * Returns the current date format
    * This method should be useful in the case that
    * somebody actually wants to use a different
    * date format and needs to check what the current
    * one is to see if it needs to be changed.
    * 
    */
    public function getDateFormat()
    {
        /*
        $this->setQuery("select value from nls_database_parameters where parameter = 'NLS_DATE_FORMAT'");
        return $this->loadResult();
        */
        // Commented out the above since it will always return the default, 
        // rather than current date format.
        return $this->_dateformat;
    }
    
    /**
    * Sets the Oracle Charset for the session.
    * As far as I've read, the character set cannot 
    * be changed in the middle of a session.
    * 
    * Please refer to:
    * http://forums.oracle.com/forums/thread.jspa?messageID=3259228
    * 
    * @param mixed $dateformat
    */
    public function setCharset($charset='AL32UTF8')
    {
        return false;
    }
    
    /**
    * Returns the current character set
    * This method should be useful in the case that
    * somebody actually wants to use a different
    * character set and needs to check what the current
    * one is to see if it needs to be changed.
    * 
    */
    public function getCharset()
    {
        $this->setQuery("select value from nls_database_parameters where parameter = 'NLS_CHARACTERSET'");
        return $this->loadResult();
    }
    
    /**
    * Creates a new descriptor object for use in setVar, setDefine
    * above.
    * 
    * @param mixed $type
    * @return OCI-Lob
    */
    public function createDescriptor($type)
    {
        if ($type == OCI_D_FILE || $type == OCI_D_LOB || $type == OCI_D_ROWID)
        {
            return oci_new_descriptor($this->_resource, $type);
        }
        return false;
    }

	/**
	 * Get the active query
	 *
	 * @access public
	 * @return string The current value of the internal SQL variable
	 */
	public function getPreparedQuery()
	{
		return $this->_prepared;
	}
    
    /**
     * Get the bounded associative array
     *
     * @access public
     * @return string The current value of the internal SQL variable
     */
    public function getBindVars()
    {
        return $this->_bounded;
    }

	/**
	 * Gets the number of affected rows from
     * the previous INSERT, UPDATE, DELETE, etc.
     * operation.
	 *
	 * @access	public
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	public function getAffectedRows()
	{
		return $this->_affectedRows;
	}

	/**
	 * Execute a batch query. For Oracle support
     * has not been added for batch queries that 
     * also require parameters to be bound.
	 *
	 * @access	public
	 * @return  boolean TRUE if successful, FALSE if not.
	 */
	public function queryBatch($abort_on_error = true, $p_transaction_safe = false)
	{
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$this->_sql = rtrim($this->_sql, '; \t\r\n\0');
			$si = $this->getVersion();
			preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
			if ($m[1] >= 4) {
				$this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 19) {
				$this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 17) {
				$this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
			}
		}
		$query_split = $this->splitSql($this->_sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			if ($command_line != '') {
                $this->setQuery($command_line);
                $this->query();
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= oci_error( $this->_resource ) . ' ';
					$this->_errorMsg .= " SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Diagnostic function.
     * Checks USER_TABLES first to see if the
     * user already has a table named PLAN_TABLES
     * created. If not, it is created and then
     * the EXPLAIN query is run and the results
     * retrieved from PLAN_TABLE and then deleted.
	 *
	 * @access	public
	 * @return	string
	 */
	public function explain()
	{
		$temp = $this->_sql;
        
        $this->setQuery("SELECT TABLE_NAME
                         FROM USER_TABLES
                         WHERE USER_TABLES.TABLE_NAME = 'PLAN_TABLE'");
        
        // If result then that means the plan_table exists
        $result = $this->loadResult();
        
        if (!$result)
        {
            $this->setQuery('CREATE TABLE "PLAN_TABLE" (
                                          "STATEMENT_ID"  VARCHAR2(30),
                                          "TIMESTAMP"  DATE,
                                          "REMARKS"  VARCHAR2(80),
                                          "OPERATION"  VARCHAR2(30),
                                          "OPTIONS"  VARCHAR2(30),
                                          "OBJECT_NODE"  VARCHAR2(128),
                                          "OBJECT_OWNER"  VARCHAR2(30),
                                          "OBJECT_NAME"  VARCHAR2(30),
                                          "OBJECT_INSTANCE"  NUMBER(22),
                                          "OBJECT_TYPE"  VARCHAR2(30),
                                          "OPTIMIZER"  VARCHAR2(255),
                                          "SEARCH_COLUMNS"  NUMBER(22),
                                          "ID"  NUMBER(22),
                                          "PARENT_ID"  NUMBER(22),
                                          "POSITION"  NUMBER(22),
                                          "COST"  NUMBER(22),
                                          "CARDINALITY"  NUMBER(22),
                                          "BYTES"  NUMBER(22),
                                          "OTHER_TAG"  VARCHAR2(255),
                                          "OTHER"  LONG)'
                           );
            if (!($cur = $this->query())) {
                return null;
            }
        }
        
        
		$this->_sql = "EXPLAIN PLAN FOR $temp";
        $this->setQuery($this->_sql);
        
        // This will add the results of the EXPLAIN PLAN
        // into the PLAN_TABLE
		if (!($cur = $this->query())) {
			return null;
		}
        
		$first = true;

		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">'.$this->getQuery().'</td></tr>';
        
        // SELECT rows that were just added to the PLAN_TABLE 
        $this->setQuery("SELECT * FROM PLAN_TABLE");
        if (!($cur = $this->query())) {
            return null;
        }
        
		while ($row = oci_fetch_assoc( $cur )) {
			if ($first) {
				$buffer .= '<tr>';
				foreach ($row as $k=>$v) {
                    if ($k == 'STATEMENT_ID' || $k == 'REMARKS' || $k == 'OTHER_TAG' || $k == 'OTHER') {
                        continue;
                    }
					$buffer .= '<th>'.$k.'</th>';
				}
				$buffer .= '</tr>';
				$first = false;
			}
			$buffer .= '</thead><tbody><tr>';
			foreach ($row as $k=>$v) {
                if ($k == 'STATEMENT_ID' || $k == 'REMARKS' || $k == 'OTHER_TAG' || $k == 'OTHER') {
                    continue;
                }
				$buffer .= '<td>'.$v.'</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
        
        $this->setQuery("DELETE PLAN_TABLE");
        
        if (!($cur = $this->query())) {
            return null;
        }
		oci_free_statement( $cur );

		$this->_sql = $temp;
        $this->setQuery($this->_sql);

		return $buffer;
	}

	/**
	 * Description
	 *
	 * @access	public
	 * @return int The number of rows returned from the most recent query.
	 */
	// TODO Check validity of this method, I don't feel it is the correct way to do it
	public function getNumRows( $cur=null )
	{
		return $this->_numRows;
	}

	/**
	 * Method to get the first field of the first row of the result set from
     * the database query.
	 *
	 * @throws    JException
     * @return    mixed    The return value or null if the query failed.
	 */
	public function loadResult()
	{
		if (!($cur = $this->query())) {
			return null;
		}
        
        $mode = $this->getMode(true);
        
		$ret = null;
		if ($row = oci_fetch_array( $cur, $mode )) {
			$ret = $row[0];
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows( $this->_prepared );
		oci_free_statement( $cur );
		return $ret;
	}

	/**
	 * Method to get an array of values from the <var>$offset</var> field
     * in each row of the result set from the database query.
     *
     * @throws    JException
     * @param    integer    The row offset to use to build the result array.
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	 */
	public function loadResultArray($numinarray = 0)
	{
		if (!($cur = $this->query())) {
			return null;
		}
        
        $mode = $this->getMode(true);
        
		$array = array();
		while ($row = oci_fetch_array( $cur, $mode )) {
			$array[] = $row[$numinarray];
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows( $this->_prepared );
		oci_free_statement( $cur );
		return $array;
	}

	/**
	 * Method to get the first row of the result set from the database query
     * as an associative array of ['field_name' => 'row_value'].
     *
     * @throws    JException
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	*/
	public function loadAssoc()
	{
        $tolower = $this->_tolower;
		if (!($cur = $this->query())) {
			return null;
		}
        
        $mode = $this->getMode();
        
		$ret = null;
		if ($array = oci_fetch_array( $cur, $mode )) {
            if ($tolower) {
                foreach($array as $field => $value) {
                    $lowercase = strtolower($field);
                    $array[$lowercase] = $value;
                    unset($array[$field]);
                }
            }
            
			$ret = $array;
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows( $this->_prepared );
		oci_free_statement( $cur );
		return $ret;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each
     * row is an associative array of ['field_name' => 'row_value'].  The array of rows
     * can optionally be keyed by a field name, but defaults to a sequential numeric array.
     *
     * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
     * behavior and should be generally avoided.
     *
     * @throws    JException
     * @param    string    The name of a field to key the result array on.
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	*/
	public function loadAssocList($key='')
	{
        $tolower = $this->_tolower;
		if (!($cur = $this->query())) {
			return null;
		}
        
        $mode = $this->getMode();
        
		$array = array();
		while ($row = oci_fetch_array( $cur, $mode )) {
            
            if ($tolower) {
                foreach($row as $field => $value) {
                    $lowercase = strtolower($field);
                    $row[$lowercase] = $value;
                    unset($row[$field]);
                }
            }
            
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows( $this->_prepared );
		oci_free_statement( $cur );
		return $array;
	}

	/**
	 * Method to get the first row of the result set from the database query
     * as an object.
     *
     * @throws    JException
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	*/
	public function loadObject()
	{
        $tolower = $this->_tolower;
        $returnlobs = $this->_returnlobs;
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($object = oci_fetch_object($cur)) {
		    if ($returnlobs) {
                foreach($object as $field => $value) {
                    if (get_class($value) == 'OCI-Lob') {
                        $object->$field = $value->load();
                    }
                }
            }
            if ($tolower) {
                $obj = new stdClass();
                foreach($object as $field => $value) {
                    $lowercase = strtolower($field);
                    $obj->$lowercase = $value;
                    unset($object->$field);
                }
                unset($value);
                unset($object);
                $object = &$obj;
            }
        	$ret = $object;
		}
        
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
		oci_free_statement($cur);
		return $ret;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each
     * row is an object.  The array of objects can optionally be keyed by a field name, but
     * defaults to a sequential numeric array.
     *
     * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
     * behavior and should be generally avoided.
     *
     * @throws    JException
     * @param    string    The name of a field to key the result array on.
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	*/
	public function loadObjectList($key='')
	{
        $tolower = $this->_tolower;
        $returnlobs = $this->_returnlobs;
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = oci_fetch_object($cur)) {
                     
            if ($returnlobs) {
                foreach($row as $field => $value) {
                    if (get_class($value) == 'OCI-Lob') {
                        $row->$field = $value->load();
                    }
                }
            }
            
            if ($tolower) {
                $obj = new stdClass();
                foreach($row as $field => $value) {
                    $lowercase = strtolower($field);
                    $obj->$lowercase = $value;
                    unset($row->$field);
                }
                unset($value);
                unset($row);
            }
            
			if ($key) {
                if ($tolower) {
                    $lowercase = strtolower($key);
                    $array[$obj->$lowercase] = $obj;
                } else {
                    $array[$row->$key] = $row;
                }
				
			} else {
                if ($tolower) {
                    $array[] = $obj;
                } else {
                    $array[] = $row;
                }
			}
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
		oci_free_statement($cur);
		return $array;
	}

	/**
	 * Method to get the first row of the result set from the database query
     * as an array.  Columns are indexed numerically so the first column in the
     * result set would be accessible via <var>$row[0]</var>, etc.
     *
     * @throws    JException
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	 */
	public function loadRow()
	{
		if (!($cur = $this->query())) {
			return null;
		}
        
        $mode = $this->getMode(true);
        
		$ret = null;
		if ($row = oci_fetch_array($cur, $mode)) {
			$ret = $row;
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
		oci_free_statement($cur);
		return $ret;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each
     * row is an array.  The array of objects can optionally be keyed by a field offset, but
     * defaults to a sequential numeric array.
     *
     * NOTE: Chosing to key the result array by a non-unique field can result in unwanted
     * behavior and should be generally avoided.
     *
     * @throws    JException
     * @param    string    The offset of a field to key the result array on.
     * @return    mixed    The return value or null if the query failed.
     * @since    1.0
	*/
	public function loadRowList($key=null)
	{
		if (!($cur = $this->query())) {
			return null;
		}
        
        $mode = $this->getMode(true);
        
		$array = array();
		while ($row = oci_fetch_array($cur, $mode)) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
		oci_free_statement($cur);
		return $array;
	}
    
    /**
     * Load the next row returned by the query.
     *
     * @return    mixed    The result of the query as an array, false if there are no more rows, or null on an error.
     *
     * @since    1.6.0
     */
    public function loadNextRow()
    {
        static $cur;
       
        if (is_null($cur)) {
            if (!($cur = $this->query())) {
                return null;
            }    
        }
        
        $mode = $this->getMode(true);
        
        if ($row = oci_fetch_array($cur, $mode)) {
            return $row;
        }
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
        oci_free_statement($cur);
        $cur = null;

        return false;
    }
    
    /**
     * Load the next row returned by the query.
     *
     * @return    mixed    The result of the query as an array, false if there are no more rows, or null on an error.
     *
     * @since    1.6.0
     */
    public function loadNextAssoc()
    {
        static $cur;
       
        if (is_null($cur)) {
            if (!($cur = $this->query())) {
                return null;
            }    
        }
        
        $mode = $this->getMode();
        $tolower = $this->_tolower;
        
        if ($array = oci_fetch_array($cur, $mode)) {
            if ($tolower) {
                foreach($array as $field => $value) {
                    $lowercase = strtolower($field);
                    $array[$lowercase] = $value;
                    unset($array[$field]);
                }
            }
            return $array;
        }
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
        oci_free_statement($cur);
        $cur = null;

        return false;
    }

    /**
     * Load the next row returned by the query.
     *
     * @return    mixed    The result of the query as an object, false if there are no more rows, or null on an error.
     *
     * @since    1.6.0
     */
    public function loadNextObject()
    {
        static $cur;
        
        $tolower = $this->_tolower;
        $returnlobs = $this->_returnlobs;
        if (is_null($cur)) {
            if (!($cur = $this->query())) {
                return null;
            }    
        }

        if ($object = oci_fetch_object($cur)) {
            if ($returnlobs) {
                foreach($object as $field => $value) {
                    if (get_class($value) == 'OCI-Lob') {
                        $object->$field = $value->load();
                    }
                }
            }
            if ($tolower) {
                $obj = new stdClass();
                foreach($object as $field => $value) {
                    $lowercase = strtolower($field);
                    $obj->$lowercase = $value;
                    unset($object->$field);
                }
                unset($value);
                unset($object);
                $object = &$obj;
            }
            return $object;
        }
        
        //Updates the affectedRows variable with the number of rows returned by the query
        $this->_numRows = oci_num_rows($this->_prepared);
        oci_free_statement( $cur );
        $cur = null;

        return false;
    }

	/**
	 * Inserts a row into a table based on an object's properties.
     *
     * @param    string    The name of the database table to insert into.
     * @param    object    An object whose public properties match the table fields.
     * @param    string    The name of the primary key. If provided the object property is updated.
     * @return   boolean   True on success.
     * @since    1.0
	*/
	public function insertObject($table, &$object, $keyName = NULL)
	{
        // Setup the SQL statement.
		$statement = "INSERT INTO $table ( %s ) VALUES ( %s ) ";
        
        // Build the fields and values arrays.
		$fields = array();
        $values = array();
		foreach (get_object_vars($object) as $k => $v) 
        {
			// If the variable is internal or non-scalar or null ignore it.
            if (($k[0] == '_') || !is_scalar($v) || is_null($v)) {
                continue;
            }
            
			$fields[] = $k;
            
            if ( $k == $keyName ) { 
                $values[] = $this->nextinsertid($table);
            } else {
                $values[] = $this->Quote($v);
            }
		}
        
        // Inject fields and values then set the SQL statement.
		$this->setQuery(sprintf($statement, implode(",", $fields), implode( ",", $values)));
		// Execute the statement.
        try {
            $this->query();  
        } catch (JException $e) {
            return false;
        }
        
		return true;
	}

	/**
     * Updates a row in a table based on an object's properties.
     *
     * @param    string    The name of the database table to insert into.
     * @param    object    An object whose public properties match the table fields.
     * @param    string    The name of the primary key for the table.
     * @param    boolean    True to update null fields or false to ignore them.
     * @return   boolean    True on success.
     * @since    1.0
    */
	public function updateObject( $table, &$object, $pk, $updateNulls=true )
	{
        // Setup the SQL statement.
		$statement = "UPDATE $table SET %s WHERE %s";
        // Build the fields array.
        $fields = array();
		foreach (get_object_vars($object) as $k => $v)
		{
			// If the variable is internal or non-scalar ignore it.
            if (($k[0] == '_') || is_object($v) || is_array($v) || is_resource($v)) {
                continue;
            }
            
            // Use the primary key for the WHERE clause and do not update it.
            if ($k == $pk) {
                $where = $pk . '=' . (is_string($v)) ? $this->quote($v) : $v;
                continue;
            }
            
            // If the value is null check to see if we want to update nulls.
			if ($v === null)
			{
                // If we are updating nulls, set the value to NULL.
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
            // The value is non-null, add it to the array to be updated.
			} else {
				$val = (is_string($v)) ? $this->quote($v) : $v;
			}
            
			// Add the field to the array.
            $fields[] = $this->nameQuote($k).'='.$v;
		}
        // Inject fields and values then set the SQL statement.
		$this->setQuery(sprintf($statement, implode(",", $fields), $where));
        
        // Execute the statement.
        try {
            $this->query();
        } catch (JException $e) {
            return false;
        }
        
		return true;
	}

    /**
    * Returns the latest sequence value for
    * a table
    * 
    * @param mixed $tableName
    * @param mixed $primaryKey
    * @return string
    */
	public function insertid($tableName = null, $primaryKey = null)
	{
        if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_SEQ';
            return $this->lastSequenceId($sequenceName);
        }
        // No support for IDENTITY columns; return null
        return null;
	}
    
    /**
     * Return the most recent value from the specified sequence in the database.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function lastSequenceId($sequenceName)
    {
        $this->_sql = 'SELECT '.$sequenceName.'.CURRVAL FROM dual';
        $this->setQuery($this->_sql);
        $value = $this->loadResult();
        return $value;
    }
    
    /**
    * Returns the next sequence value for
    * a table
    * 
    * @param mixed $tableName
    * @param mixed $primaryKey
    * @return string
    */
    public function nextInsertId($tableName = null, $primaryKey = null)
    {
        if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_SEQ';
            return $this->nextSequenceId($sequenceName);
        }
        // No support for IDENTITY columns; return null
        return null;
    }
    
    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function nextSequenceId($sequenceName)
    {
        $this->_sql = 'SELECT '.$sequenceName.'.NEXTVAL FROM dual';
        $this->setQuery($this->_sql);
        $value = $this->loadResult();
        return $value;
    }
    
    /**
     * Method to initialize a transaction.
     *
     * @return    boolean    True on success.
     * @since    1.6
     */
    public function startTransaction()
    {
        $php_version_array = explode('.', phpversion());
        
        // If PHP version is equal to or greater than PHP 5.3.2 
        // than use the new constant
        if ($php_version_array[0] >= 5 && 
            $php_version_array[1] >= 3 && 
            $php_version_array[2] >= 2) {
            $this->setCommitMode(OCI_NO_AUTO_COMMIT);
        } else {
            $this->setCommitMode(OCI_DEFAULT);
        }
    }

    /**
     * Method to roll back a transaction.
     *
     * @return    boolean    True on success.
     * @since    1.6
     */
    public function rollbackTransaction()
    {
        return oci_rollback($this->_resource);
    }

    /**
     * Method to commit a transaction.
     *
     * @return    boolean    True on success.
     * @since    1.6
     */
    public function commitTransaction()
    {
        return oci_commit($this->_resource);
    }
    
	/**
	 * Method to get the database engine version number.
     *
     * @return    string    The version number.
     * @since    1.0
	 */
	public function getVersion()
	{       
        $this->setQuery("select value from nls_database_parameters where parameter = 'NLS_RDBMS_VERSION'");
        return $this->loadResult();
	}

	/**
	 * Assumes database collation in use by the value
     * of the NLS_CHARACTERSET parameter
	 *
	 * @access	public
	 * @return string Collation in use
	 */
	public function getCollation()
	{
		return $this->getCharset();
	}

	/**
	 * Gets list of all table_names
     * for current user
	 *
	 * @access	public
	 * @return array A list of all the tables in the database
	 */
	// TODO Check is this is valid for Oracle DB
	// Visit this link for later review http://forums.devshed.com/oracle-development-96/show-tables-in-oracle-135613.html
	public function getTableList()
	{
        $this->_sql = 'SELECT table_name FROM all_tables';
        $this->setQuery($this->_sql);
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
			$this->setQuery( "select dbms_metadata.get_ddl('TABLE', '".$tblval."') from dual");
			$statement = $this->loadResult();
			$result[$tblval] = $statement;
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
	public function getTableFields($tables, $typeonly = true)
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval)
		{
            $tblval = strtoupper($tblval);
			$this->setQuery( "SELECT *
                              FROM ALL_TAB_COLUMNS
                              WHERE table_name = '".$tblval."'");
			$fields = $this->loadObjectList('', false);
            
			if($typeonly)
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->COLUMN_NAME] = preg_replace("/[(0-9)]/",'', $field->DATA_TYPE );
				}
			}
			else
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->COLUMN_NAME] = $field;
				}
			}
		}

		return $result;
	}
    
    /**
    * Sets the $_tolower variable to true
    * so that field names will be created
    * using lowercase values.
    * 
    * @return void
    */
    public function toLower()
    {
        $this->_tolower = true;
    }
    
    /**
    * Sets the $_tolower variable to false
    * so that field names will be created
    * using uppercase values.
    * 
    * @return void
    */
    public function toUpper()
    {
        $this->_tolower = false;
    }
    
    /**
    * Sets the $_returnlobs variable to true
    * so that LOB object values will be 
    * returned rather than an OCI-Lob Object.
    * 
    * @return void
    */
    public function returnLobValues()
    {
        $this->_returnlobs = true;
    }
    
    /**
    * Sets the $_returnlobs variable to false
    * so that OCI-Lob Objects will be returned.
    * 
    * @return void
    */
    public function returnLobObjects()
    {
        $this->_returnlobs = false;
    }
    
    /**
    * Depending on the value for _returnlobs,
    * this method returns the proper constant
    * combinations to be passed to the oci* functions
    * 
    * @return int
    */
    public function getMode($numeric = false)
    {
        if ($numeric === false) {
            if ($this->_returnlobs) {
                $mode = OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS;
            }
            else {
                $mode = OCI_ASSOC+OCI_RETURN_NULLS;
            }    
        } else {
            if ($this->_returnlobs) {
                $mode = OCI_NUM+OCI_RETURN_NULLS+OCI_RETURN_LOBS;
            }
            else {
                $mode = OCI_NUM+OCI_RETURN_NULLS;
            }            
        }

        return $mode;
    }
    
    /**
    * Gets the commit mode that will be used for queries
    * 
    * @return int
    */
    public function getCommitMode()
    {
        return $this->_commitMode;
    }
    
    /**
    * Sets the commit mode to use for queries
    * 
    * @return void
    */
    public function setCommitMode($commit_mode)
    {
        $this->_commitMode = $commit_mode;
    }
}