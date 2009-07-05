<?php
defined( '_JEXEC' ) or die;

class MessagesController extends JController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		// TODO: add registerTask functions here.
	}
	
	public function add()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_messages&view=message&layout=edit', false));
	}
	
	public function saveconfig()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$vars = JRequest::getVar('vars', array(), 'post', 'array');

		$model = $this->getModel('Config');
		$model->save($vars);

		$this->setRedirect("index.php?option=com_messages", JText::_("Configuration Saved"));
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid	= JRequest::getVar('cid', array(0), '', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('Message');
		
		if ($model->delete($cid)) {
			$message = JText::_('JSuccess_N_items_deleted');
			$this->setRedirect(JRoute::_('index.php?option=com_messages'), $message);
			return true;		
		} else {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_weblinks&view=weblinks', $message, 'error');
			return false;
		}
	}

	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model = $this->getModel('Message');
		$data = JRequest::get('post');
		
		if (!$model->save($data)) {
			$this->setRedirect("index.php?option=com_messages", $model->getError());
			return false;
		}
		
		$this->setRedirect("index.php?option=com_messages",  JText::_('Message Sent'));
		return true;
	}

	public function display()
	{
		if ($this->_task == 'config') {
			JRequest::setVar('view', 'config');
		} else if ($this->_task == 'view') {
			JRequest::setVar('view', 'message');
		} else if ($this->_task == 'reply') {
			JRequest::setVar('view', 'message');
			JRequest::setVar('layout', 'edit');
		}

		parent::display();
	}
}