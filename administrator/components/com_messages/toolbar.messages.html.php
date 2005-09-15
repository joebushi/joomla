<?php
/**
* @version $Id: toolbar.messages.html.php 55 2005-09-09 22:01:38Z eddieajau $
* @package Joomla
* @subpackage Messages
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
* @subpackage Messages
*/
class TOOLBAR_messages {
	function _VIEW() {
		mosMenuBar::startTable();
		mosMenuBar::customX('reply', 'restore.png', 'restore_f2.png', 'Reply', false );
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _EDIT() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'save', 'Send' );
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.messages.edit' );
		mosMenuBar::endTable();
	}

	function _CONFIG() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'saveconfig' );
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'cancelconfig' );
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.messages.conf' );
		mosMenuBar::endTable();
	}

	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.messages.inbox' );
		mosMenuBar::endTable();
	}
}
?>