<?php
/**
* @version $Id: sef.php 1553 2005-12-24 17:04:09Z Saka $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// backward compatibility
if (!defined( '_404' )) {
	define( '_404', 'We\'re sorry but the page you requested could not be found.' );
}
if (!defined( '_404_RTS' )) {
	define( '_404_RTS', 'Return to site' );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>404 - <?php echo $mosConfig_sitename; ?></title>
		<link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/templates/css/offline.css" type="text/css" />
		<link rel="shortcut icon" href="<?php echo $mosConfig_live_site; ?>/images/favicon.ico" />
		<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
	</head>
	<body>
		<table width="550" align="center" class="outline">
			<tr>
				<td width="60%" height="50" align="center">
				<img src="<?php echo $mosConfig_live_site; ?>/images/joomla_logo_black.jpg" alt="Joomla! Logo" align="middle" />
				</td>
			</tr>
			<tr>
				<td width="39%" align="center">
					<h2><?php echo _404;?></h2>
					<h3><a href="<?php echo $mosConfig_live_site; ?>">
						<?php echo _404_RTS;?></a></h3>
					<br />
					Error 404
				</td>
			</tr>
		</table>
	</body>
</html>
