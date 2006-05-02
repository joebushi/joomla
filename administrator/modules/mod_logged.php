<?php
/**
* @version $Id: mod_logged.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_list_limit;

mosFS::load( '@pageNavigationAdmin' );

$limit 			= $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mosConfig_list_limit );
$limitstart 	= $mainframe->getUserStateFromRequest( 'view_mod_logged', 'limitstart', 0 );

// hides Administrator or Super Administrator from list depending on usertype
$and = '';
if ( $my->gid == 24 ) {
	$and = "\n AND userid <> '25'";
}
if ( $my->gid == 23 ) {
	$and = "\n AND userid <> '25'";
	$and .= "\n AND userid <> '24'";
}

// get the total number of records
$query = "SELECT COUNT( * )"
. "\n FROM #__session"
. "\n WHERE userid <> 0"
. $and
. "\n ORDER BY usertype, username"
;
$database->setQuery( $query );
$total = $database->loadResult();

// page navigation
$pageNav = new mosPageNav( $total, $limitstart, $limit );

$query = "SELECT *"
. "\n FROM #__session"
. "\n WHERE userid <> 0"
. $and
. "\n ORDER BY usertype, username"
;
$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
$rows = $database->loadObjectList();

?>
<table class="adminlist">
<thead>
<tr>
    <th colspan="4">
		<?php echo $_LANG->_( 'Currently Logged in Users' ); ?>
	</th>
</tr>
</thead>
<tfoot>
<tr>
	<th colspan="4" align="center">
		<?php echo $pageNav->getPagesLinks(); ?>
	</th>
</tr>
<tr>
	<td colspan="4" align="center">
		<?php echo $_LANG->_( 'Display Num' ) ?>
		<?php echo  $pageNav->getLimitBox() ?>
		<?php echo $pageNav->getPagesCounter() ?>
	</td>
</tr>
</tfoot>

<tbody>
<?php
$k = 0;
$i = 0;
foreach ( $rows as $row ) {
	if ( $acl->acl_check( 'com_users', 'manage', 'users', $my->usertype ) ) {
		$link 	= 'index2.php?option=com_users&amp;task=editA&amp;id='. $row->userid;
		$name 	= '<a href="'. $link .'" title="'. $_LANG->_( 'Edit User Information' ) .'" class="editlink">'. $row->username .'</a>';
	} else {
		$name 	= $row->username;
	}
	?>
	<tr class="row<?php echo $k; ?>">
		<td width="10">
			<?php echo $pageNav->rowNumber( $i ); ?>
		</td>
		<td>
			<?php echo $name;?>
		</td>
		<td width="200">
			<?php echo $row->usertype;?>
		</td>
		<?php
		if ( $acl->acl_check( 'com_users', 'manage', 'users', $my->usertype ) ) {
			?>
			<td width="20">
				<a href="index2.php?option=com_users&amp;task=flogout&amp;id=<?php echo $row->userid; ?>" title="<?php echo $_LANG->_( 'Force User Logout' ); ?>">
					<img src="images/publish_x.png" width="12" height="12" border="0" alt="Logout" title="<?php echo $_LANG->_( 'Force User Logout' ); ?>" />
				</a>		
			</td>
			<?php
		}
		?>
	</tr>
	<?php
	$i++;
	$k = 1 - $k;	
}
?>
</tbody>
</table>

<input type="hidden" name="option" value="" />