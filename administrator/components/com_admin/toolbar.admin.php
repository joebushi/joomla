<?php
/**
* @version $Id: toolbar.admin.php,v 1.1 2005/08/25 14:14:12 johanjanssens Exp $
* @package Mambo
* @subpackage Admin
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for admin Manager
 * @package Mambo
 * @subpackage admin
 */
class adminToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function adminToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );
	}

	function view() {
	    if ( $GLOBALS['task'] ) {
			adminToolbar::help();
		} else {
			adminToolbar::cpanel();
		}
	}

	function sysinfo( ){
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'System Information' ), 'systeminfo.png', 'index2.php?option=com_admin&amp;task=sysinfo' );

		mosMenuBar::startTable();
		mosMenuBar::help( 'screen.system.info' );
		mosMenuBar::endTable();
	}

	function cpanel( ) {
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'Control Panel' ), 'cpanel.png', 'index2.php' );

		mosMenuBar::startTable();
		mosMenuBar::help( 'screen.cpanel' );
		mosMenuBar::endTable();
	}

	function help( ) {
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'Help' ), 'support.png', 'index2.php?option=com_admin&amp;task=help' );

		mosMenuBar::startTable();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
}

$tasker =& new adminToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>