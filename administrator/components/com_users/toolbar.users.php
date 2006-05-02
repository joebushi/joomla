<?php
/**
* @version $Id: toolbar.users.php,v 1.1 2005/08/25 14:15:07 johanjanssens Exp $
* @package Mambo
* @subpackage Users
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Users Manager
 * @package Mambo
 * @subpackage Users
 */
class usersToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function usersToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );

		// additional mappings
		$this->registerTask( 'edit', 'edit' );
		$this->registerTask( 'editA', 'edit' );
		$this->registerTask( 'new', 'edit' );
	}

	function view() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'User Manager' ), 'user.png', 'index2.php?option=com_users' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'logout', 'cancel.png', 'cancel_f2.png', $_LANG->_( 'Logout' ) );
		mosMenuBar::deleteList();
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.users' );
		mosMenuBar::endTable();
	}

	function edit( ){
		global $_LANG;
		global $id;
		
		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit User' ) : $_LANG->_( 'New User' ) );
		
		mosMenuBar::title( $text, 'user.png' );

		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::apply();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', $_LANG->_( 'Close' ) );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::help( 'screen.users.edit' );
		mosMenuBar::endTable();
	}

	function masscreate() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Mass Create Users' ), 'user.png', 'index2.php?option=com_users&task=masscreate' );

		mosMenuBar::startTable();
		mosMenuBar::save( 'savemasscreate' );
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.users.masscreate' );
		mosMenuBar::endTable();
	}
}

$tasker =& new usersToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>