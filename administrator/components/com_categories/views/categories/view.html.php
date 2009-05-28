<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Categories view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class CategoryViewCategories extends JView
{
	public $state;
	public $items;
	public $pagination;

	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @since	1.0
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

		// Build the published state filter options.
		$options	= array();
		$options[]	= JHTML::_('select.option', '*', 'Any');
		$options[]	= JHTML::_('select.option', '1', 'Published');
		$options[]	= JHTML::_('select.option', '0', 'Unpublished');
		$options[]	= JHTML::_('select.option', '-2', 'Trash');

		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('filter_state',$options);

		parent::display($tpl);
		$this->_setToolbar();
	}

	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		JToolBarHelper::title('Category: '.JText::_('Categories'));

		$state = $this->get('State');
		JToolBarHelper::custom('category.publish', 'publish.png', 'publish_f2.png', 'Publish', true);
		JToolBarHelper::custom('category.unpublish', 'unpublish.png', 'unpublish_f2.png', 'Unpublish', true);
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'category.delete');
		}
		else {
			JToolBarHelper::trash('category.trash');
		}
		JToolBarHelper::custom('category.edit', 'edit.png', 'edit_f2.png', 'Edit', true);
		JToolBarHelper::custom('category.edit', 'new.png', 'new_f2.png', 'New', false);
	}
}