<?php
/**
* @version $Id: content.php,v 1.1 2005/08/25 14:18:10 johanjanssens Exp $
* @package Mambo
* @subpackage Content
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class comContent {
	
	function show( $option, $task )
	{
		comContent::_display( $option, $task );
	}
	
	function _display( $option, $task )
	{
		global $mainframe, $acl, $my;
		
		////$task = 		$mainframe->getUserStateFromRequest( "task", 'task' );
		//$option = 		$mainframe->getUserStateFromRequest( "option", 'option' );
		$Itemid = 		$mainframe->getUserStateFromRequest( "Itemid", 'Itemid' );
		
		$id			= intval( mosGetParam( $_REQUEST, 'id', 0 ) );
		$sectionid 	= intval( mosGetParam( $_REQUEST, 'sectionid', 0 ) );
		$catid 		= intval( mosGetParam( $_REQUEST, 'catid', 0 ) );
		$pop 		= intval( mosGetParam( $_REQUEST, 'pop', 0 ) );
		$limit 		= intval( mosGetParam( $_REQUEST, 'limit', '' ) );
		$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
		
		// Editor usertype check
		$access = new stdClass();
		$access->canEdit 	= $acl->acl_check( 'action', 'edit', 'users', $my->usertype, 'content', 'all' );
		$access->canEditOwn = $acl->acl_check( 'action', 'edit', 'users', $my->usertype, 'content', 'own' );
		$access->canPublish = $acl->acl_check( 'action', 'publish', 'users', $my->usertype, 'content', 'all' );
		
		$gid = $my->gid;
		
		if( $task == 'frontpage')
		{
			comContent::_frontpage( $gid, $access, $pop );
		}
		elseif( stristr( $task, 'section' ) )
		{
			mosFS::load( 'components/com_content/content.section.php' );
			$ccSection = new comContentSection();
			$ccSection->show( $id, $gid, $access, $pop, $option, $Itemid, $task );
		}
		elseif( stristr( $task, 'category' ) )
		{
			mosFS::load( 'components/com_content/content.category.php' );
			$ccCategory = new comContentCategory();
			$ccCategory->show($id, $gid, $access, $pop, $option, $Itemid, $task, $sectionid, $limit, $limitstart);
		}
		else
		{
			mosFS::load( 'components/com_content/content.item.php' );
			$ccItem = new comContentItem();
			$ccItem->show( $id, $gid, $access, $pop, $option, $Itemid, $task );
		}
	}
	
	function _frontpage( $gid, &$access, $pop )
	{
		mosFS::load( 'components/com_content/content.utils.php' );
		mosFS::load( 'components/com_content/content.html.php' );
		
		$ccUtils = new comContentUtils();
		$ccUtils->frontpage( $gid, $access, $pop );
	}
}

comContent::show( $option, $task );
?>