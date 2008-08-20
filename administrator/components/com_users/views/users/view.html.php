<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	Users
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @static
 * @package		Joomla
 * @subpackage	Users
 * @since 1.0
 */
class UsersViewUsers extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		if($this->getLayout() == 'groups')
		{
			JSubMenuHelper::addEntry(JText::_('Users'), 'index.php?option=com_users&view=users');
			JSubMenuHelper::addEntry(JText::_('Groups'), 'index.php?option=com_users&view=users&layout=groups', true);
		} else {
			JSubMenuHelper::addEntry(JText::_('Users'), 'index.php?option=com_users&view=users', true);
			JSubMenuHelper::addEntry(JText::_('Groups'), 'index.php?option=com_users&view=users&layout=groups');
		}
		
		if($this->getLayout() == 'groups')
		{
			$this->_displayGroupList();
			return;
		}
		global $mainframe, $option;

		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'a.username LIKE '.$searchEscaped.' OR a.email LIKE '.$searchEscaped.' OR a.name LIKE '.$searchEscaped;
		}
		if ( $filter_logged == 1 )
		{
			$where[] = 's.userid = a.id';
		}
		else if ($filter_logged == 2)
		{
			$where[] = 's.userid IS NULL';
		}


		$filter = '';
		if ($filter_logged == 1 || $filter_logged == 2)
		{
			$filter = ' INNER JOIN #__session AS s ON s.userid = a.id';
		}

		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__users AS a'
		. $filter
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT a.*'
			. ' FROM #__users AS a'
			. $filter
			. $where
			. ' GROUP BY a.id'
			. $orderby
		;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();

		$n = count( $rows );
		$template = 'SELECT COUNT(s.userid)'
			. ' FROM #__session AS s'
			. ' WHERE s.userid = %d'
		;
		for ($i = 0; $i < $n; $i++)
		{
			$row = &$rows[$i];
			$query = sprintf( $template, intval( $row->id ) );
			$db->setQuery( $query );
			$row->loggedin = $db->loadResult();
		}

		// get list of Log Status for dropdown filter
		$logged[] = JHTML::_('select.option',  0, '- '. JText::_( 'Select Log Status' ) .' -');
		$logged[] = JHTML::_('select.option',  1, JText::_( 'Logged In' ) );
		$lists['logged'] = JHTML::_('select.genericlist',   $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_logged" );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display();
	}

	function _displayGroupList($tpl = null)
	{
		$document = JFactory::getDocument();
		JHTML::_('behavior.mootools');
		JHTML::_('behavior.modal');
		$document->addScript(JURI::root().'media/system/js/mootree_packed.js');
		$document->addStyleSheet(JURI::root().'media/system/css/mootree.css');
		$usergroups = new JAuthorizationUsergroup();
		$javascript = 'var tree;
			window.onload = function() {
	
			tree = new MooTreeControl({
				div: \'mytree\',
				grid: true,
				theme: \'../media/system/images/mootree.gif\',
				onSelect: function(node, state) {
					if (state) var request = new Ajax(\'index.php\', {method: \'post\',postBody: \'option=com_users&format=raw&view=groupdetail\'+node.data.url,onFailure:function(){}, onSuccess:function(response){$(\'detailuser\').setHTML( response );}}).request();
				}
			},{
				text: \'Root Node\',
				open: true
			});
			tree.adopt(\'groups\');
			tree.expand();
		}';
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'helper'.DS.'helper.php');
		$usergrouphelper = new UsersHelper();
		$document->addScriptDeclaration($javascript);
		$this->assignRef('usergroups', $usergroups);
		$this->assignRef('usergrouphelper', $usergrouphelper);
		parent::display($tpl);
	}
}