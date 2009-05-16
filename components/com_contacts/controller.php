<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * Contacts Component Controller
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	Contacts
 */
class ContactsController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$document = &JFactory::getDocument();

		$viewName = JRequest::getCmd('view');
		$viewType = $document->getType();

		$view = &$this->getView($viewName, $viewType);

		$model = &$this->getModel($viewName);
		if (!JError::isError($model)) {
			$view->setModel($model, true);
		}

		$view->assign('error', $this->getError());
		$view->display();
	}

	/**
	 * Method to send an email to a contact
	 *
	 * @static
	 * @since 1.0
	 */
	function submit() {
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
		
		$user = &JFactory::getUser();
		$model = &$this->getModel('Contact');
		
		if ($model->mailTo($user)) {
			$msg = JText::_( 'Thank you for your e-mail');
			$contact = $model->getData($user->get('aid', 0));
			$link = JRoute::_('index.php?option=com_contacts&view=contact&id='.$contact->id, false);
			$this->setRedirect($link, $msg);
		} else {
			$this->setError($model->getError());
			$this->display();
		}
	}
}
?>