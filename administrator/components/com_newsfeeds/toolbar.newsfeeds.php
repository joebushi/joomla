<?php
/**
* @version $Id: toolbar.newsfeeds.php,v 1.1 2005/08/25 14:14:50 johanjanssens Exp $
* @package Mambo
* @subpackage Newsfeeds
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Newsfeeds Manager
 * @package Mambo
 * @subpackage Newsfeeds
 */
class newsfeedsToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function newsfeedsToolbar() {
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
		
		mosMenuBar::title( $_LANG->_( 'News Feed Manager' ), 'asterisk.png', 'index2.php?option=com_newsfeeds' );

		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::deleteList();
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.newsfeeds' );
		mosMenuBar::endTable();
	}

	function edit( ){
		global $_LANG;
		global $id;
		
		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit News Feed' ) : $_LANG->_( 'New News Feed' ) );

		mosMenuBar::title( $text );

		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::apply();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::help( 'screen.newsfeeds.edit' );
		mosMenuBar::endTable();
	}
}

$tasker =& new newsfeedsToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>