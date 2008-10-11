<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 */
class AccessViewLevels extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		/*
		$state		= $this->get( 'State' );
		$items		= $this->get( 'List' );
		$pagination	= $this->get( 'Pagination' );

		$this->assignRef( 'state',		$state );
		$this->assignRef( 'items',		$items );
		$this->assignRef( 'pagination',	$pagination );
		*/
		JError::raiseNotice( 0, 'TODO' );

		$this->_setToolbar();
		parent::display($tpl);
	}

	/**
	 * Display the toolbar
	 */
	private function _setToolbar()
	{
		JToolBarHelper::title( JText::_( 'Access Control: Access Levels' ), 'user' );
		JToolBarHelper::custom( 'edit', 'edit.png', 'edit_f2.png', 'Edit', true );
		JToolBarHelper::custom( 'edit', 'new.png', 'new_f2.png', 'New', false );
		JToolBarHelper::deleteList( '', 'delete' );
	}
}