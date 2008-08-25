<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 */
class UserViewGroup extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state = $this->get( 'State' );
		$this->assignRef( 'state', $state );

		$item = &$this->get( 'Item' );
		$this->assignRef( 'item', $item );

		if ($state->get( 'id' )) {
			// Existing
		}
		else {
			// New
		}

		$this->_setToolBar();
		parent::display($tpl);
	}

	/**
	 * Display the toolbar
	 *
	 * @access	private
	 */
	function _setToolBar()
	{
		$isNew = ($this->item->get( 'id' ) == 0);
		JToolBarHelper::title( JText::_( ($isNew ? 'Add Group' : 'Edit Group' ) ), 'user' );
		if (!$isNew) {
			JToolBarHelper::custom( 'group.save2copy', 'copy.png', 'copy_f2.png', 'Save To Copy', false );
		}
		JToolBarHelper::custom( 'group.save2new', 'new.png', 'new_f2.png', 'Save And New', false );
		JToolBarHelper::save( 'group.save' );
		JToolBarHelper::apply( 'group.apply' );
		JToolBarHelper::cancel( 'group.cancel' );
		JToolBarHelper::help( 'screen.groups.edit' );
	}
}