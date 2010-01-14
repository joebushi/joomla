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
 * eAccelerator cache storage handler
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheStorageEaccelerator extends JCacheStorage
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
		$this->_setExpire($cache_id);
		$cache_content = eaccelerator_get($cache_id);
		if ($cache_content === null)
		{
			return false;
		}
		return $cache_content;
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
		$keys = eaccelerator_list_keys();

        $secret = $this->_hash;
        $data = array();		

		foreach ($keys as $key) {
			/* Trim leading ":" to work around list_keys namespace bug in eAcc. This will still work when bug is fixed */
			$name = ltrim($key['name'], ':');
			
			$namearr=explode('-',$name);
			
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
	 * Store the data to by id and group
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
		eaccelerator_put($cache_id.'-expire', time());
		return eaccelerator_put($cache_id, $data, $this->_lifetime);
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
		eaccelerator_rm($cache_id.'-expire');
		return eaccelerator_rm($cache_id);
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
		$keys = eaccelerator_list_keys();

        $secret = $this->_hash;
        foreach ($keys as $key) {
        /* Trim leading ":" to work around list_keys namespace bug in eAcc. This will still work when bug is fixed */
		$key['name'] = ltrim($key['name'], ':'); 
		
        if (strpos($key['name'], $secret.'-cache-'.$group.'-')===0 xor $mode != 'group')
					eaccelerator_rm($key['name']);
        }
		return true;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function gc()
	{
		return eaccelerator_gc();
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
		return (extension_loaded('eaccelerator') && function_exists('eaccelerator_get'));
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
		$expire		= eaccelerator_get($key.'-expire');

		// set prune period
		if ($expire + $lifetime < $this->_now ) {
			eaccelerator_rm($key);
			eaccelerator_rm($key.'-expire');
		} else {
			eaccelerator_put($key.'-expire', $this->_now );
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
