<?php
/**
* @version $Id: toolbar.update_client.php,v 1.1 2005/08/25 14:14:54 johanjanssens Exp $
* @package Mambo Update Client
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
// update_client toolbar 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
//require_once( $mainframe->getPath( 'toolbar_html' ) );

class updateClientToolbar extends mosAbstractTasker {
	// Constructor
	function updateClientToolbar() {
		parent::mosAbstractTasker('default_menu');
		$this->registerTask('purgeCache', 'blank_menu');
		$this->registerTask('viewPackage', 'blank_menu');
		$this->registerTask('buildCache', 'blank_menu');
		$this->registerTask('listPackages', 'default_menu');
		$this->registerTask('update', 'blank_menu');
		$this->registerTask('upgrade', 'blank_menu');
		$this->registerTask('addRemoteSite', 'default_menu');
		$this->registerTask('editRemoteSite', 'default_menu');			

	}
	
	function blank_menu() {
		mosMenuBar::title("Mambo Update Client");
		mosMenuBar::startTable();		
		mosMenuBar::custom('listPackages','properties.png','properties_f2.png','List Packages', false );
		mosMenuBar::endTable();
	}
		
	function default_menu() {
		mosMenuBar::title("Mambo Update Client");	
		mosMenuBar::startTable();
		mosMenuBar::custom( 'buildCache', 'save.png', 'save_f2.png', 'Rebuild Cache', false );
		mosMenuBar::custom( 'update', 'reload.png', 'reload_f2.png', 'Update Package Lists', false );
		mosMenuBar::custom( 'upgrade', 'webworld.png', 'webworld_f2.png', 'Upgrade Installed Packages', true );
		mosMenuBar::endTable();
	}
}

$tasker =& new updateClientToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );

?>
