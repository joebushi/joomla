<?php
/**
* @version $Id: toolbar.languages.php,v 1.1 2005/08/25 14:14:24 johanjanssens Exp $
* @package Mambo
* @subpackage Languages
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Language Manager
 * @package Mambo
 * @subpackage Languages
 */
class languagesToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function languagesToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'listLangs' );

		// set task level access control
		$this->setAccessControl( 'com_languages', 'manage' );

		// additional mappings
		$this->registerTask( 'trawlOptions', 'trawl' );
		$this->registerTask( 'refreshFiles', 'editXML' );
		$this->registerTask( 'installOptions', 'install' );
		$this->registerTask( 'installUpload', 'install' );
	}

	function listLangs() {
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'Language Manager' ), 'langmanager.png', 'index2.php?option=com_languages' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'packageOptions', 'downloads.png', 'downloads_f2.png', $_LANG->_( 'Package' ), false );
		mosMenuBar::deleteList();
		mosMenuBar::editList( 'edit' );
		mosMenuBar::help( 'screen.languages.manager' );
		mosMenuBar::endTable();
	}
	function trawl() {
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'Trawl' ), 'langmanager.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'trawl', 'search.png', 'search_f2.png', $_LANG->_( 'Trawl' ), false );
		mosMenuBar::help( 'screen.languages.trawl' );
		mosMenuBar::endTable();
	}
	function edit(){
		global $_LANG;
		
		$cid 	= mosGetParam( $_POST, 'cid', array() );
		$file 	= $cid[0];
		
		mosMenuBar::title( $_LANG->_( 'Edit File' ), 'langmanager.png' );

		mosMenuBar::startTable();
		mosMenuBar::save( 'save' );
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.languages.editsource' );
		mosMenuBar::endTable();
	}
	function editXML(){
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'Edit XML File' ), 'langmanager.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'refreshFiles', 'reload.png', 'reload_f2.png', $_LANG->_( 'Refresh Files' ), false );
		mosMenuBar::save( 'saveXML' );
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.languages.editxml' );
		mosMenuBar::endTable();
	}
	function listFiles(){
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'List Packages' ), 'langmanager.png' );

		mosMenuBar::startTable();
		mosMenuBar::deleteList( '', 'deletePackage' );
		mosMenuBar::help( 'screen.languages.packages' );
		mosMenuBar::endTable();
	}

	function install() {
		global $_LANG;
		
		mosMenuBar::title( $_LANG->_( 'Language Installer' ), 'install.png' );

		mosMenuBar::startTable();
		mosMenuBar::help( 'screen.languages.installer' );
		mosMenuBar::endTable();
	}

	function packageOptions() {
		global $_LANG;

		mosMenuBar::title( $_LANG->_( 'Package' ), 'install.png' );

		mosMenuBar::startTable();
		mosMenuBar::custom( 'package', 'downloads.png', 'downloads_f2.png', $_LANG->_( 'Package' ), false );
		mosMenuBar::help( 'screen.package' );
		mosMenuBar::endTable();
	}

}

$tasker =& new languagesToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>