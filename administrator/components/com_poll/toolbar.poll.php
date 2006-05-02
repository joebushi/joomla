<?php
/**
* @version $Id: toolbar.poll.php,v 1.1 2005/08/25 14:14:51 johanjanssens Exp $
* @package Mambo
* @subpackage Polls
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Poll Manager
 * @package Mambo
 * @subpackage Poll
 */
class pollToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function pollToolbar() {
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

		mosMenuBar::title( $_LANG->_( 'Poll Manager' ), 'properties_f2.png', 'index2.php?option=com_poll' );

		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::deleteList();
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.polls' );
		mosMenuBar::endTable();
	}

	function edit( ){
		global $_LANG;
		global $id;

		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit Poll' ) : $_LANG->_( 'New Poll' ) );

		mosMenuBar::title( $text, 'properties_f2.png' );

		mosMenuBar::startTable();
		mosMenuBar::popup('', 'previewpoll', 'preview.png', 'Preview');
	    mosMenuBar::save();
		mosMenuBar::apply();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
	    mosMenuBar::help( 'screen.polls.edit' );
	    mosMenuBar::endTable();
	}
}

$tasker =& new pollToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>