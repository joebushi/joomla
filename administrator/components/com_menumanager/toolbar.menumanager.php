<?php
/**
* @version $Id: toolbar.menumanager.php,v 1.1 2005/08/25 14:14:27 johanjanssens Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Menumanager Manager
 * @package Mambo
 * @subpackage Menumanager
 */
class menumanagerToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function menumanagerToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );

		// additional mappings
		$this->registerTask( 'edit', 'edit' );
		$this->registerTask( 'new', 'edit' );
		$this->registerTask( 'copyconfirm', 'copy' );
		$this->registerTask( 'deleteconfirm', 'delete' );
	}

	function view() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Menu Manager' ), 'menu.png', 'index2.php?option=com_menumanager' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'copyconfirm', 'copy.png', 'copy_f2.png', 'Copy', true );
		mosMenuBar::custom( 'deleteconfirm', 'delete.png', 'delete_f2.png', $_LANG->_( 'Delete' ), true );
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.menumanager' );
		mosMenuBar::endTable();
	}

	function edit( ){
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'New Menu' ), 'menu.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'savemenu', 'save.png', 'save_f2.png', $_LANG->_( 'Save' ), false );
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.menumanager.new' );
		mosMenuBar::endTable();
	}
	
	function copy( ){
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Copy Menus' ), 'menu.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'copymenu', 'copy.png', 'copy_f2.png', $_LANG->_( 'Copy' ), false );
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.menumanager.copy' );
		mosMenuBar::endTable();
	}
	
	function delete( ){
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Delete Menus' ), 'menu.png' );

		mosMenuBar::startTable();
		mosMenuBar::cancel( );
		mosMenuBar::endTable();
	}	
}

$tasker =& new menumanagerToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>