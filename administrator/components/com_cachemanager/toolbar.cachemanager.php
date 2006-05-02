<?php
/**
* @version $Id: toolbar.cachemanager.php,v 1.1 2005/08/25 14:14:12 johanjanssens Exp $
* @package Mambo
* @subpackage Cache Manager
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Contents Manager
 * @package Mambo
 * @subpackage Content
 */
class cachemanagerToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function cachemanagerToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'cache_manager' );
	}
	
	function cache_manager() {
		mosMenuBar::title( 'Cache Manager', 'addedit.png' );
		mosMenuBar::startTable();
		mosMenuBar::custom( 'cleancache', 'delete.png', 'delete_f2.png', 'Clean Selected', true );
		//mosMenuBar::custom( 'cleanallcache', 'delete.png', 'delete_f2.png', 'Clean All', false );
		mosMenuBar::custom( 'listcache', 'reload.png', 'reload_f2.png', 'Refresh', false );
		mosMenuBar::help( 'screen.cache.manager' );
		mosMenuBar::endTable();
	}
}

$tasker =& new cachemanagerToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>