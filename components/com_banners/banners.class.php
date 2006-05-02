<?php
/**
* @version $Id: banners.class.php,v 1.1 2005/08/25 14:18:09 johanjanssens Exp $
* @package Mambo
* @subpackage Banners
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Banners
*/
class mosBannerClient extends mosDBTable {
	var	$cid = null;
	var $name = "";
	var $contact = "";
	var $email = "";
	var $extrainfo = "";
	var $checked_out = 0;
	var $checked_out_time = 0;
	var $editor	= "";

	function mosBannerClient( &$_db ) {
		$this->mosDBTable( '#__bannerclient', 'cid', $_db );
	}

	function check() {
		global $_LANG;
		// check for valid client name
		if (trim($this->name == "")) {
			$this->_error = $_LANG->_( 'BNR_CLIENT_NAME' );
			return false;
		}

		// check for valid client contact
		if (trim($this->contact == "")) {
			$this->_error = $_LANG->_( 'BNR_CONTACT ' );
			return false;
		}

		// check for valid client email
		if ((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email )==false)) {
			$this->_error = $_LANG->_( 'BNR_VALID_EMAIL' );
			return false;
		}
		return true;
	}
}

/**
* @package Mambo
*/
class mosBanner extends mosDBTable {
	/** @var int */
	var $bid				= null;
	/** @var int */
	var $cid				= null;
	/** @var string */
	var $type				= "";
	/** @var string */
	var $name				= "";
	/** @var int */
	var $imptotal			= 0;
	/** @var int */
	var $impmade			= 0;
	/** @var int */
	var $clicks				= 0;
	/** @var string */
	var $imageurl			= "";
	/** @var string */
	var $clickurl			= "";
	/** @var date */
	var $date				= null;
	/** @var int */
	var $showBanner			= 0;
	/** @var int */
	var $checked_out		= 0;
	/** @var date */
	var $checked_out_time	= 0;
	/** @var string */
	var $editor				= "";
	/** @var string */
	var $custombannercode	= "";

	function mosBanner( &$_db ) {
		$this->mosDBTable( '#__banner', 'bid', $_db );
		$this->set("date",date("Y-m-d G:i:s"));
	}

	function clicks() {
		$this->_db->setQuery( "UPDATE #__banner SET clicks=(clicks+1) WHERE bid=$this->bid" );
		$this->_db->query();
	}

	function check() {
		global $_LANG;
		// check for valid client id
		if (is_null($this->cid) || $this->cid == 0) {
			$this->_error = $_LANG->_( 'BNR_CLIENT' );
			return false;
		}

		if(trim($this->name) == "") {
			$this->_error = $_LANG->_( 'BNR_NAME' );
			return false;
		}

		if(trim($this->imageurl) == "") {
			$this->_error = $_LANG->_( 'BNR_IMAGE' );
			return false;
		}
		if(trim($this->clickurl) == "" && trim($this->custombannercode) == "") {
			$this->_error = $_LANG->_( 'BNR_URL' );
			return false;
		}

		return true;
	}
}
?>