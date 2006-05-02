<?php
/**
* @version $Id: poll.class.php,v 1.1 2005/08/25 14:18:12 johanjanssens Exp $
* @package Mambo
* @subpackage Polls
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

//** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Polls
*/
class mosPoll extends mosDBTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string */
	var $title=null;
	/** @var string */
	var $checked_out=null;
	/** @var time */
	var $checked_out_time=null;
	/** @var boolean */
	var $published=null;
	/** @var int */
	var $access=null;
	/** @var int */
	var $lag=null;

	/**
	* @param database A database connector object
	*/
	function mosPoll( &$db ) {
		$this->mosDBTable( '#__polls', 'id', $db );
	}

	// overloaded check function
	function check() {
  		global $_LANG;

		// check for valid name
		if (trim( $this->title ) == '') {
			$this->_error = $_LANG->_( 'Your Poll must contain a title.' );
			return false;
		}

		// check for valid lag
		$this->lag = intval( $this->lag );
		if ($this->lag == 0) {
			$this->_error = $_LANG->_( 'Your Poll must have a non-zero lag time.' );
			return false;
		}

		// check for existing title
		$this->_db->setQuery( "SELECT id FROM #__polls WHERE title='$this->title'"
		);

		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->_error = $_LANG->_( 'ERRORMODULEALREADYWITHTHATNAME' );
			return false;
		}

		// sanitise some data
		if (!get_magic_quotes_gpc()) {
			$row->title = addslashes( $row->title );
		}

		return true;
	}

	// overloaded delete function
	function delete( $oid=null ) {
		$k = $this->_tbl_key;

		if ( $oid ) {
			$this->$k = intval( $oid );
		}

		if ( mosDBTable::delete( $oid ) ) {
			$this->_db->setQuery( "DELETE FROM #__poll_data WHERE pollid='".$this->$k."'" );
			if ( !$this->_db->query() ) {
				$this->_error .= $this->_db->getErrorMsg() . "\n";
			}

			$this->_db->setQuery( "DELETE FROM #__poll_date WHERE poll_id='".$this->$k."'" );
			if ( !$this->_db->query() ) {
				$this->_error .= $this->_db->getErrorMsg() . "\n";
			}

			$this->_db->setQuery( "DELETE from #__poll_menu where pollid='".$this->$k."'" );
			if ( !$this->_db->query() ) {
				$this->_error .= $this->_db->getErrorMsg() . "\n";
			}

			return true;
		} else {
			return false;
		}
	}
}
?>
