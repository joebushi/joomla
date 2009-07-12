<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML Sections View class for the QuickIcons component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 * @since		1.6
 */
class QuickIconsViewSections extends JView
{
	/**
	 * @var array items
	 */
	protected $items=null;
	
	/**
	 * @var object pagination information
	 */
	protected $pagination=null;
	
	/**
	 * @var string option name
	 */
	protected $option = null;

	
	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$items		= & $this->get('Items');
		$pagination = & $this->get('Pagination');
		$option		= & $this->get('Option');

		// Assign data to the view
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('option',		$option);
		
		// Set the toolbar and the submenu
		$this->_setToolBar();
		
		// Display the view
		parent::display($tpl);
	}
	/**
	 * Setup the Toolbar
	 *
	 * @since	1.6
	 */
	protected function _setToolBar()
	{
		JToolBarHelper::title(JText::_('QuickIcons_Sections_Manager'), 'mediamanager.png');
		JToolBarHelper::publishList('sections.publish');
		JToolBarHelper::unpublishList('sections.unpublish');
		JToolBarHelper::help('screen.quickicons');
	}
}
