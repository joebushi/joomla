<?php
/**
* @version $Id: toolbar.installer.php,v 1.1 2005/08/25 14:14:24 johanjanssens Exp $
* @package Mambo
* @subpackage Installer
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Installer Manager
 * @package Mambo
 * @subpackage Installer
 */
class installerToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function installerToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );

		// additional mappings
		$this->registerTask( 'new', 'newinst' );
	}

	function view() {
	    $element = mosGetParam( $_REQUEST, 'element', '' );
	    if ( $element == 'component' || $element == 'module' || $element == 'mambot' ) {
			installerToolbar::view2();
		} else {
			installerToolbar::view1();
		}
	}

	function view1() {
		global $_LANG;		
		
		$title = ucfirst( mosGetParam( $_GET, 'element', '' ) );
		$title = $title .' '. $_LANG->_( 'Installer' );
		
		mosMenuBar::title( $title, 'install.png' );

		mosMenuBar::startTable();
		mosMenuBar::help( 'screen.installer' );
		mosMenuBar::endTable();
	}
	
	function view2() {
		global $_LANG;		
		
		$title = ucfirst( mosGetParam( $_GET, 'element', '' ) );
		$title = $title .' '. $_LANG->_( 'Installer' );
		
		mosMenuBar::title( $title, 'install.png' );

		mosMenuBar::startTable();
		mosMenuBar::deleteList( '', 'remove', 'Uninstall' );
		mosMenuBar::help( 'screen.installer2' );
		mosMenuBar::endTable();
	}
	
	function newinst() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
}

$tasker =& new installerToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>