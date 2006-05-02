<?php
/**
* @version $Id: toolbar.statistics.php,v 1.1 2005/08/25 14:14:52 johanjanssens Exp $
* @package Mambo
* @subpackage Statistics
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Statistics Manager
 * @package Mambo
 * @subpackage Statistics
 */
class statisticsToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function statisticsToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );
	}

	function view() {
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'Browser, OS, Domain Statistics' ), 'browser.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'resetStats', 'delete.png', 'delete_f2.png', $_LANG->_( 'Delete' ), false );
		mosMenuBar::help( 'screen.stats.browser' );
		mosMenuBar::endTable();
	}

	function searches( ){
		global $_LANG;
		global $mainframe;
		
		$title = $_LANG->_( 'Search Engine Text' ) .' : ';
		$title .= $_LANG->_( 'logging is' ) .' : ';
		$title .= $mainframe->getCfg( 'enable_log_searches' ) ? '<b><font color="green">'. $_LANG->_( 'Enabled' ) .'</font></b>' : '<b><font color="red">'. $_LANG->_( 'Disabled' ) .'</font></b>';
		
		mosMenuBar::title( $title, 'searchtext.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'resetStats', 'delete.png', 'delete_f2.png', $_LANG->_( 'Delete' ), false );
		mosMenuBar::help( 'screen.stats.searches' );
		mosMenuBar::endTable();
	}

	function pageimp( ){
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'Page Impression Statistics' ), 'impressions.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'resetStats', 'delete.png', 'delete_f2.png', $_LANG->_( 'Delete' ), false );
		mosMenuBar::help( 'screen.stats.impressions' );
		mosMenuBar::endTable();
	}
}

$tasker =& new statisticsToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>