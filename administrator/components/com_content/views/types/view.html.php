<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class ContentViewTypes extends JView
{
	public $items = null;

	public $pagination = null;

	public $state = null;

	public function display($tpl = null)
	{
		$items		= & $this->get('Data');
		$pagination = & $this->get('Pagination');
		$state		= & $this->get('State');

		JToolBarHelper::title( JText::_('Type Manager'), 'generic.png' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::preferences('com_content', '550');
		JToolBarHelper::help( 'screen.content' );

		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('state',		$state);

		parent::display($tpl);
	}
}