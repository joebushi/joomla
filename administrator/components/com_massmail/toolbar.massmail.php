<?php
/**
* @version $Id: toolbar.massmail.php,v 1.1 2005/08/25 14:14:26 johanjanssens Exp $
* @package Mambo
* @subpackage Massmail
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Massmail Manager
 * @package Mambo
 * @subpackage Massmail
 */
class massmailToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function massmailToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );
	}

	function view() {
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'Mass Mail' ), 'massemail.png', 'index2.php?option=com_massmail' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'send', 'publish.png', 'publish_f2.png', 'Send Mail', false);
		mosMenuBar::cancel( 'cancel', 'Close' );
		mosMenuBar::help( 'screen.users.massmail' );
		mosMenuBar::endTable();
	}
}

$tasker =& new massmailToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>