<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the JController class
jimport('joomla.application.component.controller');

/**
 * Contacts Contact Controller
 *
 */
class ContactsControllerContact extends JController
{
	/**
	 * Display the list of contacts
	 */	
	public function display()
    {
		JRequest::setVar('view', 'contacts');
		parent::display();
    }
    
	public function add()
	{
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'contact');
		JRequest::setVar('edit', false);

		// Checkout the contact
		$model = $this->getModel('Contact');
		$model->checkout();

		parent::display();
    }
    
	public function edit()
	{
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'contact');
		JRequest::setVar('edit', true);

		// Checkout the contact
		$model = $this->getModel('Contact');
		$model->checkout();

		parent::display();
    }
    
	public function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = &$this->getModel('Contact');

		if ($id = $model->store($post)) {
			$msg = JText::_('Contact Saved');
		} else {
			$msg = $model->getError();//JText::_( 'Error Saving Contact' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		$link = 'index.php?option=com_contacts&controller=contact&task=edit&cid[]='. $id;
		$this->setRedirect($link, $msg);    	
    }
    
	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$post = JRequest::get('post');
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('Contact');

		if ($model->store($post)) {
			$msg = JText::_('Contact Saved');
		} else {
			$msg = $model->getError(); //JText::_( 'Error Saving Contact' );
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		$link = 'index.php?option=com_contacts&controller=contact';
		$this->setRedirect($link, $msg);
	}
    
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_('Select an item to delete'));
		}

		$model = $this->getModel('Contact');
		if(!$model->delete($cid)) {
			$msg = $model->getError();
		}

		$this->setRedirect('index.php?option=com_contacts&controller=contact', $msg);
	}


	public function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_('Select an item to publish'));
		}

		$model = $this->getModel('Contact');
		if(!$model->publish($cid, 1)) {
			$msg = $model->getError();
		}

		$this->setRedirect('index.php?option=com_contacts&controller=contact', $msg);
	}

	public function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_('Select an item to unpublish'));
		}

		$model = $this->getModel('Contact');
		if(!$model->publish($cid, 0)) {
			$msg = $model->getError();
		}

		$this->setRedirect('index.php?option=com_contacts&controller=contact', $msg);
	}

	public function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Checkin the contact
		$model = $this->getModel('Contact');
		$model->checkin();

		$this->setRedirect('index.php?option=com_contacts&controller=contact');
	}
	
	/**
	* Save the item(s) to the menu selected
	*/
	public function accesspublic()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get some variables from the request
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('Contact');
		if (!$model->setAccess($cid, 0)) {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_contacts&controller=contact', $msg);
	}

	/**
	 * Save the item(s) to the menu selected
	 */
	public function accessregistered()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get some variables from the request
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('Contact');
		if (!$model->setAccess($cid, 1)) {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_contacts&controller=contact', $msg);
	}

	/**
	* Save the item(s) to the menu selected
	*/
	public function accessspecial()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get some variables from the request
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('Contact');
		if(!$model->setAccess($cid, 2)) {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_contacts&controller=contact', $msg);
	}	
}

?>