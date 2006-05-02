<?php
/**
* @version $Id: update_server.class.php,v 1.1 2005/08/25 14:14:54 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
// update
/* 
Mambo Update Tables
*/

// remoteapp
/*
Remotely available applications
*/
class updateremoteapp extends mosDBTable {
	var $appid = '';	// INT(10) 
	var $productname = '';	// VARCHAR(50) 
	var $productid = 0;	// INT(10)
	var $type = '';		// VARCHAR(20)
	var $directory = '';	// VARCHAR(50) 
	var $versionstring = '';	// VARCHAR(20) 
	var $updateurl = '';	// TEXT(0) 
	var $lastupdate = '';	// DATE(0) 
	var $updated = 0;	// INT(0)
	var $updatedversion = "";// VARCHAR(20)
	var $releaseid = 0;	// INT(0)
	var $releaseinfourl = "";// TEXT(0)
	var $remoteapp	= 0;	// INT(0)
	function updatecache( &$db ) {
		$this->mosDBTable('#__update_cache', 'cacheid', $db);
	}
}

// cache
/*

*/
class updatecache extends mosDBTable {
	var $cacheid = '';	// INT(10) 
	var $productname = '';	// VARCHAR(50) 
	var $productid = 0;	// INT(10)
	var $type = '';		// VARCHAR(20)
	var $directory = '';	// VARCHAR(50) 
	var $versionstring = '';	// VARCHAR(20) 
	var $updateurl = '';	// TEXT(0) 
	var $lastupdate = '';	// DATE(0) 
	var $updated = 0;	// INT(0)
	var $updatedversion = "";// VARCHAR(20)
	var $releaseid = 0;	// INT(0)
	var $releaseinfourl = "";// TEXT(0)
	function updatecache( &$db ) {
		$this->mosDBTable('#__update_cache', 'cacheid', $db);
	}
}

// dependency
/*

*/
class updatedependency extends mosDBTable {
	var $dependencyid = '';	// INT(10) 
	var $currentrelease = '';	// INT(10) 
	var $depprodname = '';	// VARCHAR(50) 
	var $depversionstring = '';	// VARCHAR(20) 
	var $depremotesite = '';	// INT(10) 
	var $upgradeonly = 0;		// INT(10)
	function updatedependency( &$db ) {
		$this->mosDBTable('#__update_dependencies', 'dependencyid', $db);
	}
}

// product
/*
Product Table
*/
class updateproduct extends mosDBTable {
	var $productid = '';	// INT(10) 
	var $productname = '';	// VARCHAR(50) 
	var $producttype = '';	// VARCHAR(20)

	var $productdescription = '';	// TEXT(0) 
	var $producturl = '';	// TEXT(0) 
	var $productdetailsurl = '';	// TEXT(0) 
	var $published = 1; 	// INT(10)
	function updateproduct( &$db ) {
		$this->mosDBTable('#__update_product', 'productid', $db);
	}
}

// release
/*

*/
class updaterelease extends mosDBTable {
	var $releaseid = '';	// INT(10) 
	var $productid = '';	// INT(10) 
	var $releasetitle = '';	// VARCHAR(30) 
	var $releasedescription = '';	// TEXT(0) 
	var $releasesurl = '';	// TEXT(0) 
	var $releasechangelog = '';	// TEXT(0) 
	var $releasenotes = '';	// TEXT(0) 
	var $versionstring = '';	// VARCHAR(20) 
	var $updateurl = '';	// TEXT(0) 
	var $releaseurl = '';	// TEXT(0) 
	var $releasedate = '';	// DATE(0) 
	var $published = 1; 	// INT(10)
	function updaterelease( &$db ) {
		$this->mosDBTable('#__update_releases', 'releaseid', $db);
	}
}

// remotesite
/*
Remote Mambo Update Server installations
*/
class updateremotesite extends mosDBTable {
	var $remotesiteid = '';	// INT(10) 
	var $remotesitename = '';	// VARCHAR(30) 
	var $remotesiteurl = '';	// TEXT(0) 
	function updateremotesite( &$db ) {
		$this->mosDBTable('#__update_remotesite', 'remotesiteid', $db);
	}
}


?>
