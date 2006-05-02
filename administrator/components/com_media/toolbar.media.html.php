<?php
/**
* @version $Id: toolbar.media.html.php,v 1.2 2005/08/28 14:14:43 facedancer Exp $
* @package Mambo
* @subpackage Massmail
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Massmail
*/
class TOOLBAR_media {
	/**
	* Draws the menu for a New Media
	*/
	function _DEFAULT() {
		global $_LANG;		
		
		mosMenuBar::startTable();
		mosMenuBar::title( $_LANG->_( 'Media Manager' ), 'mediamanager.png', 'index2.php?option=com_media' );
		mosMenuBar::custom( 'showNewDir', 'new.png', 'new_f2.png', $_LANG->_( 'New Folder' ), false );
		mosMenuBar::custom( 'showUpload', 'upload.png', 'upload_f2.png', $_LANG->_( 'Upload' ), false );
		mosMenuBar::help( 'screen.mediamanager' );
		mosMenuBar::endTable();
	}
}
?>