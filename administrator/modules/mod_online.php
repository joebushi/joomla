<?php
/**
* @version $Id: mod_online.php 176 2005-09-13 11:58:24Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$session_id = mosGetParam( $_SESSION, 'session_id', '' );

// Get no. of users online not including current session
$query = "SELECT COUNT( session_id )"
. "\n FROM #__session"
. "\n WHERE session_id <> '$session_id'"
;
$database->setQuery($query);
$online_num = intval( $database->loadResult() );

echo $online_num . " <img src=\"images/users.png\" align=\"middle\" alt=\"Users Online\" />";
?>