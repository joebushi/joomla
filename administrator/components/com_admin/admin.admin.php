<?php
/**
* @version $Id: admin.admin.php,v 1.1 2005/08/25 14:14:12 johanjanssens Exp $
* @package Mambo
* @subpackage Admin
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

mosFS::load( '@admin_html' );

switch ($task) {

	case 'redirect':
		$goto = trim( strtolower( mosGetParam( $_REQUEST, 'link' ) ) );
		if ($goto == 'null') {
			$msg = $_LANG->_( 'WARNASSLINKITEM' );
			mosRedirect( 'index2.php?option=com_admin&task=listcomponents', $msg );
			exit();
		}
		$goto = str_replace( "'", '', $goto );
		mosRedirect($goto);
		break;

	case 'sysinfo':
		HTML_admin_misc::system_info();
		break;

	case 'help':
		HTML_admin_misc::help();
		break;

	case 'cancel':
		mosRedirect( 'index2.php' );
		break;

	case 'changelog';
		HTML_admin_misc::changelog();
		break;

	case 'cpanel':
	default:
		HTML_admin_misc::controlPanel();
		break;
}
?>