<?php
/**
* @version $Id: admin.initialise.php,v 1.1 2005/08/27 15:31:33 ratlaw101 Exp $
* @package $ambo
* @subpackage Initialise Manager
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* $ambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class comInitialiseData
{
	var $_entries = array();
	var $_pathToIni = '';
	
	function comInitialiseData( $path )
	{
		$handle = @fopen( $path, "r" );
		if( $handle == null )
		{
			$handle = @fopen( $path, "w+" );
			if( $handle == null ) return;
		}
		
		$this->_pathToIni = $path;
		
		if( filesize( $this->_pathToIni ) > 0 )
		{
			$id = 1;
			while (($data = fgetcsv( $handle, 1000, ",") ) !== false) {
				$this->_addEnry( $id, $data );
				$id++;
			}
		}
		@fclose( $handle );
	}
	
	function _addEnry( $id, $data )
	{
		$this->_entries[$id] = new comInitialiseEntry( $data );
		$this->_entries[$id]->setId( $id );
	}
	
	function _writeDatafile()
	{
		if( mosFS::file_exists( $this->_pathToIni ) )
		{
			@rename( $this->_pathToIni, $this->_pathToIni.'.bak');
		}
		
		$string = '';
		foreach ( $this->_entries as $entry )
		{
			$string.= $entry->toString();
		}
		
		mosFS::write( $this->_pathToIni, $string );
	}
	
	function isWriteable()
	{
		return is_writable( $this->_pathToIni );
	}
	
	function saveEntry( $id, $string, $type, $value, $published )
	{
		if( $id < 1 ) $id = $this->getEntriesCount()+1;
		
		$this->_addEnry( $id, array( $string, $type, $value, $published ) );
		
		$this->_writeDatafile();
	}
	
	function setPublished( $ids, $value )
	{
		foreach ( $ids as $id ) {
			if( isset( $this->_entries[$id] ) )
				$this->_entries[$id]->setPublished( $value ? 1:0 );
		}
		
		$this->_writeDatafile();
	}
	
	function deleteEntries( $ids )
	{
		foreach ( $ids as $id ) {
			if( isset( $this->_entries[$id] ) )
				unset( $this->_entries[$id] );
		}
		
		$this->_writeDatafile();
	}
	
	function getEntry( $id )
	{
		if( isset( $this->_entries[$id] ) )
			return $this->_entries[$id];
	}
	
	function &getEntries( $start, $limit )
	{
		if( count( $this->_entries ) == 0 ) return null;
		$i=0;
		foreach( $this->_entries as $entry ) {
			if( $i >= $start && $i < $start+$limit )
				$entires[] = $entry;
			$i++;
		}
		return $entires;
	}
	
	function getEntriesCount()
	{
		return count( $this->_entries );
	}
	
	function reorderEntry( $id, $direction )
	{	
		// If the destination does not exsist do nothing
		if( !isset( $this->_entries[$id + $direction] ) ) return;
		
		$tmp = $this->_entries[$id]; // Copy the entry to be moved
		
		$this->_entries[$id] = $this->_entries[$id + $direction]; // Copy the destination entry
		$this->_entries[$id]->setId( $id ); // Set the id to match new position
		
		$this->_entries[$id + $direction] = $tmp; // Insert the copied entry
		$this->_entries[$id + $direction]->setId( $id + $direction ); // Set the id to match new position
		
		$this->_writeDatafile(); // write datafile
	}
}

class comInitialiseEntry
{
	var $id = ''; //unique id
	var $string = ''; //String to search for
	var $type = ''; //0=template 1=redirect
	var $value = ''; //String template or URL
	var $published = ''; //0=no 1=yes
	
	function comInitialiseEntry( $array )
	{
		$this->string = $array[0];
		$this->type = $array[1];
		$this->value = $array[2];
		$this->published = $array[3];
	}
	
	function setId( $id )
	{
		$this->id = $id;
	}
	
	function setPublished( $value )
	{
		$this->published = $value;
	}
	
	function setString( $value )
	{
		$this->string = $value;
	}
	
	function setValue( $value )
	{
		$this->value = $value;
	}
	
	function toString()
	{
		return $this->string.",".$this->type.",".$this->value.",".$this->published."\n";
	}
}

class comInitialise
{
	
	/**
	 * Creates a view
	 */
	function show(){
		global $mosConfig_absolute_path, $mainframe;
		
		$task = 		$mainframe->getUserStateFromRequest( "task", 'task' );
		$option = 		$mainframe->getUserStateFromRequest( "option", 'option' );
		$entriesList =	$mainframe->getUserStateFromRequest( "cid", 'cid' );
		
		$data = new comInitialiseData( $mosConfig_absolute_path.'/mambots/initialise.ini' );
		
		switch ( $task )
		{
			case 'reorder':
				$id =			$entriesList[0];
				$direction =	$mainframe->getUserStateFromRequest( 'direction', 'direction' );
				
				$data->reorderEntry( $id, $direction );
				comInitialise::_listEntries( $option, $data );
				break;
				
			case 'edit':
				$entry = $data->getEntry( $entriesList[0] );
				comInitialise::_showEntry( $option, $entry );
				break;
				
			case 'new':
				comInitialise::_showEntry( $option, null );
				break;
				
			case 'save':
			
				$id =			$mainframe->getUserStateFromRequest( 'id', 'id', 0 );
				$string =		$mainframe->getUserStateFromRequest( 'string', 'string' );
				$type =			$mainframe->getUserStateFromRequest( 'type', 'type' );
				$value =		$mainframe->getUserStateFromRequest( 'value', 'value' );
				$published =		$mainframe->getUserStateFromRequest( 'published', 'published', 0 );
				
				$data->saveEntry( $id, $string, $type, $value, $published );
				comInitialise::_listEntries( $option, $data );
				break;
				
			case 'remove':
				$data->deleteEntries( $entriesList );
				comInitialise::_listEntries( $option, $data );
				break;
				
			case 'publish':
				$data->setPublished( $entriesList, true );
				comInitialise::_listEntries( $option, $data );
				break;
				
			case 'unpublish':
				$data->setPublished( $entriesList, false );
				comInitialise::_listEntries( $option, $data );
				break;
				
			default:
				comInitialise::_listEntries( $option, $data );
				break;
		}
	}
	
	/**
	 * Creates a view
	 */
	function _listEntries( $option, $data ){
		global $mosConfig_list_limit, $mainframe;
		
		$limit 			= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
		$limitstart 	= $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 );
		
		// load files
		mosFS::load( '@admin_html' );
		mosFS::load( '@pageNavigationAdmin' );
		
		$pageNav = new mosPageNav( $data->getEntriesCount(), $limitstart, $limit );
		$permissions = $data->isWriteable();
		
		comInitialiseScreens::viewEntries( $option, $data->getEntries( $limitstart, $limit ), $pageNav, $permissions );
	}
	
	/**
	 * Creates a view
	 */
	function _showEntry( $option, $entry ){
		global $mosConfig_list_limit, $mainframe;
		
		$limit 			= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
		$limitstart 	= $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 );
		
		// load files
		mosFS::load( '@admin_html' );
		
		comInitialiseScreens::showEntry( $option, $entry );
	}
}

comInitialise::show();
?>