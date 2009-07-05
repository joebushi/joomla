<?php
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

class MessagesViewConfig extends JView
{
	public $vars;

	public function display($tpl = null)
	{
		$vars = $this->get('Vars');

		$this->assignRef('vars', $vars);

		parent::display($tpl);
		$this->_setToolbar();
	}

	protected function _setToolbar()
	{
		JToolBarHelper::title(JText::_('Private Messaging Configuration'), 'inbox.png');
		JToolBarHelper::save('saveconfig');
		JToolBarHelper::cancel('cancelconfig');
		JToolBarHelper::help('screen.messages.conf');
	}
}