<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the JController class
jimport('joomla.application.component.controller');

/**
 * Contacts Field Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	Contacts
 */
class ContactsControllerField extends JController
{
	/**
	 * Display the list of fields
	 */	
    public function display()
    {
    	JRequest::setVar('view', 'Fields');
        parent::display();
    }
    
    public function add()
    {
    	JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'field');
		JRequest::setVar('edit', false);

		// Checkout the field
		$model = $this->getModel('Field');
		$model->checkout();
		
		parent::display();
    }
    
    public function edit()
    {
    	JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'field');
		JRequest::setVar('edit', true);

		// Checkout the field
		$model = $this->getModel('Field');
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

		$model = &$this->getModel('Field');

		if ($id = $model->store($post)) {
			$msg = JText::_('Field Saved');
		} else {
			$msg = $model->getError();// JText::_( 'Error Saving Field' );
		}
		
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		$link = 'index.php?option=com_contacts&controller=field&task=edit&cid[]='. $id;
		$this->setRedirect($link, $msg);    	
    }
    
	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('Field');

		if ($model->store($post)) {
			$msg = JText::_( 'Field Saved' );
		} else {
			$msg = $model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		$link = 'index.php?option=com_contacts&controller=field';
		$this->setRedirect($link, $msg);
	}
    
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('Field');
		if($model->delete($cid)) {
			$msg = JText::_( 'Field(s) Deleted' );
		} else {
			$msg = $model->getError();// JText::_( 'Error Deleting Field(s)' );
		}

		$link = 'index.php?option=com_contacts&controller=field';
		$this->setRedirect($link, $msg);
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

		$model = $this->getModel('Field');
		if (!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_contacts&controller=field');
	}

	public function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_('Select an item to unpublish'));
		}

		$model = $this->getModel('Field');
		if (!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_contacts&controller=field' );
	}

	public function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Checkin the field
		$model = $this->getModel('Field');
		$model->checkin();

		$this->setRedirect('index.php?option=com_contacts&controller=field');
	}


	public function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model = $this->getModel('Field');
		$model->move(-1);

		$this->setRedirect('index.php?option=com_contacts&controller=field');
	}

	public function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model = $this->getModel('Field');
		$model->move(1);

		$this->setRedirect('index.php?option=com_contacts&controller=field');
	}

	public function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid 	= JRequest::getVar('cid', array(), 'post', 'array');
		$order 	= JRequest::getVar('order', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('Field');
		$model->saveorder($cid, $order);

		$msg = 'New ordering saved';
		$this->setRedirect('index.php?option=com_contacts&controller=field', $msg);
	}

	/**
	* Save the item(s) to the menu selected
	*/
	public function accesspublic()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get some variables from the request
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('Field');
		if (!$model->setAccess($cid, 0)) {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_contacts&controller=field');
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

		$model = $this->getModel('Field');
		if (!$model->setAccess($cid, 1)) {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_contacts&controller=field');
	}

	/**
	* Save the item(s) to the menu selected
	*/
	public function accessspecial()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get some variables from the request
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('Field');
		if (!$model->setAccess($cid, 2)) {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_contacts&controller=field');
	}
}
?>