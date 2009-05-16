<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the JView class
jimport( 'joomla.application.component.view');

/**
 * Field View
 *
 * @package		Joomla.Administrator
 * @subpackage	Contacts
 */
class ContactsViewField extends JView
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$option = Jrequest::getCmd('option');
		
		$db = &JFactory::getDBO();
		$uri = &JFactory::getURI();
		$user = &JFactory::getUser();
		$model = &$this->getModel();

		// TODO: ACL
		/*if (!$user->authorize( 'com_contacts', 'manage fields' )) {
			$mainframe->redirect('index.php?option=com_contacts&controller=contact', JText::_('ALERTNOTAUTH'));
		}*/
		
		$lists = array();

		//get the field
		$field = &$this->get('data');
		$isNew = ($field->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id'))) {
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_( 'The Field' ), $field->title);
			$mainframe->redirect('index.php?option='. $option . '&controller=' . $controller, $msg);
		}

		// Edit or Create?
		if (!$isNew) {
			$model->checkout( $user->get('id') );
		} else {
			// initialise new record
			$field->published = 1;
			$field->approved = 1;
			$field->order = 0;
		}

		// build the html select list for ordering
		$query = "SELECT ordering AS value, title AS text"
			. " FROM #__contacts_fields"
			. " WHERE pos = '$field->pos'"
			. " ORDER BY ordering";

		$lists['ordering'] = JHTML::_('list.specificordering',  $field, $field->id, $query );

		// build the html select list for published
		$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $field->published );

		// build the html select list for access
		$lists['access'] = JHTML::_('list.accesslevel', $field);
		
		// build the html select list for type
		$types = array();
		//$types[] = JHTML::_('select.option', 'checkbox', 'Check Box (Single)' );
		//$types[] = JHTML::_('select.option', 'multicheckbox', 'Check Box (Muliple)' );
		//$types[] = JHTML::_('select.option', 'date', 'Date' );
		//$types[] = JHTML::_('select.option', 'select', 'Drop Down (Single Select)' );
		//$types[] = JHTML::_('select.option', 'multiselect', 'Drop Down (Multi-Select)' );
		$types[] = JHTML::_('select.option', 'text', 'Text Field' );
		$types[] = JHTML::_('select.option', 'textarea', 'Text Area' );	
		$types[] = JHTML::_('select.option', 'editor', 'Editor Text Area' );
		//$types[] = JHTML::_('select.option', 'number', 'Number Text' );		
		$types[] = JHTML::_('select.option', 'email', 'Email Address' );
		$types[] = JHTML::_('select.option', 'url', 'URL' );
		//$types[] = JHTML::_('select.option', 'radio', 'Radio Button' );
		$types[] = JHTML::_('select.option', 'image', 'Image' );
		
		$lists['type'] = JHTML::_('select.genericlist', $types, 'type', 'class="inputbox"', 'value', 'text', $field->type );
			
		// build the html select list for position
		$positions = array();
		$positions[] = JHTML::_('select.option', 'title', 'Title');
		$positions[] = JHTML::_('select.option', 'top', 'Top');
		$positions[] = JHTML::_('select.option', 'left', 'Left');
		$positions[] = JHTML::_('select.option', 'main', 'Main');
		$positions[] = JHTML::_('select.option', 'right', 'Right');
		$positions[] = JHTML::_('select.option', 'bottom', 'Bottom');
		
		$lists['pos'] = JHTML::_('select.genericlist', $positions, 'pos', 'class="inputbox"', 'value', 'text', $field->pos );
				
		//clean field data
		JFilterOutput::objectHTMLSafe($field, ENT_QUOTES, 'description');

		$file = JPATH_COMPONENT.DS.'models'.DS.'field.xml';
		$params = new JParameter( $field->params, $file );

		$this->assignRef('lists', $lists);
		$this->assignRef('field', $field);
		$this->assignRef('params', $params);

		parent::display($tpl);
	}
}