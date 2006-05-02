<?php
/**
* @version $Id: admin.update_server.html.php,v 1.1 2005/08/25 14:14:54 johanjanssens Exp $
* @package Mambo Update Server
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
	function listProducts( &$rows, $pageNav) {
		global $my, $acl;
        
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>
			
				<?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Product List" ?> ]</small></small>
			</th>
		</tr>
		</table>

		<table class="adminlist" >
		<tr>
			<th width="5">
			<?php echo 'Num'; ?>
			</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th>Product ID</th>
			<th class="title">
			<?php echo 'Name'  ?>
			</th>
			<th nowrap="nowrap">
			<?php echo 'Published' ?>
			</th>
			<th align="center">
			<?php echo 'Product URL / Product Details URL'  ?>
			</th>
		  </tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_update_server&task=editProduct&cid='. $row->productid;

//			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
//			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$checked = "<input id=\"cb$i\" name=\"cid[]\" value=\"{$row->productid}\" onclick=\"isChecked(this.checked);\" type=\"checkbox\">";
			$task = $row->published ? 'unpublish' : 'publish';
         $img = $row->published ? 'tick.png' : 'publish_x.png';
         $alt = $row->published ? 'Published' : 'Unpublished';

			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
					<?php echo $checked; ?>
				</td>
				<td align="center"><?php echo $row->productid; ?></td>
				<td>
					<a href="<?php echo $link; ?>" title="Edit Product">
					<?php echo htmlspecialchars($row->productname, ENT_QUOTES); ?>
					</a>
				</td>
				<td width="10%" align="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                                <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td align="center">
					<?php 
					if($row->producturl) {
						echo $row->producturl; 
					} else {
						echo "No URL";
					}
					echo "<br>";				
					if($row->productdetailsurl) {
						echo $row->productdetailsurl;
					} else {
					  echo "No URL"; 
					} ?>
				</td>			
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="type" value="product" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>
		<?php
	}


	function editProduct($row) {
	?>
	                <form action="index2.php" method="post" name="adminForm">

			                <table class="adminheading">
					                <tr>
							                        <th class="edit" rowspan="2" nowrap>

										                                <?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Edit Product" ?> ]</small></small>
														                        </th>
																	                </tr>
																			                </table>


		<table align="left">
			<tr><td>Product Name: </td><td><input type="text" name="productname" value="<?php echo $row->productname; ?>"></td></tr>
			<tr><td>Product Type: </td><td><input type="text" name="producttype" value="<?php echo $row->producttype ?>"></td></tr>
			<tr><td>Product Description:</td><td><textarea name="productdescription" cols="80" rows="5"><?php echo $row->productdescription; ?></textarea></td></tr>
			<tr><td>Product XML-RPC Server:</td><td><input type="text" name="producturl" value="<?php echo $row->producturl; ?>" width="100"></td></tr>
			<tr><td>Product Details URL:</td><td><input type="text" name="productdetailsurl" value="<?php echo $row->productdetailsurl; ?>" width="100"></td></tr>
		</table>
		<input type="hidden" name="productid" value="<?php echo $row->productid; ?>">
		<input type="hidden" name="published" value="<?php echo $row->published; ?>">	
		<input type="hidden" name="type" value="product" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->		
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>

	<?php
	}

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
	function listReleases( &$rows, $pageNav) {
		global $my, $acl;
        
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>
			
				<?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Releases" ?> ]</small></small>
			</th>
		</tr>
		</table>

		<table class="adminlist" >
		<tr>
			<th width="5">
			<?php echo 'Num'; ?>
			</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title">
			<?php echo 'Product + Release'  ?>
			</th>
			<th>Release ID</th><th>Published</th>
			<th align="center">
			<?php echo 'Version'  ?>
			</th>
			<th align="center">
			<?php echo 'Release Date' ?>
			</th>
		  </tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$link 	= 'index2.php?option=com_update_server&task=editRelease&cid='. $row->releaseid;
			$checked = "<input id=\"cb$i\" name=\"cid[]\" value=\"{$row->releaseid}\" onclick=\"isChecked(this.checked);\" type=\"checkbox\">";
			$task = $row->published ? 'unpublish' : 'publish';
         $img = $row->published ? 'tick.png' : 'publish_x.png';
         $alt = $row->published ? 'Published' : 'Unpublished';
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
					<?php echo $row->releasetitle; ?>
					</a>
				</td>
				<td align="center"><?php echo $row->releaseid; ?></td>
				<td width="10%" align="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                                <img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td align="center">
					<?php echo $row->versionstring; ?>					
				</td>			
				<td align="center">
					<?php echo $row->releasedate; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="type" value="release" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="listreleases" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>
		<?php
	}



	
	function editRelease($row, $products) {
	?>
	                <form action="index2.php" method="post" name="adminForm">

			                <table class="adminheading">
					                <tr>
							                        <th class="edit" rowspan="2" nowrap>

										                                <?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Edit Release" ?> ]</small></small>
														                        </th>
																	                </tr>
																			                </table>


		<table align="left">
			<tr><td>Product</td><td><?php echo $products; ?></td></tr>
			<tr><tD>Release Title</td><td><input name="releasetitle" id="releasetitle" value="<?php echo $row->releasetitle; ?>"></td></tr>
			<tr><td>Release Description</td><td><textarea cols="50" rows="5" name="releasedescription" id="releasedescription"><?php echo $row->releasedescription; ?></textarea></td></tr>
			<tr><td>Release Extended Information URL:</td><td><input type="text" name="releasesurl" id="releasesurl"></td></tr> 
			<tr><TD>Release Changelog</TD><td><textarea cols="50" rows="5" name="releasechangelog" id="releasechangelog"><?php echo $row->releasechangelog; ?></textarea></td></tr>
			<tr><TD>Release Notes</TD><td><textarea cols="50" rows="5" name="releasenotes" id="releasenotes"><?php echo $row->releasenotes; ?></textarea></td></tr>
			<tr><TD>Version</TD><td><input name="versionstring" id="versionstring" value="<?php echo $row->versionstring; ?>"></td></tr>
			<tr><TD>Upgrade URL (Installable Package):</TD><td><input name="updateurl" id="updateurl" value="<?php echo $row->updateurl; ?>"></td></tr>
			<tr><TD>Release URL (Installable Package):</TD><td><input name="releaseurl" id="releaseurl" value="<?php echo $row->releaseurl; ?>"></td></tr>
			<tr><TD>Release Date (MySQL YYYY-MM-DD format):</TD><td><input name="releasedate" id="releasedate" value="<?php echo $row->releasedate; ?>"></td></tr>
		</table>
		<input type="hidden" name="releaseid" value="<?php echo $row->releaseid; ?>">
		<input type="hidden" name="published" value="<?php echo $row->published; ?>">	
		<input type="hidden" name="type" value="release" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->		
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>

	<?php
	
	}

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
	function listRemoteSites( &$rows, $pageNav) {
		global $my, $acl;
        
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>
			
				<?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Remote Sites" ?> ]</small></small>
			</th>
		</tr>
		</table>

		<table class="adminlist" >
		<tr>
			<th width="5">
			<?php echo 'Num'; ?>
			</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title">
			<?php echo 'Name'  ?>
			</th>			
			<th align="center">
			<?php echo 'XML-RPC URL'  ?>
			</th>
		  </tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_update_server&task=editRemoteSite&cid='. $row->remotesiteid;
			$checked = "<input id=\"cb$i\" name=\"cid[]\" value=\"{$row->remotesiteid}\" onclick=\"isChecked(this.checked);\" type=\"checkbox\">";
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
					<?php echo htmlspecialchars($row->remotesitename, ENT_QUOTES); ?>
					</a>
				</td>
				<td align="center">					
					<?php 
					if($row->remotesiteurl) {
						echo $row->remotesiteurl;
					} else {
					  echo "No URL"; 
					} ?>
				</td>			
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="type" value="remotesite" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="listremotesites" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>
		<?php
	}



	
	function editRemoteSite($row) {
	?>
	                <form action="index2.php" method="post" name="adminForm">

			                <table class="adminheading">
					                <tr>
							                        <th class="edit" rowspan="2" nowrap>

										                                <?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Remote Site" ?> ]</small></small>
														                        </th>
																	                </tr>
																			                </table>


		<table align="left">
			<tr><td>Remote Site Name: </td><td><input type="text" name="remotesitename" value="<?php echo $row->remotesitename; ?>" size="30" maxlength="30"></td></tr>
			<tr><td>Remote Site URL:</td><td><input type="text" name="remotesiteurl" value="<?php echo $row->remotesiteurl; ?>" size="60" maxlength="200"></td></tr>
		</table>
		<input type="hidden" name="remotesiteid" value="<?php echo $row->remotesiteid; ?>">
		<input type="hidden" name="type" value="remotesite" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="editremotesite" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->		
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>

	<?php
	
	}

	
	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
	function listDependencies( &$rows, $pageNav) {
		global $my, $acl;
        
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit" rowspan="2" nowrap>
			
				<?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Dependencies" ?> ]</small></small>
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="5">
			<?php echo 'Num'; ?>
			</th>
			<th width="5">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title" >
			<?php echo 'Product Release' ?>
			</th>
			<th class="title" >
			<div align="center"><?php echo 'Dependant upon'  ?></div>
			</th>			
			<th class="title">
			<div align="center"><?php echo 'Using site' ?></div>
			</th>
		  </tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_update_server&task=editDependency&cid='. $row->dependencyid;
			$checked = "<input id=\"cb$i\" name=\"cid[]\" value=\"{$row->dependencyid}\" onclick=\"isChecked(this.checked);\" type=\"checkbox\">";
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
					<?php echo $checked; ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>" title="Edit Dependency">
					<?php echo htmlspecialchars($row->productname . " " . $row->releasetitle, ENT_QUOTES); ?>
					</a>
				</td>
				<td align="center">					
					<?php 
					echo $row->depprodname . " " . $row->depversionstring; 
					 ?>
				</td>		
				<td align="center">
					<?php
					echo $row->remotesitename;
					?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="type" value="dependency" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="listdependencies" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>
		<?php
	}



	
	function editDependency($row,$release,$remotesite, $productselect,$versionselect) {
	?>
		<script language="JavaScript" >
		<!--
		function setProduct()
{	
	document.adminForm.depprodname.value = document.adminForm.dbdepproductname.options[document.adminForm.dbdepproductname.selectedIndex].value;
}

		function setVersion()
{		
	document.adminForm.depversionstring.value = document.adminForm.dbdepversionstring.options[document.adminForm.dbdepversionstring.selectedIndex].value;
}
		-->
		</script>
	                <form action="index2.php" method="post" name="adminForm">

			                <table class="adminheading">
					                <tr>
							                        <th class="edit" rowspan="2" nowrap>

										                                <?php echo "Mambo Update Server" ?> <small><small>[ <?php echo "Edit Dependency" ?> ]</small></small>
														                        </th>
																	                </tr>
																			                </table>


		<table align="left">
			<tr><td>Current Release: </td><td colspan="3"><?php echo $release; ?></td></tr>
			<tr><td>Dependency Product Name:</td><td><input type="text" name="depprodname" value="<?php echo $row->depprodname; ?>" ></td><td>Use Local Product:</td><td><?php echo $productselect ?></td></tr>
			<tr><TD>Dependency Version String:</TD><td><input type="text" name="depversionstring" value="<?php echo $row->depversionstring; ?>"></td><td>Use Local Version:</td><td><?php echo $versionselect ?></td></tr></tr>
			<tr><TD>Dependency Remote Site:</TD><td colspan="3"><?php echo $remotesite; ?></td></tr>
		</table>
		<input type="hidden" name="dependencyid" value="<?php echo $row->dependencyid ?>">
		<input type="hidden" name="type" value="dependency" />
		<input type="hidden" name="option" value="com_update_server" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<!--<input type="hidden" name="hidemainmenu" value="0" />-->		
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		</form>

	<?php
	
	}
	
		
}
?>
