<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 */
class UserControllerGroup extends JController
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'save2copy',	'save' );
		$this->registerTask( 'save2new',	'save' );
		$this->registerTask( 'apply',		'save' );
		$this->registerTask( 'unpublish',	'publish' );
		$this->registerTask( 'trash',		'publish' );
		$this->registerTask( 'orderup',		'ordering' );
		$this->registerTask( 'orderdown',	'ordering' );
	}

	/**
	 * Display the view
	 */
	function display()
	{
		JError::raiseWarning( 500, 'This controller does not implement a display method' );
	}

	/**
	 * Proxy for getModel
	 */
	function &getModel()
	{
		return parent::getModel( 'Group', 'UserModel', array( 'ignore_request' => true ) );
	}

	/**
	 * Method to edit a object
	 *
	 * Sets object ID in the session from the request, checks the item out, and then redirects to the edit page.
	 *
	 * @access	public
	 * @return	void
	 */
	function edit()
	{
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$id  = JRequest::getInt( 'id', @$cid[0] );

		$session = &JFactory::getSession();
		$session->set( 'users.group.id', $id );

		if ($id) {
			// Checkout item
			//$model = $this->getModel();
			//$model->checkout( $id );
		}
		$this->setRedirect( JRoute::_( 'index.php?option=com_users&view=group&layout=edit', false ) );
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item ID in the session to null, and then redirects to the list page.
	 *
	 * @access	public
	 * @return	void
	 */
	function cancel()
	{
		$session = &JFactory::getSession();
		// Clear the session of the item
		$session->set( 'users.group.id', null );

		$this->setRedirect( JRoute::_('index.php?option=com_users&view=groups', false ) );
	}

	/**
	 * Save the record
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken();

		// Get posted form variables.
		$input = JRequest::get('post');

		// Get the id of the item out of the session.
		$session	= &JFactory::getSession();
		$id			= (int) $session->get('users.group.id');
		$input['id'] = $id;

		// Get the extensions model and set the post request in its state.
		$model	= &$this->getModel();
		$result	= $model->save( $input );
		$msg	= JError::isError( $result ) ? $result->message : 'Saved';

		if ($this->_task == 'apply') {
			$session->set( 'users.redirect.id', $model->getState( 'id' ) );
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=group&layout=edit', false), JText::_($msg));
		}
		else if ($this->_task == 'save2new') {
			$session->set( 'users.group.id', null );
			$model->checkin($id);

			$this->setRedirect(JRoute::_('index.php?option=com_users&view=group&layout=edit', false), JText::_($msg));
		}
		else {
			$session->set( 'users.group.id', null );
			$model->checkin($id);

			$this->setRedirect(JRoute::_('index.php?option=com_users&view=groups', false), JText::_($msg));
		}
	}

	/**
	 * Deletes a user
	 */
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

			// Get items from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');

		if (empty( $cid )) {
			JError::raiseWarning(500, JText::_( 'No items selected' ));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport( 'joomla.utilities.arrayhelper' );
			JArrayHelper::toInteger( $cid );

			// Remove the items.
			if (!$model->delete($cid)) {
				JError::raiseWarning( 500, $model->getError() );
			}
		}

		$this->setRedirect( 'index.php?option=com_users&view=groups' );
	}
}
