<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Category view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @version		1.0
 */
class CategoryViewCategory extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		$state	= $this->get('State');
		$item	= $this->get('Item');
		$form	= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Bind the item data to the form object.
		if ($item) {
			$form->bind($item);
		}

		$this->assignRef('state',	$state);
		$this->assignRef('item',	$item);
		$this->assignRef('form',	$form);

		parent::display($tpl);

		JRequest::set('hidemainmenu', 1);
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
		$user = &JFactory::getUser();
		if (is_object($this->item)) {
			$isCheckedOut	= JTable::isCheckedOut($user->get('id'), $this->item->checked_out);
			$isNew			= ($this->item->id == 0);
		}
		else {
			$isCheckedOut	= false;
			$isNew			= true;
		}

		JToolBarHelper::title('Category: '.JText::_(($isCheckedOut ? 'View Item' : ($isNew ? 'Add Category' : 'Edit Category'))));
		if (!$isCheckedOut) {
			JToolBarHelper::custom('category.save2new', 'new.png', 'new_f2.png', 'Save And New', false);
			JToolBarHelper::save('category.save');
			JToolBarHelper::apply('category.apply');
		}
		JToolBarHelper::cancel('category.cancel');
	}
}