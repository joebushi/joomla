<?php
/**		 
* VFS API FOR ACCESS AND STORAGE INTO MIXED PROTOCOL VIRTUAL FILESYSTEM.
*
* @version $Id: config.php,v 1.1 2005/08/30 17:37:41 bluecherry Exp $
* @package MultiVirtualFileSystem
* @author Timothy Beutels - www.bluecherry.be
* @copyright (C) 2005 Timothy Beutels
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/**  
*	CHECK IF LOADED BY MAMBO
*	Ensure this file is being included by a parent file
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/*---------------*/
/*-- LIBRARIES --*/	
/*---------------*/	

/** Define Pear Library location */ 
define("PEAR_VFS","pear\vfs.php"); 	 

/** Include gettext emulation script */
require_once("gettext.class.php");

/*---------------*/
/*-- LIBRARIES --*/	
/*---------------*/	

/** Working folder, stores files/folders when executing some VFS operations 
* @NOTE 1: If local folder at $mosConfig_absolute_path/vfs_temp, please fill in "/vfs_temp". 
* @NOTE 2: You may use any folder accessable through the VFS (eg: remote ftp folder, etc...)
*/
define("MVFS_TEMP","/vfs_temp/"); 

/*--------------*/
/*-- SECURITY --*/	
/*--------------*/	 
/*
* IMPORTANT NOTE: 
* Do NOT change these settings manually once you started using the VFS.
*
* Data you stored prior to changing these settings will be unreadable. 
*/	  

/** Use encryption of sensitive information (e.g.: ftp passwords, ...)? */
var $encrypt = true;

/** What encryption method would you like to use, if encryptoin is turned on? */
var $encrypt_method = "cast128"; 	 // Cast 128 encryption

/** Encryption sentence */
var $encrypt_sentence = "i would like to encrypt my sensetive information";

   
?>
