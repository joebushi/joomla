<?php
/**
* @version		$Id: $
* @package		Joomla
* @subpackage	Newsfeeds
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Newsfeeds component
 *
 * @static
 * @package		Joomla
 * @subpackage	Newsfeeds
 * @since 1.0
 */
class NewsfeedsViewNewsfeeds extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
	
		$db					=& JFactory::getDBO();
		$user				=& JFactory::getUser();
	
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',				'word' );
		$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",		'filter_catid',		0,				'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search',			'',				'string' );
		$search				= JString::strtolower( $search );
	
		// Get data from the model
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		// Is cache directory writable?
		$visible = 0;
		// check to hide certain paths if not super admin
		// TODO: Change this when ACLs more solid
		if ( $user->get('gid') == 25 ) {
			$visible = 1;
		}
		$cache = NewsfeedsViewNewsfeeds::writableCell( JPATH_SITE.DS.'cache', 0, '<strong>'. JText::_('Cache Directory') .'</strong> ', $visible );
		$lists['cache']		= $cache;
	
		// build list of categories
		$javascript = 'onchange="document.adminForm.submit();"';
		$lists['catid'] = JHTML::_('list.category',  'filter_catid', 'com_newsfeeds', $filter_catid, $javascript );
	
		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );
	
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
	
		// search filter
		$lists['search']= $search;
	
		$this->assignRef('user',		$user);
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}

	function writableCell( $folder, $relative=1, $text='', $visible=1 )
	{
		$writeable 		= '<b><font color="green">'. JText::_( 'Writable' ) .'</font></b>';
		$unwriteable 	= '<b><font color="red">'. JText::_( 'Unwritable' ) .'</font></b>';

		$result = '';
		$result .= '<tr>';
		$result .= '<td class="item">';
		$result .= $text;
		if ( $visible ) {
			$result .= $folder . '/';
		}
		$result .= '</td>';
		$result .= '<td >';
		if ( $relative ) {
			$result .= is_writable( "../$folder" ) ? $writeable : $unwriteable;
		} else {
			$result .= is_writable( "$folder" ) ? $writeable : $unwriteable;
		}
		$result .= '</td>';
		$result .= '</tr>';
		
		return $result;
	}
}