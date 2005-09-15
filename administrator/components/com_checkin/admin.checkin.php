<?php
/**
* @version $Id$
* @package Joomla
* @subpackage Checkin
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

if (!$acl->acl_check( 'administration', 'config', 'users', $my->usertype )) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}
?>
<table class="adminheading">
<tr>
	<th class="checkin">
		Global Check-in
	</th>
</tr>
</table>
<table class="adminform">
<tr>
	<th class="title">
		Database Table
	</th>
	<th class="title">
		# of Items
	</th>
	<th class="title">
		Checked-In
	</th>
	<th class="title">
	</th>
</tr>
<?php
$lt = mysql_list_tables($mosConfig_db);
$k = 0;
while (list($tn) = mysql_fetch_array( $lt )) {
	// make sure we get the right tables based on prefix
	if (!preg_match( "/^".$mosConfig_dbprefix."/i", $tn )) {
		continue;
	}
	$lf = mysql_list_fields($mosConfig_db, "$tn");
	$nf = mysql_num_fields($lf);

	$foundCO = false;
	$foundCOT = false;
	$foundE = false;
	for ($i = 0; $i < $nf; $i++) {
		$fname = mysql_field_name($lf, $i);
		if ( $fname == 'checked_out') {
			$foundCO = true;
		} else if ( $fname == 'checked_out_time') {
			$foundCOT = true;
		} else if ( $fname == 'editor') {
			$foundE = true;
		}
	}

	if ($foundCO && $foundCOT) {
		if ($foundE) {
			$query = "SELECT checked_out, editor"
			. "\n FROM $tn"
			. "\n WHERE checked_out > 0"
			;
			$database->setQuery( $query );
		} else {
			$query = "SELECT checked_out"
			. "\n FROM $tn"
			. "\n WHERE checked_out > 0"
			;
			$database->setQuery(  );
		}
		$res = $database->query();
		$num = $database->getNumRows( $res );

		if ($foundE) {
			$query = "UPDATE $tn"
			. "\n SET checked_out = 0, checked_out_time = '00:00:00', editor = NULL"
			. "\n WHERE checked_out > 0"
			;
			$database->setQuery( $query );
		} else {
			$query = "UPDATE $tn"
			. "\n SET checked_out = 0, checked_out_time = '0000-00-00 00:00:00'"
			. "\n WHERE checked_out > 0"
			;
			$database->setQuery( $query );
		}
		$res = $database->query();

		if ($res == 1) {
			if ($num > 0) {
				echo "<tr class=\"row$k\">";
				echo "\n	<td width=\"350\">Checking table - $tn</td>";
				echo "\n	<td width=\"150\">Checked in <b>$num</b> items</td>";
				echo "\n	<td width=\"100\" align=\"center\"><img src=\"images/tick.png\" border=\"0\" alt=\"tick\" /></td>";
				echo "\n	<td>&nbsp;</td>";
				echo "\n</tr>";
			} else {
				echo "<tr class=\"row$k\">";
				echo "\n	<td width=\"350\">Checking table - $tn</td>";
				echo "\n	<td width=\"150\">Checked in <b>$num</b> items</td>";
				echo "\n	<td width=\"100\">&nbsp;</td>";
				echo "\n	<td>&nbsp;</td>";
				echo "\n</tr>";
			}
			$k = 1 - $k;
		}
	}
}
?>
<tr>
	<td colspan="4">
		<b>Checked out items have now been all checked in</b>
	</td>
</tr>
</table>