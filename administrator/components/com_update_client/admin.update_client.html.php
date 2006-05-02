<?php
/**
* @version $Id: admin.update_client.html.php,v 1.2 2005/08/27 18:13:41 pasamio Exp $
* @package Mambo Update Client
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_update_server {

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
	function listPackages( &$rows, $pageNav, $option) {
		global $my, $acl;
        
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>
			
				<?php echo "Mambo Update Client" ?> <small><small>[ <?php echo "Package List" ?> ]</small></small>
			</th>
		</tr>
		</table>

		<table class="adminlist" >
		<tr>
			<th width="5">
			<?php echo 'ID'; ?>
			</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title">
			<?php echo 'Name'  ?>
			</th>
			<th nowrap="nowrap">
			<?php echo 'Type' ?>
			</th>
			<th align="center">
			<?php echo 'Installed Version'  ?>
			</th>
			<th align="center">
			<?php echo 'Recent Version'  ?>
			</th>
			<th align="center">
			<?php echo 'Last Updated'?>
			</th>
		  </tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_update_client&task=viewPackage&cid='. $row->cacheid;

//			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
//			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$checked = "<input id=\"cb$i\" name=\"cid[]\" value=\"{$row->cacheid}\" onclick=\"isChecked(this.checked);\" type=\"checkbox\">";
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
					<?php echo $checked; ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>" title="Edit Product">
					<?php echo htmlspecialchars($row->productname, ENT_QUOTES); ?>
					</a>
				</td>
				<td align="center">
					<?php echo $row->type; ?>
				</td>
				<td align="center">
					<?php 
					if($row->versionstring) {
						echo $row->versionstring; 
					} else {
						echo "Unknown Local Versions";
					}?>
				</td>
				<td align="center">
				<?php									
					if($row->updatedversion) {
						echo $row->updatedversion;
					} else {					  
						if(($row->lastupdate == "0000-00-00") || (is_null($row->lastupdate)) ) {
						  	echo "Haven't checked for updates.";
						} else {
							echo "No updates";
						}			
					} ?>
				</td>			
				<td align="center">
				<?php 
				if(($row->lastupdate == "0000-00-00") || (is_null($row->lastupdate)) ) {
					echo "N/A";
				} else {
					echo $row->lastupdate;
				}	 
				?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="type" value="package" />
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />	
		</form>
		<?php
	}
	
	function viewPackage($option, &$row) {
	?>
	<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>
				<?php echo "Mambo Update Client" ?> <small><small>[ <?php echo "View Package Details" ?> ]</small></small>
			</th>
		</tr>
	</table>	
	<!--Row? <?php print_r($row); ?>-->
	<table align="left">
		<tr><td>Name: </td><td><?php echo $row->productname; ?></td></tr>
		<tr><td>Type:</td><td><?php echo $row->type; ?></td></tr>
		<tr><td>Current Version:</td><td><?php echo $row->versionstring; ?></td></tr>			
		<tr><td>Updated Version:</td><td><?php echo $row->updatedversion; ?></td></tr>			
		<tr><td>XML-RPC Server:</td><td><?php echo $row->updateurl; ?></td></tr>
		<tr><td>Release Info URL:</td><td><a href="<?php echo $row->releaseinfourl; ?>" target="_blank"><?php echo $row->releaseinfourl; ?></a></td></tr>
		<tr><td>Download URL:</td><td><a href="<?php echo $row->downloadurl; ?>" target="_blank"><?php echo $row->downloadurl; ?></a></td></tr>
	</table>
	<form action="index2.php" method="post" name="adminForm">	
	<input type="hidden" name="productid" value="<?php echo $row->productid; ?>">
	<input type="hidden" name="type" value="product" />
	<input type="hidden" name="option" value="com_update_client" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<!--<input type="hidden" name="hidemainmenu" value="0" />-->
	<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
	</form>

	<?php
	}

	
	function returnToList($option) {
	if(!$option) { $option = 'com_update_client'; }
	?><p><a href="index2.php?option=<?php echo $option ?>">Return to a list of installed components.</a></p><?php
	}
	
	function emptyAdminForm($option) {
	?>
		<form action="index2.php" method="post" name="adminForm">
		<input type="hidden" name="type" value="package" />
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->
		<input type="hidden" name="redirect" value="" />	
		</form>
	<?php
	}
	

		
}
?>
