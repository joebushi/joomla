<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2010 Klas Berlič
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Memcache cache storage handler
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheStorageMemcache extends JCacheStorage
{
	/**
	 * Resource for the current memcached connection.
	 * @var resource
	 */
	 private $_db = null;

	/**
	 * Use persistent connections
	 * @var boolean
	 */
	var $_persistent = false;
	
	var $_compress = 0;
	/**
	 * Constructor
	 *
	 * @access protected
	 * @param array $options optional parameters
	 */
	function __construct($options = array())
	{
		/**if (!$this->test()) {
			return JError::raiseError(404, "THE_MEMCACHE_EXTENSION_IS_NOT_AVAILABLE");
		}*/
		parent::__construct($options);
		if (!isset($this->_db)) $this->getConnection();

	}

	/**
	 * return memcache connection object
	 *
	 * @static
	 * @access private
	 * @return object memcache connection object
	 */
	function getConnection() {
		
			$config = &JFactory::getConfig();
			$this->_persistent	= $config->getValue('config.memcache_persist', true);
			$this->_compress	= $config->getValue('config.memcache_compress', true);
			// This will be an array of loveliness
			// @todo: multiple servers
			//$servers	= (isset($params['servers'])) ? $params['servers'] : array();
			$server=array();
			$server['host'] = $config->getValue('config.memcache_server_host', 'localhost');
			$server['port'] = $config->getValue('config.memcache_server_port',11211);
			// Create the memcache connection
			$this->_db = new Memcache;
				$this->_db->addServer($server['host'], $server['port'], $this->_persistent);
				//$db->connect($server['host'], $server['port']) or die ("Could not connect");
			

			/**if(false === $this->_db->get($this->_hash.'init-time')) {

				$this->_db->set($this->_hash.'init-time', time(), 0, 0);
				$this->_db->set($this->_hash.'hits',   0, 0, 0);
				$this->_db->set($this->_hash.'misses', 0, 0, 0);
				$this->_db->set($this->_hash.'304s', 0, 0, 0);
				$this->_db->set($this->_hash.'count', 0, 0, 0);
				$this->_db->set($this->_hash.'count-gzip', 0, 0, 0);
			}*/
			// memcahed has no list keys, we do our own accounting, initalise key index
			if($this->_db->get($this->_hash.'-index') === false) {
				$empty = array();
				$this->_db->set($this->_hash.'-index', serialize($empty) , $this->_compress, 0);
			}
			
		return;
	}


	/**
	 * Get cached data from memcache by id and group
	 *
	 * @access	public
	 * @param	string	$id			The cache data id
	 * @param	string	$group		The cache data group
	 * @param	boolean	$checkTime	True to verify cache time expiration threshold
	 * @return	mixed	Boolean false on failure or a cached data string
	 * @since	1.5
	 */
	function get($id, $group, $checkTime)
	{
		$cache_id = $this->_getCacheId($id, $group);
		return unserialize($this->_db->get($cache_id));
	}

	 /**
	 * Get all cached data
	 *
	 *
	 * @access	public
	 * @return	array data
	 * @since	1.6
	 */
	function getAll()
	{
		$keys = unserialize($this->_db->get($this->_hash.'-index'));

        $secret = $this->_hash;
        $data = array();		
		if (!empty($keys)){
		foreach ($keys as $key) {
			if (empty($key)) continue;
			$namearr=explode('-',$key->name);
			
			if ($namearr !== false && $namearr[0]==$secret &&  $namearr[1]=='cache') {
			
			$group = $namearr[2];
			
			if (!isset($data[$group])) {
			$item = new CacheItem($group);
			} else {
			$item = $data[$group];
			}

			$item->updateSize($key->size/1024);
			
			$data[$group] = $item;
			
			}
		}
		}
	
					
		return $data;
	}
	
	/**
	 * Store the data to memcache by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @param	string	$data	The data to store in cache
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function store($id, $group, $data)
	{   $data = serialize($data);
		$cache_id = $this->_getCacheId($id, $group);
		$index = unserialize($this->_db->get($this->_hash.'-index'));
		if ($index === false) {$index = array();} else {$index = $index;}
		$tmparr = new stdClass;
		$tmparr->name = $cache_id;
		$tmparr->size = strlen($data);
		$index[] = $tmparr;
		$this->_db->replace($this->_hash.'-index', serialize($index) , 0, 0);
		$this->_db->set($cache_id, serialize($data), $this->_compress, $this->_lifetime);
		
		return;
	}

	/**
	 * Remove a cached data entry by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function remove($id, $group)
	{
		$cache_id = $this->_getCacheId($id, $group);
		
		$index = unserialize($this->_db->get($this->_hash.'-index'));
		if ($index === false) {$index = array();} else {$index = $index;}
		
		foreach ($index as $key=>$value){
		if ($value->name == $cache_id) unset ($index[$key]);
		break;
		}
		$this->_db->replace($this->_hash.'-index', serialize($index), 0, 0);
		
		return $this->_db->delete($cache_id);
	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * group mode		: cleans all cache in the group
	 * notgroup mode	: cleans all cache not in the group
	 *
	 * @access	public
	 * @param	string	$group	The cache data group
	 * @param	string	$mode	The mode for cleaning cache [group|notgroup]
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function clean($group, $mode)
	{
		$index = unserialize($this->_db->get($this->_hash.'-index'));
		if ($index === false) {$index = array();} else {$index = $index;}
		
		$secret = $this->_hash;
        foreach ($index as $key=>$value) {
		
        if (strpos($value->name, $secret.'-cache-'.$group.'-')===0 xor $mode != 'group')
					$this->_db->delete($value->name);
					unset ($index[$key]);
        }
        $this->_db->replace($this->_hash.'-index', serialize($index) , 0, 0);
		return true;
		
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function gc()
	{  //dummy, memcache has builtin garbage collector
		return true;
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function test()
	{
		return (extension_loaded('memcache') && class_exists('Memcache'));
	}

	/**
	 * Get a cache_id string from an id/group pair
	 *
	 * @access	private
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	string	The cache_id string
	 * @since	1.5
	 */
	function _getCacheId($id, $group)
	{	
		$name	= md5($this->_application.'-'.$id.'-'.$this->_language);
		return $this->_hash.'-cache-'.$group.'-'.$name;
	}
}
