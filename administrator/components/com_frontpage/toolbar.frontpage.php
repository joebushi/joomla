<?php
/**
* @version $Id: toolbar.frontpage.php,v 1.1 2005/08/25 14:14:23 johanjanssens Exp $
* @package Mambo
* @subpackage Content
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Frontpage Manager
 * @package Mambo
 * @subpackage Frontpage
 */
class frontpageToolbar extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function frontpageToolbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'view' );

		// set task level access control
		//$this->setAccessControl( 'com_weblinks', 'manage' );
	}

	function view() {
		global $_LANG;		

		mosMenuBar::title( $_LANG->_( 'Frontpage Manager' ), 'frontpage.png', 'index2.php?option=com_frontpage' );

		mosMenuBar::startTable();
		// TODO
		//mosMenuBar::popup('', 'previewfrontpage', 'preview.png', 'Preview', true);
		mosMenuBar::custom('remove','delete.png','delete_f2.png','Remove', true);
		mosMenuBar::help( 'screen.frontpage' );
		mosMenuBar::endTable();
	}
}

$tasker =& new frontpageToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>