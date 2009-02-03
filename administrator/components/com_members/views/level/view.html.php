<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.application.component.view');

/**
 * The HTML Members access level view.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_members
 * @version		1.6
 */
class MembersViewLevel extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
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

		$item->title	= $item->getAssetGroupName();
		$form->bind($item);

		$this->assignRef('form',	$form);
		$this->assignRef('state',	$state);
		$this->assignRef('item',	$item);

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
		$isNew	= ($this->item->getAssetGroupId() == 0);
		JToolBarHelper::title(JText::_($isNew ? 'Members_Title_Add_Access_Level' : 'Members_Title_Edit_Access_Level'));

		JToolBarHelper::save('level.save');
		JToolBarHelper::apply('level.apply');
		JToolBarHelper::cancel('level.cancel');
		//JToolBarHelper::help('index', true);
	}
}