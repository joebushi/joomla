<?php
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

class MessagesViewMessages extends JView
{
	public $pagination;
	public $items;
	public $state;
	
	public function display($tpl = null)
	{
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$state = $this->get('State');

		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('state', $state);
		
		parent::display($tpl);
		$this->_setToolbar();
	}
	
	protected function _setToolbar()
	{
		JToolBarHelper::title(JText::_('Private Messaging'), 'inbox.png');
		JToolBarHelper::deleteList();
		JToolBarHelper::addNewX();
		JToolBarHelper::custom('config', 'config.png', 'config_f2.png', 'Settings', false, false);
		JToolBarHelper::help('screen.messages.inbox');
	}
}