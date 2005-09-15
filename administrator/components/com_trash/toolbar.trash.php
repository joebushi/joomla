<?php
/**
* @version $Id: toolbar.trash.php 182 2005-09-13 14:09:32Z stingrey $
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

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ($task) {

	case 'settings':
		TOOLBAR_Trash::_SETTINGS();
		break;

	case 'restoreconfirm':
	case 'deleteconfirm':
		TOOLBAR_Trash::_DELETE();
		break;

	default:
		TOOLBAR_Trash::_DEFAULT();
		break;
}
?>