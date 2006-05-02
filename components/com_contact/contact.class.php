<?php
/**
* @version $Id: contact.class.php,v 1.1 2005/08/25 14:18:09 johanjanssens Exp $
* @package Mambo
* @subpackage Contact
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

mosFS::load( 'includes/vcard.class.php' );
	
/**
* @package Mambo
* @subpackage Contact
*/
class mosContact extends mosDBTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string */
	var $name=null;
	/** @var string */
	var $con_position=null;
	/** @var string */
	var $address=null;
	/** @var string */
	var $suburb=null;
	/** @var string */
	var $state=null;
	/** @var string */
	var $country=null;
	/** @var string */
	var $postcode=null;
	/** @var string */
	var $telephone=null;
	/** @var string */
	var $fax=null;
	/** @var string */
	var $misc=null;
	/** @var string */
	var $image=null;
	/** @var string */
	var $imagepos=null;
	/** @var string */
	var $email_to=null;
	/** @var int */
	var $default_con=null;
	/** @var int */
	var $published=null;
	/** @var int */
	var $checked_out=null;
	/** @var datetime */
	var $checked_out_time=null;
	/** @var int */
	var $ordering=null;
	/** @var string */
	var $params=null;
	/** @var int A link to a registered user */
	var $user_id=null;
	/** @var int A link to a category */
	var $catid=null;
	/** @var int */
	var $access=null;

	/**
	* @param database A database connector object
	*/
	function mosContact() {
	    global $database;
		$this->mosDBTable( '#__contact_details', 'id', $database );
	}

	function check() {
		$this->default_con = intval( $this->default_con );
		return true;
	}
}

/**
* @package Mambo
* class needed to extend vcard class and to correct minor errors
*/
class MambovCard extends vCard {

	// needed to fix bug in vcard class
	function setName( $family='', $first='', $additional='', $prefix='', $suffix='' ) {
		$this->properties["N"] 	= "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
	}

	// needed to fix bug in vcard class
	function setAddress( $postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL' ) {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key 	= 'ADR';
		if ( $type != '' ) {
			$key	.= ';'. $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';
		$this->properties[$key] = encode( $extended ) .';'. encode( $street ) .';'. encode( $city ) .';'. encode( $region) .';'. encode( $zip ) .';'. encode( $country );
	}

	// added ability to set filename
	function setFilename( $filename ) {
		$this->filename = $filename .'.vcf';
	}	

	// added ability to set position/title
	function setTitle( $title ) {
		$title 	= trim( $title );
		$this->properties['TITLE'] 	= $title;
	}

	// added ability to set organisation/company
	function setOrg( $org ) {
		$org 	= trim( $org );
		$this->properties['ORG'] 	= $org;
	}

	function getVCard( $sitename ) {
		$text 	= "BEGIN:VCARD\r\n";
		$text 	.= "VERSION:2.1\r\n";
		foreach( $this->properties as $key => $value ) {
			$text	.= "$key:$value\r\n";
		}
		$text	.= "REV:" .date("Y-m-d") ."T". date("H:i:s"). "Z\r\n";
		$text	.= "MAILER: Mambo vCard for ". $sitename ."\r\n";
		$text	.= "END:VCARD\r\n";
		return $text;
	}
	
}
?>