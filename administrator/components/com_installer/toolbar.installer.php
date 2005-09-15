<?php
/**
* @version $Id: toolbar.installer.php 186 2005-09-13 15:15:44Z stingrey $
* @package Joomla
* @subpackage Installer
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ($task){
	case 'new':
		TOOLBAR_installer::_NEW();
		break;

	default:
		$element = mosGetParam( $_REQUEST, 'element', '' );
		if ($element == 'component' || $element == 'module' || $element == 'mambot') {
			TOOLBAR_installer::_DEFAULT2();
		} else {
			TOOLBAR_installer::_DEFAULT();
		}
		break;
}
?>