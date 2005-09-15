<?php
/**
* @version $Id: toolbar.banners.php 55 2005-09-09 22:01:38Z eddieajau $
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

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ($task) {
	case 'newclient':
	case 'editclient':
	case 'editclientA':
		TOOLBAR_bannerClient::_EDIT();
		break;

	case 'listclients':
		TOOLBAR_bannerClient::_DEFAULT();
		break;

	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_banners::_EDIT();
		break;

	default:
		TOOLBAR_banners::_DEFAULT();
		break;
}
?>