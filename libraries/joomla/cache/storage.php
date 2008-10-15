<?php
/**
* @version		$Id:storage.php 6961 2007-03-15 16:06:53Z tcp $
* @package		Joomla.Framework
* @subpackage	Cache
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
 * Abstract cache storage handler
 *
 * @abstract
 * @author		Louis Landry <louis.landry@joomla.org>
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
abstract class JCacheStorage extends JObject
{
	protected $_application = null;
	protected $_language = 'en-GB';
	protected $_locking = true;
	protected $_lifetime = null;

	/**
	* Constructor
	*
	* @access protected
	* @param array $options optional parameters
	*/
	protected function __construct( $options = array() )
	{
		$this->_now			= time();
		$this->_setOptions($options);
	}

	protected function _setOptions($options) {
		$this->_application	= (isset($options['application'])) ? $options['application'] : $this->_application;
		$this->_language	= (isset($options['language'])) ? $options['language'] : $this->_language;
		$this->_locking		= (isset($options['locking'])) ? $options['locking'] : $this->_locking;
		$this->_lifetime	= (isset($options['lifetime'])) ? $options['lifetime'] : $this->_lifetime;
	}

	/**
	 * Returns a reference to a cache storage hanlder object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @param	string	$handler	The cache storage handler to instantiate
	 * @return	object	A JCacheStorageHandler object
	 * @since	1.5
	 */
	public static function &getInstance($handler = 'file', $options = array())
	{
		static $instances = array();

		$handler = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $handler));
		if (!isset($instances[$handler]))
		{
			$class   = 'JCacheStorage'.ucfirst($handler);
			if(!class_exists($class))
			{
				$path = dirname(__FILE__).DS.'storage'.DS.$handler.'.php';

				if (file_exists($path) ) {
					require_once($path);
				} else {
					throw new JException('Unable to load cache storage', 500, E_ERROR, $handler);
				}
			}

			$instances[$handler] = new $class($options);
			if(rand(0,100) === 1) {
				$instances[$handler]->gc();
			}
		}
		$instance = clone($instances[$handler]);
		$instance->_setOptions($options);
		return $instance;
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	abstract public static function test();

	/**
	 * Get cached data by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id			The cache data id
	 * @param	string	$group		The cache data group
	 * @param	boolean	$checkTime	True to verify cache time expiration threshold
	 * @return	mixed	Boolean false on failure or a cached data string
	 * @since	1.5
	 */
	abstract public function get($id, $group, $checkTime);

	/**
	 * Store the data to cache by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @param	string	$data	The data to store in cache
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	abstract public function store($id, $group, $data);

	/**
	 * Remove a cached data entry by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	abstract public function remove($id, $group);

	/**
	 * Clean cache for a group given a mode.
	 *
	 * group mode		: cleans all cache in the group
	 * notgroup mode	: cleans all cache not in the group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$group	The cache data group
	 * @param	string	$mode	The mode for cleaning cache [group|notgroup]
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	abstract public function clean($group, $mode);

	/**
	 * Garbage collect expired cache data
	 *
	 * @abstract
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	abstract public function gc();

	/**
	 * Get a cache_id string from an id/group pair
	 *
	 * @access	protected
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	string	The cache_id string
	 * @since	1.5
	 */
	protected function _getCacheId($id, $group)
	{
		$name	= md5($this->_application.'-'.$id.'-'.$this->_hash.'-'.$this->_language);
		return 'cache_'.$group.'-'.$name;
	}


}
