<?php
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

class MessagesViewMessage extends JView
{
	public $recipientslist;
	public $subject;
	public $item;
	
	public function display($tpl = null)
	{
		$recipientslist = $this->get('RecipientsList');
		$subject = $this->get('Subject');
		$item = $this->get('Item');

		$model = $this->getModel();
		$model->markAsRead();

		$this->assignRef('recipientslist', $recipientslist);
		$this->assignRef('subject', $subject);
		$this->assignRef('item', $item);

		parent::display($tpl);

		if ($this->getLayout() == 'edit') {
			$this->_setFormToolbar();
		} else {
			$this->_setDefaultToolbar();
		}
	}
	
	protected function _setFormToolbar()
	{
		JToolBarHelper::title(JText::_('Write Private Message'), 'inbox.png');
		JToolBarHelper::save('save', 'Send');
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.messages.edit');
	}
	
	protected function _setDefaultToolbar()
	{
		JToolBarHelper::title(JText::_('View Private Message'), 'inbox.png');
		JToolBarHelper::customX('reply', 'restore.png', 'restore_f2.png', 'Reply', false);
		JToolBarHelper::deleteList();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.messages.read');
	}
}