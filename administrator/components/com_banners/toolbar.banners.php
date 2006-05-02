<?php
/**
* @version $Id: toolbar.banners.php,v 1.1 2005/08/25 14:14:12 johanjanssens Exp $
* @package Mambo
* @subpackage Banners
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Banners Manager
 * @package Mambo
 * @subpackage Banners
 */
class bannersToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function bannersToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'viewbanner' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );

		// additional mappings
		$this->registerTask( 'listclients', 'viewclient' );
		$this->registerTask( 'edit', 'editbanner' );
		$this->registerTask( 'editA', 'editbanner' );
		$this->registerTask( 'new', 'editbanner' );
		$this->registerTask( 'editclient', 'editclient' );
		$this->registerTask( 'editclientA', 'editclient' );
		$this->registerTask( 'newclient', 'editclient' );
	}

	function viewbanner() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Banner Manager', 'asterisk.png', 'index2.php?option=com_banners' ) );

		mosMenuBar::startTable();
		mosMenuBar::media_manager( 'banners' );
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::deleteList();
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.banners' );
		mosMenuBar::endTable();
	}

	function viewclient() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Banner Client Manager' ), 'asterisk.png', 'index2.php?option=com_banners&task=listclients' );

		mosMenuBar::startTable();
		mosMenuBar::deleteList( '', 'removeclients' );
		mosMenuBar::editList( 'editclient' );
		mosMenuBar::addNew( 'newclient' );
		mosMenuBar::help( 'screen.banners.client' );
		mosMenuBar::endTable();
	}

	function editbanner( ){
		global $_LANG;
		global $id;
		
		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit Banner' ) : $_LANG->_( 'New Banner' ) );

		mosMenuBar::title( $text );

		mosMenuBar::startTable();
		mosMenuBar::media_manager( 'banners' );
		mosMenuBar::save();
		mosMenuBar::apply();
		
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		
		mosMenuBar::help( 'screen.banners.edit' );
		mosMenuBar::endTable();
	}

	function editclient( ){
		global $_LANG;
		global $id;
		
		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit Client' ) : $_LANG->_( 'New Client' ) );

		mosMenuBar::title( $text );

		mosMenuBar::startTable();
		mosMenuBar::save( 'saveclient' );
		mosMenuBar::apply( 'applyclient' );
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancelclient', 'Close' );
		} else {
			mosMenuBar::cancel( 'cancelclient' );
		}
		mosMenuBar::help( 'screen.banners.client.edit' );
		mosMenuBar::endTable();
	}	
}

$tasker =& new bannersToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>