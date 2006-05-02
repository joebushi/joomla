<?php
/**
* @version $Id: toolbar.config.php,v 1.1 2005/08/25 14:14:14 johanjanssens Exp $
* @package Mambo
* @subpackage Config
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


/**
 * Toolbar for Config Manager
 * @package Mambo
 * @subpackage Config
 */
class configToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function configToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );
	}

	function view() {
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'Global Configuration' ), 'config.png', 'index2.php?option=com_config' );

		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::apply();
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.config' );
		mosMenuBar::endTable();
	}
}

$tasker =& new configToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>