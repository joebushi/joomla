<?php
/**
* @version $Id: mod_unread.php 176 2005-09-13 11:58:24Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$query = "SELECT COUNT(*)"
. "\n FROM #__messages"
. "\n WHERE state = 0"
. "\n AND user_id_to = $my->id"
;
$database->setQuery( $query );
$unread = $database->loadResult();

if ($unread) {
	echo "<a href=\"index2.php?option=com_messages\" style=\"color: red; text-decoration: none;  font-weight: bold\">$unread <img src=\"images/mail.png\" align=\"middle\" border=\"0\" alt=\"Mail\" /></a>";
} else {
	echo "<a href=\"index2.php?option=com_messages\" style=\"color: black; text-decoration: none;\">$unread <img src=\"images/nomail.png\" align=\"middle\" border=\"0\" alt=\"Mail\" /></a>";
}
?>