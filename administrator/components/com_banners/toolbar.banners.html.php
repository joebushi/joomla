<?php
/**
* @version $Id: toolbar.banners.html.php 55 2005-09-09 22:01:38Z eddieajau $
* @package Joomla
* @subpackage Banners
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
* @subpackage Banners
*/
class TOOLBAR_banners {
	/**
	* Draws the menu for to Edit a banner
	*/
	function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::media_manager( 'banners' );
		mosMenuBar::spacer();
		mosMenuBar::save();
		mosMenuBar::spacer();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Close' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners.edit' );
		mosMenuBar::endTable();
	}
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::media_manager( 'banners' );
		mosMenuBar::addNewX();
		mosMenuBar::editListX();
		mosMenuBar::deleteList();
		mosMenuBar::help( 'screen.banners' );
		mosMenuBar::endTable();
	}
}

/**
* @package Joomla
*/
class TOOLBAR_bannerClient {
	/**
	* Draws the menu for to Edit a client
	*/
	function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::save( 'saveclient' );
		mosMenuBar::spacer();
		if ( $id ) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancelclient', 'Close' );
		} else {
			mosMenuBar::cancel( 'cancelclient' );
		}
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners.client.edit' );
		mosMenuBar::endTable();
	}
	/**
	* Draws the default menu
	*/
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::addNewX( 'newclient' );
		mosMenuBar::spacer();
		mosMenuBar::editListX( 'editclient' );
		mosMenuBar::spacer();
		mosMenuBar::deleteList( '', 'removeclients' );
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.banners.client' );
		mosMenuBar::endTable();
	}
}
?>