<?php
/**
* @version $Id: toolbar.poll.php 187 2005-09-13 15:31:57Z stingrey $
* @package Joomla
* @subpackage Polls
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ($task) {
	case 'new':
		TOOLBAR_poll::_NEW();
		break;

	case 'edit':
		$cid = mosGetParam( $_REQUEST, 'cid', array(0) );
		if (!is_array( $cid )) {
			$cid = array(0);
		}

		$query = "SELECT published"
		. "\n FROM #__polls"
		. "\n WHERE id = $cid[0]"
		;
		$database->setQuery( $query );
		$published = $database->loadResult();

		$cur_template = $mainframe->getTemplate();

		TOOLBAR_poll::_EDIT( $cid[0], $cur_template );
		break;

	case 'editA':
		$id = mosGetParam( $_REQUEST, 'id', 0 );

		$query = "SELECT published"
		. "\n FROM #__polls"
		. "\n WHERE id = $id"
		;
		$database->setQuery( $query );
		$published = $database->loadResult();

		$cur_template = $mainframe->getTemplate();

		TOOLBAR_poll::_EDIT( $id, $cur_template );
		break;

	default:
		TOOLBAR_poll::_DEFAULT();
		break;
}
?>