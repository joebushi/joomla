<?php
/**
* @version $Id: mambo.scripthandler.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/**
 * Manages all scrtips
 *
 * USEAGE:
 * mosFactory::getScriptHandler()
 * $scriptHandler->add( 'http://www.somehost.com/script.js', 'http' );
 * or
 * $scriptHandler->add( 'http://www.somehost.com/style.css', 'http' );
 * or
 * $scriptHandler->add( '<script>my js script</script>', 'js', true ); // the last argument allows for caching
 * or
 * $scriptHandler->add( '<style>my js script</style>', 'css', false ); // the last argument allows for caching
 *
 * @singleton
 */
class mosScriptHandler
{
	var $_scripts = array();
	
	/**
	 * @param String $id
	 * @param String $script
	 */
	function _add( $id, $script )
	{
		if( !isset( $this->_scripts[$id] ) ) $this->_scripts[$id] = $script;
	}
	
	/**
	 * Outputs all scripts in $this->_scripts
	 */
	function getScripts()
	{
		foreach ($this->_scripts as $script) {
			echo $script."\n";
		}
	}
	
	/**
	 * If a script matching the id is found it is returned
	 *
	 * @param String $id
	 * @return String
	 */
	function getScript( $id )
	{
		if( isset( $this->_isScript( $id ) ) ) return $this->_scripts[$id];
		return null;
	}
	
	/**
	 * @param String $script Either a url or script string
	 * @param String $type http=URL String, js=JavaScript String, css=CSS String
	 * @param String $idParam adds an id param to the tag e.g. <scritp id="$idParam" />
	 * @param boolean $cache
	 * @return boolean Return true or false depending if the Script was added
	 */
	function add( $script, $type='', $idParam=null, $cache=false )
	{
		$id = md5( $script );
		if( $this->_isScript( $id ) ) return false;
		
		switch ($type)
		{
			case 'http': // load JavaScript file from external source
				return $this->_addUrl( $id, $script, $idParam, false );
				
			case 'js': // string as JavaScript
				return $this->_addJsString( $id, $script, $idParam, $cache );
			
			case 'css': // string as JavaScript
				return $this->_addCssString( $id, $script, $idParam, $cache );
				
			default: // Try and load from library 
				return false;
		}
	}
	
	/**
	 * Check if the file or url exists
	 *
	 * @param $url URL or path
	 * @return boolean
	 */
	function _fileExists( $url )
	{
		$handle = @fopen( $url, 'r' );
		return $handle ? true : false;
	}
	
	/**
	 * @param String $id
	 * @param String $path
	 * @param String $idParam
	 * @return boolean
	 */
	function _addUrl( $id, $path, $idParam )
	{
		$type = mosFS::getExt( $path );
		switch ( $type )
		{
			case 'js':
				return $this->_addJsFile( $id, $path, $idParam, false );
			
			case 'css':
				return $this->_addCssFile( $id, $path, $idParam, false );
		
			default:
				return false;
		}
	}
	
	/**
	 * @param String $id
	 * @param String $path
	 * @param String $idParam
	 * @param boolean $library
	 * @return boolean
	 */
	function _addJsFile( $id, $path, $idParam, $library=false )
	{
		global $mosConfig_live_site;
		
		if( $library ) // to be finalised
			$path = $mosConfig_live_site.'/includes/js/'.str_replace( '.', '/', $path).'.js';
		
		if( !$this->_fileExists( $path ) ) return false;
		
		if( $idParam ) $idParam = ' id="'.$idParam.'"';
		
		$this->_add( $id, '<script type="text/javascript" src="'.$path.'"'.$idParam.'></script>' );
		return true;
	}
	
	/**
	 * @param String $id
	 * @param String $script
	 * @param String $idParam
	 * @param boolean $cache
	 * @return boolean
	 */
	function _addJsString( $id, $script, $idParam, $cache=false )
	{
		if( $cache )
		{
			global $mosConfig_live_site;
			
			$cache =& mosFactory::getCache( 'scripthandler', 'mosCache_File' );
			$cache->setCacheUrl( $mosConfig_live_site.'/cache' );
			$path = $cache->getUrl( $script, '.js' );
			
			return $this->_addJsFile( $id, $path, $idParam, false );
		}
		else
		{
			if( $idParam ) $idParam = ' id="'.$idParam.'"';
			$this->_add( $id, '<script language="JavaScript" type="text/javascript"'.$idParam.">\n".$script."\n</script>\n" );
			return true;
		}
	}
	
	/**
	 * @param String $id
	 * @param String $path
	 * @param String $idParam
	 * @param boolean $library
	 * @return boolean
	 */
	function _addCssFile( $id, $path, $idParam, $library=false )
	{
		global $mosConfig_live_site;
		
		if( $library ) // to be finalised
			$path = $mosConfig_live_site.'/includes/css/'.str_replace( '.', '/', $path).'.js';
		
		if( !$this->_fileExists( $path ) ) return false;
		
		if( $idParam ) $idParam = ' id="'.$idParam.'"';
		
		$this->_add( $id, '<link type="text/css" rel="stylesheet" href="'.$path.'"'.$idParam.'/>' );
		return true;
	}
	
	/**
	 * @param String $id
	 * @param String $script
	 * @param String $idParam
	 * @param boolean $cache
	 * @return boolean
	 */
	function _addCssString( $id, $script, $idParam, $cache=false )
	{
		if( $cache )
		{
			global $mosConfig_live_site;
			
			$cache =& mosFactory::getCache( 'scripthandler', 'mosCache_File' );
			$cache->setCacheUrl( $mosConfig_live_site.'/cache' );
			$path = $cache->getUrl( $script, '.css' );
			
			return $this->_addCssFile( $id, $path, $idParam, false );
		}
		else
		{
			if( $idParam ) $idParam = ' id="'.$idParam.'"';
			$this->_add( $id, '<style'.$idParam.">\n".$script."\n</style>\n" );
			return true;
		}
	}
	
	/**
	 * To be finalised; dependent on library implmentation
	 *
	 * @return boolean
	 */
	function _dependancyCheck()
	{
		return true;
	}
	
	/**
	 * Checks if a script id exsits
	 *
	 * @param String $id
	 * @return boolean
	 */
	function _isScript( $id )
	{
		return isset( $this->_scripts[$id] );
	}
}
?>