<?php
/**
* @version $Id: component_item_link.menu.php 55 2005-09-09 22:01:38Z eddieajau $
* @package Joomla
* @subpackage Menus
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

mosAdminMenus::menuItem( $type );

switch ( $task ) {
	case 'component_item_link':
		// this is the new item, ie, the same name as the menu `type`
		component_item_link_menu::edit( 0, $menutype, $option );
		break;

	case 'edit':
		component_item_link_menu::edit( $cid[0], $menutype, $option );
		break;

	case 'save':
	case 'apply':
		saveMenu( $option, $task );
		break;
}
?>