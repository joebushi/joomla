<?php
/**
 * @version		$Id: view.html.php 12276M 2009-09-12 12:29:42Z (local) $
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Modules component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @since 1.0
 */
class ModulesViewModules extends JView
{
	protected $client;
	protected $filter;
	protected $pagination;
	protected $rows;
	protected $user;

	function display($tpl = null)
	{
		// Get data from the model
		$rows		= & $this->get('Data');
		$total		= & $this->get('Total');
		$pagination = & $this->get('Pagination');
		$filter		= & $this->get('Filter');
		$client		= & $this->get('Client');

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('rows',		$rows);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('filter',		$filter);
		$this->assignRef('client',		$client);

		parent::display($tpl);
		$this->_setToolbar();
	}
	
	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		JToolBarHelper::title(JText::_('Module Manager'), 'module.png');
		JToolBarHelper::publishList('modules.publish');
		JToolBarHelper::unpublishList('modules.unpublish');
		JToolBarHelper::custom('modules.copy', 'copy.png', 'copy_f2.png', 'Copy', true);
		JToolBarHelper::deleteList('', 'modules.remove');		
		JToolBarHelper::editListX('module.edit');
		JToolBarHelper::addNewX('module.add');
		JToolBarHelper::help('screen.modules');
	}
}
