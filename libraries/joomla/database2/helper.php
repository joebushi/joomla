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
 * Database helper functions.
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.6
 */
class JDatabaseHelper
{
	/**
	 * Get the database connectors
	 *
	 * @return	array	An array of available database connectors.
	 * @since	1.6
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

			if (!class_exists($class)) {
				require_once dirname(__FILE__).DS.'database'.DS.$name.'.php';
			}

			if (call_user_func_array(array(trim($class), 'test'), null)) {
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	 * Splits a string of queries into an array of individual queries
	 *
	 * @param	string	The queries to split
	 * @return	array	queries
	 * @since	1.6
	 */
	public static function splitSql($queries)
	{
		$start = 0;
		$open = false;
		$open_char = '';
		$end = strlen($queries);
		$query_split = array();
		for ($i=0; $i<$end; $i++)
		{
			$current = substr($queries, $i, 1);
			if (($current == '"' || $current == '\''))
			{
				$n = 2;
				while(substr($queries,$i - $n + 1, 1) == '\\' && $n < $i)
				{
					$n ++;
				}

				if ($n % 2 == 0)
				{
					if ($open)
					{
						if ($current == $open_char) {
							$open = false;
							$open_char = '';
						}
					} else {
						$open = true;
						$open_char = $current;
					}
				}
			}
			if (($current == ';' && !$open) || $i == $end - 1) {
				$query_split[] = substr($queries, $start, ($i - $start + 1));
				$start = $i + 1;
			}
		}

		return $query_split;
	}

	/**
	 * Execute a batch query
	 *
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public static function queryBatch($abort_on_error=true, $p_transaction_safe = false)
	{
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$this->_sql = rtrim($this->_sql, "; \t\r\n\0");
			$si = $this->getVersion();
			preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", $si, $m);
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
			$command_line = trim($command_line);
			if ($command_line != '') {
				$this->_cursor = mysql_query($command_line, $this->_resource);
				if ($this->_debug) {
					$this->_ticker++;
					$this->_log[] = $command_line;
				}
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= mysql_errno($this->_resource) . ' ';
					$this->_errorMsg .= mysql_error($this->_resource)." SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}
}
