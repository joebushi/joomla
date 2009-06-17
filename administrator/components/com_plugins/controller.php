<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Base controller class for Plugins.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @version		1.6
 */
class PluginsController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display()
	{
		// Get the document object.
		$document = &JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName = JRequest::getWord('view', 'plugins');
		$vFormat = $document->getType();
		$lName = JRequest::getWord('layout', 'default');

		// Instantiate the view and model.
		if ($view = &$this->getView($vName, $vFormat))
		{
			switch ($vName)
			{
				case 'plugin':
					$model = &$this->getModel('plugin');
					break;

				case 'plugins':
				default:
					$model = &$this->getModel('plugins');
					break;
			}

			// Configure the view.
			$view->setModel($model, true);
			$view->setLayout($lName);
			$view->assignRef('document', $document);

			// Display the view.
			$view->display();
		}
	}
}