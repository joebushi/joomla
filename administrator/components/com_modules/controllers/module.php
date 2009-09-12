<?php
/**
 * @version		$Id
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Module controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @version		1.6
 */
class ModulesControllerModule extends JController
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('apply',	'save');
		$this->registerTask('save2copy','save');		
	}

	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_modules', false));
	}

	/**
	 * Saves the module after an edit form submit
	 *
	 * @return	void
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$cache	= & JFactory::getCache();
		$cache->clean('com_content');

		$data	= JRequest::get('post');
		// fix up special html fields
		$data['content']   = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);

		$model	= $this->getModel('Module');
		$task	= $this->getTask();
		
		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Check-in the original row.
			if (!$model->checkin())
			{
				// Check-in failed, go back to the item and display a notice.
				$this->setMessage(JText::sprintf('JError_Checkin_save %s', $model->getError()), 'error');
				$this->setRedirect(JRoute::_('index.php?option=com_modules&view=module&client='. $client->id, false));
				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data['id']	= 0;
			$task		= 'apply';
		}

		// Attempt to save the data.		
		if (!$model->save($data)) {
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JError_Save_failed %s', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_modules&view=module&client='. $client->id, false));
			return false;			
		}
		
		// Check-in the row
		if (!$model->checkin())
		{
			// Check-in failed, go back to the row and display a notice.
			$this->setMessage(JText::sprintf('JError_Checkin_saved %s', $model->getError()), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_modules&view=module&client='. $client->id, false));
			return false;
		}
		
		$this->setMessage(JText::_('Item saved'));
		$client = $model->getClient();
		switch ($task)
		{
			case 'save':
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_modules&view=modules&client='. $client->id, false));
				break;
				
			case 'apply':
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_modules&client='. $client->id .'&task=module.edit&cid[]='. $model->_id, false));
				break;
		}
	}

	/**
	* Displays a list to select the creation of a new module
	*/
	function add()
	{
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'selecttype');
		JRequest::setVar('edit', false);

		// Checkout the module
		$model = $this->getModel('Module');
		$model->checkout();

		parent::display();
	}
	
	/**
	 * Method to edit an existing module
	 *
	 * @return	void
	 */
	function edit()
	{
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'module');
		JRequest::setVar('edit', true);

		// Checkout the module
		$model = $this->getModel('Module');
		$model->checkout();

		parent::display();
	}

	/**
	 * Cancels an edit operation
	 */
	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('module');
		$model->checkin();

		$client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$this->setRedirect('index.php?option=com_modules&client='.$client->id);
	}

	function preview()
	{
		JRequest::setVar('view', 'prevw');

		$document = &JFactory::getDocument();
		$document->setTitle(JText::_('Module Preview'));

		parent::display();
	}
	
}