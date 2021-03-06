<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of template styles.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesViewStyles extends JView
{
	protected $state;
	protected $items;
	protected $pagination;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		$this->_setToolbar();
		parent::display($tpl);
	}

	/**
	 * Setup the Toolbar.
	 */
	protected function _setToolbar()
	{
		$state	= $this->get('State');
		$canDo	= TemplatesHelper::getActions();
		$isSite	= ($state->get('filter.client_id') == 0);

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('style.edit');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('styles.sethome', 'default.png', 'default_f2.png', 'Templates_Toolbar_Set_Home', true);
		}
		JToolBarHelper::title(JText::_('Templates_Manager_Styles'), 'thememanager');
		if ($canDo->get('core.create') && $isSite) {
			JToolBarHelper::addNew('styles.duplicate', 'Templates_Toolbar_Save_Copy');
		}
		if ($canDo->get('core.delete') && $isSite) {
			JToolBarHelper::deleteList('', 'styles.delete');
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_templates');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.templates');
	}
}
