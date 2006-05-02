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
<link href="<?php echo $mosConfig_live_site;?>/templates/rhuk_planetfall/css/template_css.css" rel="stylesheet" type="text/css" />
</head>
<body class="page_bg">
<a name="up" id="up"></a>
<?php
@$collspan_offset = ( mosCountModules( 'right' ) + mosCountModules( 'user2' ) and !$_REQUEST['task'] == 'edit' ) ? 2 : 1;
?>
<div align="center" >
	<table width="798" border="0" cellpadding="0" cellspacing="0" class="big_frame">
	<tr>
		<td colspan="<?php echo $collspan_offset + 1; ?>">
		<img src="<?php echo $mosConfig_live_site; ?>/templates/rhuk_planetfall/images/top_bar.jpg" width="798" height="9" alt=""/>
		</td>
	</tr>
	<tr>
		<td colspan="<?php echo $collspan_offset; ?>" class="header">
		<img src="<?php echo $mosConfig_live_site; ?>/templates/rhuk_planetfall/images/spacer.png" width="646" height="9" alt=""/>
		<br />
		</td>
		<td class="top_right_box" style="width: 151px; padding-left: 5px;" valign="top">
			<table cellpadding="0" cellspacing="1" border="0" width="120" class="contentpaneopen">
			<tr>
				<td class="contentheading" width="145">
				Search
				</td>
			</tr>
			<tr>
				<td>
				<form action='index.php' method='post'>
				<div class="searchblock" id="searchblock">
				Enter Keywords:
				<input size="15" class="inputbox" type="text" name="searchword" style="width:128px;" value="search..."  onblur="if(this.value=='') this.value='search...';" onfocus="if(this.value=='search...') this.value='';" />
				<input type="hidden" name="option" value="search" />
					<div align="left">
					<input type="submit" value="GO" class="button" style="width:35px;" />
					</div>
				</div>
				</form>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="<?php echo $collspan_offset+1; ?>" class="silver_box" style="height:26px;">
		<div id="silver_toolbar">
			<div id="silver_date">
			<?php echo mosCurrentDate(); ?>
			</div>
			<div id="silver_menu">
			<?php mosLoadModules ( 'user3' ); ?>
			</div>
			<div style="clear: both;"></div>
		</div>
		</td>
	</tr>
	<tr>
		<td valign="top" class="content_box">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr valign="top">
				<td>
				<!-- main content area -->
				<div class="contentblock" id="contentblock" style="width:<?php echo 635 - ( 151 * ( $collspan_offset-1 ) )   ?>">
					<?php mosPathWay(); ?>
					<br />
					<?php
					if ( mosCountModules( 'top' ) ) {
						mosLoadModules ( 'top' );
					}
					mosMainBody();
					?>
					<br />
				</div>
				<?php
				if ( mosCountModules( 'bottom' ) ) { ?>
					<div class="footerblock" id="footerblock">
					<?php mosLoadModules ( 'bottom' ); ?>
					<br />
					</div>
					<?php
				}
				?>
				</td>
			</tr>
			</table>
		</td>
		<?php
		if ( ( mosCountModules( 'right' ) + mosCountModules( 'user2' ) ) and @$_REQUEST['task'] != 'edit' ) {
			?>
			<td valign="top" class="middle_box" width="151" style="width:151px">
			<div id="middle_box">
				<?php
				if ( mosCountModules( 'right' ) ) {
					?>
					<div class="rightblock" id="rightblock" style="width:145px">
					<?php mosLoadModules ( 'right' ); ?>
					</div>
					<?php
				}
				if ( mosCountModules( 'user2' ) ) {
					?>
					<div class="user2block" id="user2block" style="width:143px">
					<?php mosLoadModules ( 'user2' ); ?>
					</div>
					<?php
				} ?>
			</div>
			</td>
			<?php
		}
		?>
		<td valign="top" class="right_box" width="151" style="width: 151px">
		<div id="right_box">
			<!-- far right menu -->
			<div class="leftblock" id="leftblock" style="width:143px">
			<?php mosLoadModules ( 'left' ); ?>
			</div>
			<div class="user1block" id="user1block" style="width:143px">
			<?php
			if ( mosCountModules( "user1" ) ) {
				mosLoadModules ( 'user1' );
			}
			?>
			</div>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="<?php echo $collspan_offset+1; ?>">
		<img src="<?php echo $mosConfig_live_site; ?>/templates/rhuk_planetfall/images/top_bar.jpg" width="798" height="9" alt="" />
		</td>
	</tr>
	</table>
	<?php mosLoadModules( 'footer', -1 );?>
</div>
<?php mosLoadModules( 'debug', -1 );?>
</body>
</html>
