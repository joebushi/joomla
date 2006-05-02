<?php
/**
* @version $Id: admin.cachemanager.php,v 1.1 2005/08/25 14:14:12 johanjanssens Exp $
* @package Mambo
* @subpackage Cache Manager
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class comCacheManagerData {
	
	/**
	 * @var String
	 */
	var $dirMarker = '..';
	/**
	 * @var String
	 */
	var $indexMarker = 'index.html';
	
	/**
	 * An Array of Class CacheManagerItem
	 * Index by Cache Group ID
	 *
	 * @var String
	 */
	var $items = array();
	
	/**
	 * Parse $path for cache file groups.
	 * Any files identifided as cache are logged 
	 * in a group and stored in $this->items.
	 *
	 * @param String $path
	 */
	function addPath( $path ){
		if ($handle = opendir( $path )) {
			while (false !== ($file = readdir($handle))) {
				if( is_dir( $path."/".$file ) && $file != "." && $file != ".." ){
					$this->addPath( $path."/".$file );
				}elseif( is_file( $path."/".$file ) ){
					$filename = basename( $path."/".$file );
					$group = null;
					
					// Get cache group name from file
					if( substr( $filename, 0, 6 ) == "cache_" )
					{
						$group = $this->_getCacheGroupName( $filename );
					}
					else if( $filename != $this->indexMarker )
					{ // If group is null use the directory name as the group
						$array = explode( '/', $path );
						$group = $this->dirMarker.$array[count( $array ) -1];
						// Do not create a group if the the folders are for development
						if( $group == $this->dirMarker.'cache' || $group == $this->dirMarker.'CVS' ) $group = null;
					}
						
					if($group){
						if(!isset( $this->items[$group]) ){
							$this->items[$group] = new CacheManagerItem( $group );
						}
						$this->items[$group]->updateSize( filesize( $path."/".$file )/ 1024 );
					}
				}
			}
			closedir($handle);
		}
	}
	
	/**
	 * Retrive a Cache Group ID from a cache filename
	 *
	 * @param String $filename
	 * @return String
	 */
	function _getCacheGroupName( $filename ){
		$parts = explode( "_", $filename );
		for($i=1;$i<count($parts)-1;$i++){
			$group[] = $parts[$i];
		}
		return implode($group, "_");
	}
	
	/**
	 * Get the number of current Cache Groups
	 *
	 * @return int
	 */
	function getGroupCount(){
		return count($this->items);
	}
	
	/**
	 * Retrun an Array containing a sub set of the total
	 * number of Cache Groups as defined by the params.
	 *
	 * @param Int $start
	 * @param Int $limit
	 * @return Array
	 */
	function &getRows( $start, $limit ){
		$i=0;
		if(count($this->items) == 0) return null;
		
		foreach($this->items as $item) {
			if($i >= $start && $i < $start+$limit)
				$rows[] = $item;
			$i++;
		}
		return $rows;
	}
	
	/**
	 * Clean out a cache group as named by param.
	 * If no param is passed clean all cache groups.
	 *
	 * @param String $group
	 */
	function cleanCache( $group='' ){
		global $mosConfig_cachepath;
		
		if( substr( $group, 0, strlen( $this->dirMarker ) ) == $this->dirMarker )
		{
			$path = $mosConfig_cachepath.'/'.substr( $group, strlen( $this->dirMarker ), strlen( $group ) ).'/';
			
			$files = mosReadDirectory( $path );
			
			foreach ( $files as $file )
			{
				if( !is_dir( $file ) && $file != $this->indexMarker )
				{
					$file = $path . $file;
					unlink( $file );
				}
			}
		}else{
			//$cache =& mosFactory::getCache( $group );
			mosCache::cleanCache( $group );
		}
	}
	
	/**
	 * Takes an array of cache group names
	 *
	 * @param String $array
	 */
	function cleanCacheList( $array ){
		foreach ($array as $group) {
			$this->cleanCache( $group );
		}
	}
}

/**
 * This Class is used by CacheManagerData to store group cache data.
 *
 */
class CacheManagerItem {
	
	/**
	 * @var String
	 */
	var $group = "";
	/**
	 * @var Int
	 */
	var $size = 0;
	/**
	 * @var Int
	 */
	var $count = 0;
	
	/**
	 * Create a cache Group
	 *
	 * @param String $group
	 * @return CacheManagerItem
	 */
	function CacheManagerItem ( $group ){
		$this->group = $group; 
	}
	
	/**
	 * Add the value of $size to the $this->size;
	 *
	 * @param Int $size
	 */
	function updateSize( $size ){
		$this->size = number_format($this->size + $size, 2);
		$this->count++;
	}
}

/**
 * This class controls the component and it's output
 * Current it is used as a Static class
 */
class comCacheManager {
	
	/**
	 * Creates a view
	 */
	function show(){
		global $mosConfig_cachepath, $mainframe;
		
		$task = 		$mainframe->getUserStateFromRequest( "task", 'task' );
		$option = 		$mainframe->getUserStateFromRequest( "option", 'option' );
		$cachegroup =	$mainframe->getUserStateFromRequest( "cachegroup", 'cachegroup' );
		$cachelist =	$mainframe->getUserStateFromRequest( "cid", 'cid' );
		
		$cmData = new comCacheManagerData();
		
		switch ( $task ) {
			case 'cleanallcache':
				$cmData->cleanCache();
				$cmData->addPath( $mosConfig_cachepath );
				comCacheManager::_listCache( $option, $cmData );
				break;
			case 'cleancache':
				$cmData->cleanCacheList( $cachelist );
				$cmData->addPath( $mosConfig_cachepath );
				comCacheManager::_listCache( $option, $cmData );
				break;
			default:
				$cmData->addPath( $mosConfig_cachepath );
				comCacheManager::_listCache( $option, $cmData );
				break;
		}
	}
	
	/**
	 * @param String $option
	 * @param comCacheManagerdata $cmData
	 */
	function _listCache( $option, &$cmData){
		global $mosConfig_list_limit, $mainframe;
		
		$limit 			= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
		$limitstart 	= $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 );
		
		// load files
		mosFS::load( '@admin_html' );
		mosFS::load( '@pageNavigationAdmin' );
		
		$pageNav = new mosPageNav( $cmData->getGroupCount(), $limitstart, $limit );
		
		cacheManagerScreens::viewCache( $option, $cmData->getRows( $limitstart, $limit ), $pageNav );
	}
}

comCacheManager::show();
?>