<?php
/**
* @version $Id: cpanel.php,v 1.1 2005/08/25 14:18:07 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

?>
<div id="datacellcpanel">
<table class="adminform">
<tr>
	<td valign="top">
		<?php mosLoadAdminModules( 'icon', 0 ); ?>
	</td>
	<td width="47%" valign="top">
		<div style="width=100%;">
			<form action="index2.php" method="post" name="adminForm">
				<?php mosLoadAdminModules( 'cpanel', 1 ); ?>
			</form>
		</div>
	</td>
</tr>
</table>
</div>
<?php
global $_VERSION;

if ( $_VERSION->DEV_STATUS == 'Dev' ) {
	// DEV ONLY
	?>
	<style type="text/css">
	s {
		color: red;
	}
	.todo {
		background-color: #E9EFF5;
		text-align: left;
		width: 60%;
		height: 300px;
		overflow: auto;
		color: blue;
		border: 1px solid #999999;
		padding: 20px;
	}
	hr {
		border: 1px dotted black;
	}
	span.todotitle {
		font-weight: bold;
		color: black;
	}
	</style>
	<strong>
		CVS TESTER NOTES
	</strong>
	<br/>
	<pre class="todo">
		<?php
		readfile( $GLOBALS['mosConfig_absolute_path'].'/TODO.php' );
		?>
	</pre>
	<?php
}
?>