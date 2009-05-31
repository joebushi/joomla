<?php
/**
 * @version		$Id$
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die;

/**
 * Setup controller for the Joomla Core Installer.
 * - JSON Protocol -
 *
 * @package		Joomla.Installation
 * @since		1.6
 */
class JInstallationControllerSetup extends JController
{
	function loadSampleData()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or $this->sendResponse(new JException(JText::_('Invalid_Token'), 403));

		// Get the setup model.
		$model = &$this->getModel('Setup', 'JInstallationModel', array('dbo' => null));

		// Get the options from the session.
		$vars = $model->getOptions();

		// Get the database model.
		$database = &$this->getModel('Database', 'JInstallationModel', array('dbo' => null));

		// Attempt to load the database sample data.
		$return = $database->installSampleData($vars);

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException($database->getError(), 500));
		}

		// Create a response body.
		$r = new JObject();
		$r->text = 'Sample Data Loaded Successfully.';

		// Send the response.
		$this->sendResponse($r);
	}

	function detectFtpRoot()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or $this->sendResponse(new JException(JText::_('Invalid_Token'), 403));

		// Get the posted config options.
		$vars = JRequest::getVar('vars', array(), 'post', 'array');

		// Get the setup model.
		$model = & $this->getModel('Setup');

		// Store the options in the session.
		$vars = $model->storeOptions($vars);

		// Get the database model.
		$filesystem = & $this->getModel('Filesystem');

		// Attempt to detect the Joomla root from the ftp account.
		$return = $filesystem->detectFtpRoot($vars);

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException($filesystem->getError(), 500));
		}

		// Create a response body.
		$r = new JObject();
		$r->root = $return;

		// Send the response.
		$this->sendResponse($r);
	}

	function verifyFtpSettings()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JRequest::checkToken('request') or $this->sendResponse(new JException(JText::_('Invalid_Token'), 403));

		// Get the posted config options.
		$vars = JRequest::getVar('vars', array(), 'post', 'array');

		// Get the setup model.
		$model = & $this->getModel('Setup');

		// Store the options in the session.
		$vars = $model->storeOptions($vars);

		// Get the database model.
		$filesystem = & $this->getModel('Filesystem');

		// Attempt to detect the Joomla root from the ftp account.
		$return = $filesystem->verifyFtpSettings($vars);

		// If an error was encountered return an error.
		if (!$return) {
			$this->sendResponse(new JException($filesystem->getError(), 500));
		}

		// Create a response body.
		$r = new JObject();
		$r->valid = $return;

		// Send the response.
		$this->sendResponse($r);
	}

	/**
	 * Method to handle a send a JSON response. The data parameter
	 * can be a JException object for when an error has occurred or
	 * a JObject for a good response.
	 *
	 * @access	public
	 * @param	object	JObject on success, JException on failure.
	 * @return	void
	 * @since	1.6
	 */
	function sendResponse($response)
	{
		// Check if we need to send an error code.
		if (JError::isError($response))
		{
			// Send the appropriate error code response.
			JResponse::setHeader('status', $response->getCode());
			JResponse::setHeader('Content-Type', 'application/json; charset=utf-8');
			JResponse::sendHeaders();
		}

		// Send the JSON response.
		echo json_encode(new JInstallationJsonResponse($response));

		// Close the application.
		$app = &JFactory::getApplication();
		$app->close();
	}
}

/**
 * Joomla Core Installation JSON Response Class
 *
 * @package		Joomla.Installation
 * @since		1.6
 */
class JInstallationJsonResponse
{
	function __construct($state)
	{
		// The old token is invalid so send a new one.
		$this->token = JUtility::getToken(true);

		// Check if we are dealing with an error.
		if (JError::isError($state))
		{
			// Prepare the error response.
			$this->error	= true;
			$this->header	= JText::_('Installation_Header_Error');
			$this->message	= $state->getMessage();
		}
		else
		{
			// Prepare the response data.
			$this->error	= false;
			$this->data		= $state;
		}
	}
}

// Set the error handler.
//JError::setErrorHandling(E_ALL, 'callback', array('JInstallationControllerSetup', 'sendResponse'));