<?php
/**
* @version $Id: url.menu.php,v 1.1 2005/08/25 14:14:34 johanjanssens Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $task;
global $cid, $menutype, $option;

switch ($task) {
	case 'url':
		// this is the new item, ie, the same name as the menu `type`
		url_menu::edit( 0, $menutype, $option );
		break;

	case 'edit':
		url_menu::edit( $cid[0], $menutype, $option );
		break;
		
	case 'save':
	case 'apply':
		saveMenu( $option, $task );
		break;
}
?>