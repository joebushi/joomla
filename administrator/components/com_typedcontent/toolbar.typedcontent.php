<?php
/**
* @version $Id: toolbar.typedcontent.php,v 1.1 2005/08/25 14:14:53 johanjanssens Exp $
* @package Mambo
* @subpackage Content
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Static Content Manager
 * @package Mambo
 * @subpackage Static Content
 */
class typedcontentToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function typedcontentToolbar() {
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
		
		mosMenuBar::title( $_LANG->_( 'Static Content Manager' ), 'addedit.png', 'index2.php?option=com_typedcontent' );

		mosMenuBar::startTable();
		mosMenuBar::popup('', 'previewcontent', 'preview.png', 'Preview', true);
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::trash();
		mosMenuBar::editList( 'editA' );
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.staticcontent' );
		mosMenuBar::endTable();
	}

	function edit( ){
		global $_LANG;
		global $id;
		
		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit Static Content' ) : $_LANG->_( 'New Static Content' ) );

		mosMenuBar::title( $text, 'addedit.png' );

		mosMenuBar::startTable();
		//mosMenuBar::preview( 'contentwindow', true );
		mosMenuBar::media_manager();
		mosMenuBar::save();
		mosMenuBar::apply();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::help( 'screen.staticcontent.edit' );
		mosMenuBar::endTable();
	}
}

$tasker =& new typedcontentToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>