<?php
/**
* @version $Id: cpanel.php 3 2005-09-06 15:11:03Z akede $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Restricted access' );

?>
<table class="adminform">
<tr>
	<td width="55%" valign="top">
	   <?php mosLoadAdminModules( 'icon', 0 ); ?>
	</td>
	<td width="45%" valign="top">
		<div style="width=100%;">
			<form action="index2.php" method="post" name="adminForm">
			<?php mosLoadAdminModules( 'cpanel', 1 ); ?>
			</form>
		</div>
	</td>
</tr>
</table>