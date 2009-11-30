<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Tracks list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class BannersControllerTracks extends JController
{
	/**
	 * Proxy for getModel.
	 */
	public function &getModel($name = 'Tracks', $prefix = 'BannersModel')
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Method to remove a record.
	 */
	public function delete()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get the model.
		$model = $this->getModel();
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$type = $app->getUserStateFromRequest($this->_context.'.filter.type', 'filter_type');
		$model->setState('filter.type', $type);

		$begin = $app->getUserStateFromRequest($this->_context.'.filter.begin', 'filter_begin', '', 'string');
		$model->setState('filter.begin', $begin);

		$end = $app->getUserStateFromRequest($this->_context.'.filter.end', 'filter_end', '', 'string');
		$model->setState('filter.end', $end);

		$categoryId = $app->getUserStateFromRequest($this->_context.'.filter.category_id', 'filter_category_id', '');
		$model->setState('filter.category_id', $categoryId);

		$clientId = $app->getUserStateFromRequest($this->_context.'.filter.client_id', 'filter_client_id', '');
		$model->setState('filter.client_id', $clientId);
		$model->setState('list.limit', 0);
		$model->setState('list.start', 0);

		$count = $model->getTotal();
		// Remove the items.
		if (!$model->delete()) {
			JError::raiseWarning(500, $model->getError());
		}
		else {
			$this->setMessage(JText::sprintf('JController_N_Items_deleted', $count));
		}

		$this->setRedirect('index.php?option=com_banners&view=tracks');
	}
}
