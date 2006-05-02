<?php
/**
* @version $Id: update_server.html.php,v 1.1 2005/08/25 14:18:15 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_update_server {
	function listProducts($option,$Itemid,$rows) {
		?>
		<div class="componentheading">
		<?php echo "Mambo Update Server"; ?>
		</div> 
				
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="contentpane">
		<tr><Th width="60%" valign="top">Product Listing</Th></tr>
		<?php foreach($rows as $row) { ?>
		<tr>
			<td width="60%" valign="top" class="contentdescription">
			<a href="index.php?option=<?php echo $option ?>&Itemid=<?php echo $Itemid ?>&task=showProduct&productid=<?php echo $row->productid ?>">
			<?php
			echo  $row->productname;
			?>
			</a>
			</td>
		</tr>
		<?php } ?>
		</table>		
	<?php
	}
	
	function showProduct($option,$Itemid,$row,$releases) {
		?>
		<div class="componentheading">
		<?php echo "Mambo Update Server"; ?>
		</div> 
				
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="contentpane">
		<tr><Th valign="top" colspan="2"><?php echo  $row->productname ?></Th></tr>		
		<tr>
			<td valign="top" class="contentdescription" colspan="2">
			<?php echo  $row->productdescription ?>
			</td>
		</tr>
		<tR><TD width="150">Details:</TD><TD><a href="<?php echo $row->productdetailsurl ?>" target="_blank"><?php echo $row->productdetailsurl ?></a></TD></tR>
		<tR><TD width="150">XML-RPC Server:</TD><TD><?php echo $row->producturl ?></TD></tR>
		</table>		
		<br>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="contentpane">
		<tr><Th valign="top" colspan="2">Releases</Th></tr>		
		<?php foreach($releases as $release) { ?>
		<tr>
			<td valign="top" class="contentdescription" colspan="2">
			<a href="index.php?option=<?php echo $option ?>&Itemid=<?php echo $Itemid ?>&task=showRelease&releaseid=<?php echo $release->releaseid; ?>"><?php echo  $release->releasetitle ?> (<?php echo $release->versionstring ?>)</a>
			</td>
		</tr>
		<?php } 
		if(!count($releases)) {
		?>
		<tr><td valign="top" class="contentdescription" colspan="2">No releases found.</td></tr>
		<?php
		}
		?>
		</table>		
		
		<hr>
		<a href="index.php?option=<?php echo $option; ?>&Itemid=<?php echo $Itemid ?>">Product Listing</a>
	<?php
	}	
	
		function showRelease($option,$Itemid,$row) {
		?>
		<div class="componentheading">
		<?php echo "Mambo Update Server"; ?>
		</div> 
				
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="contentpane">
		<tr><Th valign="top" colspan="2"><a href="index.php?option=<?php echo $option ?>&Itemid=<?php echo $Itemid ?>&task=showProduct&productid=<?php echo $row->productid ?>"><?php echo $row->productname . " " . $row->releasetitle ?></a></Th></tr>		
		<tr>
			<td valign="top" class="contentdescription" colspan="2">
			<?php echo  $row->releasedescription ?>
			<hr>
			</td>
		</tr>
		<tr><th colspan="2">Details</th></tr>
		<tR><TD width="150">Releases URL:</TD><TD><a href="<?php echo $row->releasesurl ?>" target="_blank"><?php echo $row->releasesurl ?></a></TD></tR>
		<tR><TD width="150">Release Changelog:</TD><TD><?php echo $row->releasechangelog ?></TD></tR>
		<tR><TD width="150">Release Notes:</TD><TD><?php echo $row->releasenotes ?></TD></tR>		
		<tR><TD width="150">Version String:</TD><TD><?php echo $row->versionstring ?></TD></tR>		
		<tR><TD width="150">Update URL(XML-RPC):</TD><TD><?php echo $row->updateurl ?></TD></tR>
		<tR><TD width="150">Release URL (XML-RPC):</TD><TD><?php echo $row->releaseurl ?></TD></tR>
		<tR><TD width="150">Release Date:</TD><TD><?php echo $row->releasedate ?></TD></tR>
		</table>		
		<hr>
		<a href="index.php?option=<?php echo $option; ?>&Itemid=<?php echo $Itemid ?>">Product Listing</a>
	<?php
	}	

}	
?>
