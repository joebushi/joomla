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
class UserViewLevels extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		/*
		$state = $this->get( 'State' );
		$this->assignRef( 'state', $state );

		$items = &$this->get( 'Items' );
		$this->assignRef( 'items', $items );

		// setup the page navigation footer
		$pagination	= &$this->get( 'Pagination' );
		$this->assignRef( 'pagination', $pagination );
		*/
		JError::raiseNotice( 0, 'TODO' );

		$this->_setToolbar();
		parent::display($tpl);
	}

	/**
	 * Display the toolbar
	 * @access	public
	 */
	function _setToolBar()
	{
		JToolBarHelper::title( JText::_( 'Access Levels' ), 'user' );
		JToolBarHelper::custom( 'edit', 'edit.png', 'edit_f2.png', 'Edit', true );
		JToolBarHelper::custom( 'edit', 'new.png', 'new_f2.png', 'New', false );
		JToolBarHelper::deleteList( '', 'delete' );
	}
}