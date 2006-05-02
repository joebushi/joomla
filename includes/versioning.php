<?php
/**
* @version $Id: versioning.php,v 1.4 2005/09/02 18:53:23 alekandreev Exp $
* @package Mambo
* @subpackage Database
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* mosDBVersionedTable Abstract Class.
* @abstract
* @package Mambo
* @subpackage Database
*
* Adds transparent versioning support to all database objects.
* @package Mambo
* @author Alek Andreev <alek@zvuk.net>
*/
class mosDBVersionedTable extends mosDBTable {
	function store() {
		$k = $this->_tbl_key;
		$this->_db->setQuery("select max(revision) from {$this->_tbl} where `{$this->_tbl_key}` = '" . $this->$k . "'");
		$this->_db->query();
		$this->revision = 1 + $this->_db->loadResult();
		if ($this->revision == 1)
			$this->active = 1;
		if ($this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key )) {
			if ($_POST['draft'] != 'yes')
				$this->setActiveRevision($this->revision);
			return true;
		} else {
			$this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->getErrorMsg();
			return false;
		}
	}
	
	function load( $oid=null, $revision = null) {
		
		/* if (!empty($_GET['showrev']) and $revision === null)
			$revision = $_GET['showrev'];*/
		$k = $this->_tbl_key;
		if ($oid !== null) {
			$this->$k = $oid;
		}
		$oid = $this->$k;
		if ($oid === null) {
			return false;
		}
		$query = "SELECT *"
		. "\n FROM $this->_tbl"
		. "\n WHERE $this->_tbl_key = '$oid'";
		if ($revision !== null) $query .= " AND revision = '$revision'";
		else $query .= " AND active = 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadObject( $this );
		
	}

	function setActiveRevision($revision,$id = NULL) {
		if ($id === NULL) $id = $this->id;
		$k = $this->_tbl_key;
		$this->_db->setQuery("UPDATE {$this->_tbl} SET active = IF(revision = '$revision',1,0) WHERE `{$this->_tbl_key}` = '" . $id . "'");
		$this->_db->query();
	}
	
	function listRevisions() {
		$this->_db->setQuery("SELECT c.revision revision, unix_timestamp(c.modified) modified, c.active active, u.name name FROM {$this->_tbl} c, #__users u WHERE 
				c.modified_by = u.id AND c.id = {$this->id} ORDER BY c.revision DESC");
		return $this->_db->loadAssocList();
	}
	
	/* checkout is not supported for versioned content, yet */
	function checkout($id = null) { }
	function checkin($id = null) { }
	function isCheckedOut() { return false; }
}