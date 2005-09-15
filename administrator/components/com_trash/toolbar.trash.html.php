<?php
/**
* @version $Id: toolbar.trash.html.php 55 2005-09-09 22:01:38Z eddieajau $
* @package Joomla
* @subpackage Trash
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Trash
*/
class TOOLBAR_Trash {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::custom('restoreconfirm','restore.png','restore_f2.png','Restore', true);
		mosMenuBar::spacer();
		mosMenuBar::custom('deleteconfirm','delete.png','delete_f2.png','Delete', true);
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.trashmanager' );
		mosMenuBar::endTable();
	}

	function _DELETE() {
		mosMenuBar::startTable();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _SETTINGS() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::back();
		mosMenuBar::endTable();
	}

}
?>