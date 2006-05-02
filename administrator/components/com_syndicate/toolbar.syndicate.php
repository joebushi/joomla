<?php
/**
* @version $Id: toolbar.syndicate.php,v 1.1 2005/08/25 14:14:53 johanjanssens Exp $
* @package Mambo
* @subpackage Syndicate
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Syndicate Manager
 * @package Mambo
 * @subpackage Synidicate
 */
class syndicateToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function syndicateToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' )
	}

	function view() {
		global $_LANG;		
		
		mosMenuBar::title( $_LANG->_( 'Syndication Manager', 'asterisk.png','index2.php?option=com_syndicate' ) );

		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel( 'cancel', 'Close' );
		mosMenuBar::help( 'screen.syndicate' );
		mosMenuBar::endTable();
	}
}

$tasker =& new syndicateToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>