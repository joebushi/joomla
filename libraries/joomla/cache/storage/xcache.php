<?php
/**
 * @version		$id:$
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2010 Klas Berlič
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * XCache cache storage handler
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheStorageXCache extends JCacheStorage
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
	 * Get cached data by id and group
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

		//check if id exists
		if (!xcache_isset($cache_id)){
			return false;
		}

		return xcache_get($cache_id);
	}
	
	
	 /**
	 * Get all cached data
	 *
	 *  requires the php.ini setting xcache.admin.enable_auth = Off
	 *
	 * @access	public
	 * @return	array data
	 * @since	1.6
	 */
	function getAll()
	{	$allinfo = xcache_list(XC_TYPE_VAR, 0);
		$keys = $allinfo['cache_list'];


        $secret = $this->_hash;
        $data = array();		

		foreach ($keys as $key) {
		
			$namearr=explode('-',$key['name']);
			
			if ($namearr !== false && $namearr[0]==$secret &&  $namearr[1]=='cache') {
			
			$group = $namearr[2];
			
			if (!isset($data[$group])) {
			$item = new CacheItem($group);
			} else {
			$item = $data[$group];
			}

			$item->updateSize($key['size']/1024);
			
			$data[$group] = $item;
			
			}
		}
	
					
		return $data;
	}
	/**
	 * Store the data by id and group
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
		return xcache_set($cache_id, $data, $this->_lifetime);
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

		if (!xcache_isset($cache_id)){
			return true;
		}

		return xcache_unset($cache_id);
	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * requires the php.ini setting xcache.admin.enable_auth = Off
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
		$allinfo = xcache_list(XC_TYPE_VAR, 0);
		$keys = $allinfo['cache_list'];
		
        $secret = $this->_hash;
        foreach ($keys as $key) {
		
        if (strpos($key['name'], $secret.'-cache-'.$group.'-')===0 xor $mode != 'group')
					xcache_unset($key['name']);
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
		// dummy, xcache has builtin garbage collector, turn it on in php.ini by changing default xcache.gc_interval setting from 0 to 3600 (=1 hour)
		
		/**
		$now = time();

		$cachecount = xcache_count(XC_TYPE_VAR);

			for ($i = 0; $i < $cachecount; $i ++) {

				$allinfo  = xcache_list(XC_TYPE_VAR, $i);
				$keys = $allinfo ['cache_list'];

				foreach($keys as $key) {

					if(strstr($key['name'], $this->_hash)) {
						if(($key['ctime'] + $this->_lifetime ) < $this->_now) xcache_unset($key['name']);
					}
				}
			}

		 */
		
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
		return (extension_loaded('xcache'));
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
