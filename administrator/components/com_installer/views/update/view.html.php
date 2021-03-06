<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

include_once dirname(__FILE__).DS.'..'.DS.'default'.DS.'view.php';

/**
 * Extension Manager Update View
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.6
 */
class InstallerViewUpdate extends InstallerViewDefault
{
	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::custom('update.update', 'config', 'config', 'Update', true, false);
		JToolBarHelper::custom('update.find', 'refresh', 'refresh','FIND_UPDATES',false,false);
		JToolBarHelper::custom('update.purge', 'purge', 'purge', 'PURGE_CACHE', false,false);
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.installer');

		// Get data from the model
		$state		= &$this->get('State');
		$items		= &$this->get('Items');
		$pagination	= &$this->get('Pagination');

		$paths = new stdClass();
		$paths->first = '';

		$this->assignRef('paths', $paths);
		$this->assignRef('state', $this->get('state'));


		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		JHTML::_('behavior.tooltip');
		parent::display($tpl);
	}

	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->index	= $index;
		$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;

		$this->assignRef('item', $item);
	}
}