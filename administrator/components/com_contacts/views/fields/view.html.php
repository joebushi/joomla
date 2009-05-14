<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import the JView class
jimport('joomla.application.component.view');

/**
 * Field View
 */
class ContactsViewFields extends JView
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();
		$user = & JFactory::getUser();
		
		// TODO: ACL
		/*if (!$user->authorize( 'com_contacts', 'manage fields' )) {
			$mainframe->redirect('index.php?option=com_contacts&controller=contact', JText::_('ALERTNOTAUTH'));
		}*/

		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'f.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );
		
		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}    
	
}


?>