<?php
/**
* @version $Id: toolbar.contact.php,v 1.1 2005/08/25 14:14:15 johanjanssens Exp $
* @package Mambo
* @subpackage Contact
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Contact Manager
 * @package Mambo
 * @subpackage Contact
 */
class contactToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function contactToolbar() {
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
		
		mosMenuBar::title( $_LANG->_( 'Contact Manager' ), 'contacts_f2.png', 'index2.php?option=com_contact' );

		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::deleteList();
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.contactmanager' );
		mosMenuBar::endTable();
	}

	function edit( ){
		global $id;
		global $_LANG;		
		
		if ( !$id ) {
			$id = mosGetParam( $_REQUEST, 'cid', '' );
		}
		$text = ( $id ? $_LANG->_( 'Edit Contact' ) : $_LANG->_( 'New Contact' ) );
		
		mosMenuBar::title( $text, 'contacts_f2.png' );

		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::apply();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::help( 'screen.contactmanager.edit' );
		mosMenuBar::endTable();
	}
}

$tasker =& new contactToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>