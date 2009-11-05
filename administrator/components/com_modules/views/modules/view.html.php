<?php
/**
 * @version		$Id$
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
	public $state;
	public $items;
	public $pagination;

	public function display($tpl = null)
	{
		// Get data from the model
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',			$state);
		$this->assignRef('items',			$items);
		$this->assignRef('pagination',		$pagination);

		parent::display($tpl);
		$this->_setToolbar();
		$this->_setSubmenu();
	}

	/**
	 * Display the submenu
	 */
	protected function _setSubmenu()
	{
		JSubMenuHelper::addEntry(JText::_('Site'), 'index.php?option=com_modules&client_id=0', ($this->state->get('client.id') == 0));
		JSubMenuHelper::addEntry(JText::_('Administrator'), 'index.php?option=com_modules&client_id=1', ($this->state->get('client.id') == 1));
	}

	/**
	 * Display the toolbar
	 */
	protected function _setToolbar()
	{
		$user	= JFactory::getUser();

		JToolBarHelper::title(JText::_('Module Manager'), 'module.png');
		if ($user->authorise('core.create', 'com_modules')) {
			JToolBarHelper::addNew('module.add');
		}
		if ($user->authorise('core.edit', 'com_modules')) {
			JToolBarHelper::editList('module.edit');
		}
			JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'Copy', true);
			JToolBarHelper::divider();
		if ($user->authorise('core.edit.state', 'com_modules'))
		{
			JToolBarHelper::publishList('modules.publish');
			JToolBarHelper::unpublishList('modules.unpublish');
		}
			JToolBarHelper::divider();
		if ($user->authorise('core.delete', 'com_modules')) {
			JToolBarHelper::deleteList('', 'modules.delete');
		}
		if ($user->authorise('core.admin', 'com_modules'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_modules');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.weblink');
	}
}