<?php

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// xml prolog

echo '<?xml version="1.0" encoding="'. $_LANG->iso() .'"?' .'>';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_LANG->isoCode();?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_LANG->iso(); ?>" />

<?php mosShowHead(); ?>

<link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/css/template_css.css" />

</head>

<body>



<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee">

<tr>

	<td width="6" bgcolor="#FFFFFF">

	<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/pixel.png" width="1" height="1" alt="spacer" />

	</td>

	<td valign="top" class="greybg">

		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

		<tr>

			<td bgcolor="#FFFFFF">

			<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/pixel.png" width="1" height="6" alt="spacer" /></td>

			<td width="180" height="6" valign="bottom" bgcolor="#FFFFFF">

			<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/search_01.png" width="180" height="3" alt="search" /></td>

		</tr>

		<tr>

			<td rowspan="3" valign="bottom">

			<p>

			<font class="title">

			<?php echo $mosConfig_sitename; ?>

			</font>

			<br />

			<font class="subtitle">

			Your slogan here

			</font>

			</p>

			</td>

			<td height="17" valign="top" class="greybg">

			<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/search_02.png" width="180" height="17" alt="Search" />

			</td>

		</tr>

		<tr>

			<td height="10">

			<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/pixel.png" width="1" height="10" alt="spacer" />

			</td>

		</tr>

		<tr>

			<td valign="top">

			<?php mosLoadModules ( 'user4', -1 ); ?>

			</td>

		</tr>

		</table>

		<!-- This is the vertical menu. Change the links as needed or delete the script from this line if you dont use it-->

		<table width="100%" border="0" cellpadding="0" cellspacing="0">

		<tr>

			<td>

			<?php mosLoadModules ( 'user3', -1 ); ?>

			</td>

		</tr>

		</table>

		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

		<tr>

			<td width="180" class="newsflash" valign="top">

			<?php mosLoadModules( 'top', 1 ); ?>

			</td>

			<td align="right" valign="top">

			<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/image_01.jpg" width="600" height="182" alt="header" />

			</td>

		</tr>

		</table>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">

		<tr>

			<td width="170" valign="top" bgcolor="#eeeeee">

			<?php mosLoadModules ( 'left' ); ?>

			</td>

			<td width="6" bgcolor="#FFFFFF">&nbsp;</td>

			<td valign="top">

			<br />

				<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">

				<tr>

					<td class="pathway">

					<?php mosPathWay(); ?>

					</td>

				</tr>

				</table>

			<br />

				<table width="98%" border="0" align="center" cellpadding="4" cellspacing="0">

				<tr>

					<td class="mainpage">

					<?php mosMainBody(); ?>

					</td>

				</tr>

				</table>

			</td>

			<td class="mainpage-bkg">

			<img src="<?php echo $mosConfig_live_site;?>/templates/JavaBean/images/pixel.png" width="1" height="1" alt="spacer"/>

			</td>

			<td width="150" valign="top">

				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

				<tr>

					<td style="padding-right:5px;">

					<?php mosLoadModules ( 'right' ); ?>

					</td>

				</tr>

				</table>

			<br />

			</td>

		</tr>

		</table>

		

		<table width="100%" border="0" cellspacing="0" cellpadding="0">

		<tr>

			<td align="center" valign="middle" bgcolor="#999999">

			<?php mosLoadModules( 'footer', -1 );?>

			</td>

		</tr>

		</table>

	</td>

	<td width="6" bgcolor="#FFFFFF">

	<img src="<?php echo $mosConfig_live_site; ?>/templates/JavaBean/images/pixel.png" width="1" height="1" alt="spacer"/>

	</td>

</tr>

</table>

<?php mosLoadModules( 'debug', -1 );?>

</body>

</html>