<?php
/**
* @version $Id$
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/
global $mosConfig_offset;

/// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$count 	= intval( $params->get( 'count', 20 ) );
$access = !$mainframe->getCfg( 'shownoauth' );
$now 	= date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 );

$query = "SELECT a.id AS id, a.title AS title, COUNT(b.id) as cnt"
. "\n FROM #__sections as a"
. "\n LEFT JOIN #__content as b ON a.id = b.sectionid"
. ( $access ? "\n AND b.access <= $my->gid" : '' )
. "\n AND ( b.publish_up = '0000-00-00 00:00:00' OR b.publish_up <= '$now' )"
. "\n AND ( b.publish_down = '0000-00-00 00:00:00' OR b.publish_down >= '$now' )"
. "\n WHERE a.scope = 'content'"
. "\n AND a.published = 1"
. ( $access ? "\n AND a.access <= $my->gid" : '' )
. "\n GROUP BY a.id"
. "\n HAVING COUNT( b.id ) > 0"
. "\n ORDER BY a.ordering"
. "\n LIMIT $count"
;
$database->setQuery( $query );
$rows = $database->loadObjectList();

if ( $rows ) {
	?>
	<ul>
	<?php
		foreach ($rows as $row) {
			$link = sefRelToAbs( "index.php?option=com_content&task=blogsection&id=". $row->id );
			?>
			<li>
				<a href="<?php echo $link;?>">
					<?php echo $row->title;?></a>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}
?>