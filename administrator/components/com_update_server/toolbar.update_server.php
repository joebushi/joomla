<?php
/**
* @version $Id: toolbar.update_server.php,v 1.1 2005/08/25 14:14:54 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// update_server toolbar 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
//require_once( $mainframe->getPath( 'toolbar_html' ) );

class updateServerToolbar extends mosAbstractTasker {
	// Constructor
	function updateServerToolbar() {
		parent::mosAbstractTasker('listmenu');
		$this->registerTask('listProducts','listmenu');
		$this->registerTask('listRemoteSites','listmenu');
		$this->registerTask('listReleases','listmenu');
		$this->registerTask('listDependencies','listmenu');

		$this->registerTask('new','editMenu');
		$this->registerTask('newProduct','editMenu');
		$this->registerTask('newRemoteSite','editMenu');		
		$this->registerTask('newRelease','editMenu');
		$this->registerTask('newDependency','editMenu');
		$this->registerTask('edit','editMenu');
		$this->registerTask('editProduct','editMenu');
		$this->registerTask('editRemoteSite','editMenu');
		$this->registerTask('editRelease','editMenu');
		$this->registerTask('editDependency','editMenu');	


	}
	
	function editMenu() { 
		mosMenuBar::title("Mambo Update Server - Edit");
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
	function listMenu() { 
		mosMenuBar::title("Mambo Update Server");
		mosMenuBar::startTable();
		mosMenuBar::addNew();
		mosMenuBar::deleteList();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::endTable();
	}
}

$tasker =& new updateServerToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
	
?>
