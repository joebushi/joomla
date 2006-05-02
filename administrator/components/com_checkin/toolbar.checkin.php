<?php
/**
* @version $Id: toolbar.checkin.php,v 1.1 2005/08/25 14:14:13 johanjanssens Exp $
* @package Mambo
* @subpackage Checkin
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Checkin Manager
 * @package Mambo
 * @subpackage Checkin
 */
class checkinToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function checkinToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'checkinList' );

		// set task level access control
		$this->setAccessControl( 'com_checkin', 'manage' );
	}

	function checkinList() {
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'Checkin Manager' ), 'checkin.png', 'index2.php?option=com_checkin' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'checkin', 'save.png', 'save_f2.png', $_LANG->_( 'Checkin' ), true );
		mosMenuBar::help( 'screen.checkin' );
		mosMenuBar::endTable();
	}
}

$tasker =& new checkinToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>