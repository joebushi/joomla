<?php
/**
* @version		$Id$
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
 * Joomla! Cache type object
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheObject extends JCache
{


	/**
	 * Executes a cache call for a raw php variable
	 *
	 * @access	public
	 * @param	string	id to store cache as
	 * @param	string	group to limit cache to
	 * @return	mixed	cached data (or false if it doesn't exist)
	 * @since	1.6
	 */
	function get( $id, $group = null )
	{
		// Get the storage handler and get callback cache data by id and group
		$cached = false;
		$data = parent::get($id, $group);
		if ($data !== false) {
			$cached = @unserialize( $data );
			if($cached === false) {
				$cached = $data;
			}
		}
		return $cached;
	}

	/**
	 * Stores a raw php variable with the id and group
	 *
	 * @access	public
	 * @param	mixed 	Variable to store
	 * @param	string	id to store cache as
	 * @param	string	group to limit cache to
	 * @param	int	how long to cache the data for
	 * @since	1.6
	 */
	function store( $data, $id, $group = null, $ttl = 0 ) {
		if($ttl) {
			$this->setLifetime($ttl);
		}
		if(is_object($data) || is_array($data)) {
			$data = serialize($data);
		}
		return parent::store($data, $id, $group);
	}

}
