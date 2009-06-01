<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Menus Menu Items View.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class MenusViewItems extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}

	/**
	 * Build the default toolbar.
	 *
	 * @access	protected
	 * @return	void
	 * @since	1.0
	 */
	function buildDefaultToolBar()
	{
		JToolBarHelper::title(JText::_('Menus_Title_Items'));

		JToolBarHelper::custom('item.add', 'new.png', 'new_f2.png', 'New', false);
		JToolBarHelper::custom('item.edit', 'edit.png', 'edit_f2.png', 'Edit', true);
		JToolBarHelper::deleteList('', 'item.delete');

		JToolBarHelper::divider();

		// We can't use the toolbar helper here because there is no generic popup button.
		$bar = &JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', 'config', 'JToolbar_Options', 'index.php?option=com_menus&view=config&tmpl=component', 570, 500);

		//JToolBarHelper::help('index', true);
	}
}
