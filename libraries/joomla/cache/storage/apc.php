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
 * APC cache storage handler
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheStorageApc extends JCacheStorage
{
	/**
	 * Constructor
	 *
	 * @access protected
	 * @param array $options optional parameters
	 */
	function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Get cached data from APC by id and group
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
		$this->_setExpire($cache_id);
		return apc_fetch($cache_id);
	}
	
	 /**
	 * Get all cached data
	 *
	 *
	 * @access	public
	 * @return	array data
	 * @since	1.5
	 */
	function getAll()
	{
		$allinfo = apc_cache_info('user');
		
		$keys = $allinfo['cache_list'];
		
		print_r($keys);
		
        $secret = $this->_hash;
        $data = array();		

		foreach ($keys as $key) {
			
			$name=$key['info'];
			$namearr=explode('-',$name);
			
			if ($namearr !== false && $namearr[0]==$secret &&  $namearr[1]=='cache') {
			
			$group = $namearr[2];
			
			if (!isset($data[$group])) {
			$item = new CacheItem($group);
			} else {
			$item = $data[$group];
			}

			$item->updateSize($key['mem_size']/1024);
			
			$data[$group] = $item;
			
			}
		}
	
					
		return $data;
	}
	
	
	
	
	/**
	 * Store the data to APC by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @param	string	$data	The data to store in cache
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function store($id, $group, $data)
	{
		$cache_id = $this->_getCacheId($id, $group);
		apc_store($cache_id.'-expire', time());
		return apc_store($cache_id, $data, $this->_lifetime);
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
		apc_delete($cache_id.'-expire');
		return apc_delete($cache_id);
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
		$allinfo = apc_cache_info('user');
		$keys = $allinfo['cache_list'];
		
        $secret = $this->_hash;
        foreach ($keys as $key) {
		
        if (strpos($key['info'], $secret.'-cache-'.$group.'-')===0 xor $mode != 'group')
					apc_delete($key['info']);
        }
		return true;
	}
	
	/**
	 * Garbage collect expired cache data
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 * * @since	1.6
	 */
	function gc()
	{
		// dummy, apc has builtin garbage collector controled by apc.gc_ttl
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
		return extension_loaded('apc');
	}

	/**
	 * Set expire time on each call since memcache sets it on cache creation.
	 *
	 * @access private
	 *
	 * @param string  $key   Cache key to expire.
	 * @param integer $lifetime  Lifetime of the data in seconds.
	 */
	function _setExpire($key)
	{
		$lifetime	= $this->_lifetime;
		$expire		= apc_fetch($key.'-expire');

		// set prune period
		if ($expire + $lifetime < time()) {
			apc_delete($key);
			apc_delete($key.'-expire');
		} else {
			apc_store($key.'-expire',  time());
		}
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
