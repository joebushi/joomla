<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the JView class
jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Contacts component
 *
 * @static
 * @package		Joomla
 * @subpackage	Contacts
 * @since 1.0
 */
class ContactsViewContact extends JView
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$user =& JFactory::getUser();
		$model =& $this->getModel();

		// TODO: ACL
		/*if (!$user->authorize( 'com_contacts', 'manage contacts' )) {
			$mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
		}*/	
		
		$lists = array();

		//get the contact
		$contact = &$this->get('data');
		$isNew = ($contact->id < 1);
		
		//get the fields
		$fields =& $this->get('fields');
		if($fields == null){
			$query = "SELECT title, type, params FROM #__contacts_fields WHERE published = 1 ORDER BY pos, ordering";
			$db->setQuery( $query );
			$fields = $db->loadObjectList();
			foreach($fields as $field){
				$field->show_contact = 1;
				$field->show_directory = 1;
				$field->data = null;
			}
		}
		
		//get the categories
		$categories =& $this->get('categories');

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The Contact' ), $contact->name );
			$mainframe->redirect( 'index.php?option='. $option . '&controller=' . $controller, $msg );
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout( $user->get('id') );
		}
		else
		{
			// initialise new record
			$contact->published = 1;
			$contact->approved 	= 1;
			$contact->order 	= 0;
		}

		// build the html list for categories
		$query = "SELECT id AS value, title AS text"
		        . " FROM #__categories"
		        . " WHERE extension = 'com_contacts'"
		        . " AND published = 1"
		        . " ORDER BY lft";
		$db->setQuery( $query );
		$cat = $db->loadObjectList();
		
		$select = array();
		foreach ($categories as $category){
			$select[] = $category->id;	
		}
		
		$lists['category'] = JHTML::_('select.genericlist', $cat, 'categories[]', 'multiple="multiple" class="inputbox" size="'. count($cat).'"', 'value', 'text', $select );
		
		$i = 0;
		foreach ($categories as $category) { 	
			$query = "SELECT c.name AS text, map.ordering AS value "
				."FROM jos_contacts_contacts c "
				."LEFT JOIN jos_contacts_con_cat_map map ON map.contact_id = c.id "
				."WHERE c.published = 1 AND map.category_id = '$category->id' ORDER BY ordering";
	
			$order = JHTML::_('list.genericordering', $query );
            $lists['ordering'.$i] = JHTML::_('select.genericlist', $order, 'ordering[]', 'class="inputbox" size="1"', 'value', 'text', intval( $category->ordering ) );
			$i++;
		}			

		//$lists['ordering'] = JHTML::_('list.specificordering',  $contact, $contact->id, $query );

		// build the html select list for published
		$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $contact->published );

		// build the html select list for access
		$lists['access'] = JHTML::_('list.accesslevel', $contact);
				
		
		// build the html for the booleanlist Show / Hide Field
		$i = 0;
		foreach ($fields as $field){
			$field->params = new JParameter($field->params);
			$field->name = JFilterOutput::stringURLSafe($field->title);
			
			$lists['showContact'.$i] = JHTML::_('select.booleanlist',  'showContact'.$i, 'class="inputbox"', $field->show_contact, 'show', 'hide' );
			$lists['showDirectory'.$i] = JHTML::_('select.booleanlist',  'showDirectory'.$i, 'class="inputbox"', $field->show_directory, 'show', 'hide' );
			$i++;
		}
		
		// build list of users
		$lists['user_id'] = JHTML::_('list.users',  'user_id', $contact->user_id, 1, null, 'name', 0 );
		
		//clean contact data
		JFilterOutput::objectHTMLSafe( $contact, ENT_QUOTES, 'description' );

		$file 	= JPATH_COMPONENT.DS.'models'.DS.'contact.xml';
		$params = new JParameter( $contact->params, $file );
		 
		$this->assignRef('lists',		$lists);
		$this->assignRef('contact',		$contact);
		$this->assignRef('fields',		$fields);
		$this->assignRef('categories',	$categories);
		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
}
