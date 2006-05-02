<?php
/**
* @version $Id: toolbar.export.php,v 1.1 2005/08/25 14:14:15 johanjanssens Exp $
* @package Mambo
* @subpackage Export
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Export
 * @package Mambo
 * @subpackage Export
 */
class exportToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function exportToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'exportOptions' );

		// set task level access control
		$this->setAccessControl( 'com_checkin', 'manage' );
	}

	function exportOptions() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Export Manager' ), 'backup.png', 'index2.php?option=com_export' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'export', 'forward.png','forward_f2.png', $_LANG->_( 'Export' ), true );
		mosMenuBar::help( 'screen.exportoptions' );
		mosMenuBar::endTable();
	}

	function export() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Export' ), 'backup.png' );

		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::help( 'screen.exportoptions' );
		mosMenuBar::endTable();
	}

	function restoreList() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Exported Files' ), 'backup.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom('restore','restore.png','restore_f2.png',$_LANG->_( 'Restore' ), true);
		mosMenuBar::custom('deleteFiles','delete.png','delete_f2.png',$_LANG->_( 'Delete' ), true);
		mosMenuBar::help( 'screen.restorelist' );
		mosMenuBar::endTable();
	}
}

$tasker =& new exportToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>