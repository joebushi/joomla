<?php
/**
* @version $Id: toolbar.media.php,v 1.3 2005/08/31 17:28:51 facedancer Exp $
* @package Mambo
* @subpackage Massmail
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Media Manager
 * @package Mambo
 * @subpackage Media
 */
class mediaToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function mediaToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );
	}

	function view() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Media Manager' ), 'mediamanager.png', 'index2.php?option=com_media' );

		mosMenuBar::startTable();		
		mosMenuBar::custom( 'showNewDir', 'new.png', 'new_f2.png', $_LANG->_( 'Folder' ), false );
		mosMenuBar::custom( 'showUpload', 'upload.png', 'upload_f2.png', $_LANG->_( 'Upload' ), false );
		mosMenuBar::help( 'screen.mediamanager' );
		mosMenuBar::custom( 'config', 'MMconfig.png', 'MMconfig_f2.png', 'config', false);
		mosMenuBar::custom( 'icons', 'MMicons.png', 'MMicons_f2.png', 'icons', false);
		mosMenuBar::custom( 'details', 'MMdetails.png', 'MMdetails_f2.png', 'details', false);	
		mosMenuBar::custom( 'move_selected', 'MMmove_selected.png', 'MMmove_selected_f2.png', 'Move');
		mosMenuBar::deleteList();	
		mosMenuBar::endTable();
	}
	
	function config() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Media Manager' ), 'mediamanager.png', 'index2.php?option=com_media' );
		
		mosMenuBar::startTable();
			mosMenuBar::save();
			mosMenuBar::cancel();
		mosMenuBar::endTable();		
	}
}

$tasker =& new mediaToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>