<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Content Type Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class ContentControllerTypes extends JController
{
	protected $_redirectURL;

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new',	'save');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');
		$this->_redirectURL = 'index.php?option=com_content&controller=types';
	}

	public function display()
	{
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', 'types' );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view =& $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));

		switch ($viewName) {
			case 'type':
				$model = $this->getModel('type');
				$cid = JRequest::getVar('cid', array(0), '', 'array');
				$model->setState('id', (int) $cid[0]);
				break;

			case 'types':
			default:
				$model = $this->getModel('types');
				$this->_setTypesModelState($model);
				break;
		}

		// Push the model into the view (as default)
		$view->setModel($model, true);

		$view->setLayout($viewLayout);
		$view->display();
	}

	protected function _setTypesModelState($model)
	{
		$app = JFactory::getApplication();
		$context = 'com_content.viewtypes';

		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_content.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$model->setState('limit', $limit);
		$model->setState('limitstart', $limitstart);

		// Get the filter request variables
		$order		= $app->getUserStateFromRequest($context.'.filter_order', 'filter_order', 'ordering', 'cmd');
		$orderDir	= $app->getUserStateFromRequest($context.'.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$search		= $app->getUserStateFromRequest($context.'.search', 'search', '', 'string');
		$search		= JString::strtolower($search);

		$model->setState('filter.order', $order);
		$model->setState('filter.orderDir', $orderDir);
		$model->setState('filter.search', $search);
	}

	public function edit()
	{
		JRequest::setVar( 'view', 'type');
		JRequest::setVar( 'hidemainmenu', 1);
		$this->display();
	}

	public function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die('Invalid Token');

		$model = $this->getModel('type');

		$data = JRequest::get('post');
		$data['description'] = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );

		$msg = JText::_('Saved');
		$msgType = 'message';

		try {
			$model->store($data);
		} catch (JException $e) {
			$msg = $e->getMessage();
			$msgType = 'error';
		}

		switch ($this->getTask()) {
			case 'save':
				$url = $this->_redirectURL;
				break;

			case 'save2new':
				$url = $this->_redirectURL . '&task=add';
				break;

			case 'apply':
			default:
				$id = $model->getState('id');
				$return	= ($id ? $id : JRequest::getInt('id', 0));
				$url = $this->_redirectURL . '&task=edit&cid[]=' . $return;
				break;
		}

		$this->setRedirect($url, $msg, $msgType);
	}


	public function remove()
	{
		/*
		 * @todo have some confirmation dialog over here. like field data will be removed etc.
		 */
		// Check for request forgeries.
		JRequest::checkToken() or die( 'Invalid Token' );
		$mainframe = JFactory::getApplication();

		$cid = JRequest::getVar('cid', array(), '', 'array');
		$msg = JText::_( 'ITEMS DELETED' );
		$msgType = 'message';

		if (empty($cid)) {
			$msg = JText::_('No items selected');
			$msgType = 'error';
		} else {
			$model = $this->getModel('type');

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			try {
				$model->delete($cid);
			} catch (JException $e) {
				$msg = $e->getMessage();
				$msgType = 'error';
			}
		}

		$this->setRedirect($this->_redirectURL, $msg, $msgType);
	}

	public function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('type');
		$model->saveorder($cid, $order);

		$msg = JText::_('New ordering saved');
		$this->setRedirect($this->_redirectURL, $msg );
	}

	public function order()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$task	= $this->getTask();
		$values	= array('orderup' => -1, 'orderdown' => 1);
		$value	= JArrayHelper::getValue( $values, $task, -1, 'int' );

		$model = $this->getModel('type');
		$model->move($value);

		$this->setRedirect($this->_redirectURL);
	}

	public function cancel()
	{
		$msg = JText::_('Operation Cancelled');
		$this->setRedirect($this->_redirectURL, $msg );
	}
}