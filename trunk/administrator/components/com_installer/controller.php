<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

/**
 * Installer Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */
class InstallerController extends JController
{
	/**
	 * Display the extension installer form
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function installform()
	{
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Install');
		$view	= &$this->getView('Install');

		$ftp = &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Install an extension
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function doInstall()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model	= &$this->getModel('Install');
		$view	= &$this->getView('Install');

		$ftp = &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		if ($model->install())
		{
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
		}

		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Manage an extension type (List extensions of a given type)
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function manage()
	{
		$type	= JRequest::getWord('type', 'components');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);
		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');

		$view->assignRef('ftp', $ftp);
		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Discover handler
	 */
	public function discover()
	{
		$model	= &$this->getModel('discover');
		$view	= &$this->getView('discover');
		$model->discover();
		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		$view->setModel($model, true);
		$view->display();
	}

	function discover_install()
	{
		$model	= &$this->getModel('discover');
		$view	= &$this->getView('discover');
		$model->discover_install();
		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		$view->setModel($model, true);
		$view->display();
	}

	function discover_purge()
	{
		$model = &$this->getModel('discover');
		$model->purge();
		$this->setRedirect('index.php?option=com_installer&task=manage&type=discover', $model->_message);
	}

	/**
	 * Enable an extension (If supported)
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function enable()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		$type	= JRequest::getWord('type', 'components');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);

		$ftp = &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		if (method_exists($model, 'enable'))
		{
			$eid = JRequest::getVar('eid', array(), '', 'array');
			JArrayHelper::toInteger($eid, array());
			$model->enable($eid);
		}

		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Disable an extension (If supported)
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function disable()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		$type	= JRequest::getWord('type', 'components');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);

		$ftp = &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		if (method_exists($model, 'disable'))
		{
			$eid = JRequest::getVar('eid', array(), '', 'array');
			JArrayHelper::toInteger($eid, array());
			$model->disable($eid);
		}

		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Remove an extension (Uninstall)
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$type	= JRequest::getWord('type', 'components');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);
		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$eid = JRequest::getVar('eid', array(), '', 'array');

		// Update to handle components radio box
		// Checks there is only one extensions, we're uninstalling components
		// and then checks that the zero numbered item is set (shouldn't be a zero
		// if the eid is set to the proper format)
		if ((count($eid) == 1) && ($type == 'components') && (isset($eid[0]))) {
			$eid = array($eid[0] => 0);
		}

		JArrayHelper::toInteger($eid, array());
		$result = $model->remove($eid);

		$view->setModel($model, true);
		$view->display();
	}

	function refresh()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$type	= JRequest::getWord('type', 'manage');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);
		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$uid = JRequest::getVar('eid', array(), '', 'array');

		JArrayHelper::toInteger($uid, array());
		$result = $model->refresh($uid);

		$view->setModel($model, true);
		$view->display();
	}

	// Should probably use multiple controllers here
	function update()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$type	= JRequest::getWord('type', 'components');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);
		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$uid = JRequest::getVar('uid', array(), '', 'array');

		JArrayHelper::toInteger($uid, array());
		if ($model->update($uid))
		{
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
		}

		$view->setModel($model, true);
		$view->display();
	}

	function update_find()
	{
		// Find updates
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$type	= JRequest::getWord('type', 'components');
		$model	= &$this->getModel($type);
		$view	= &$this->getView($type);

		$ftp	= &JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		$model->purge();
		$result = $model->findUpdates();

		$view->setModel($model, true);
		$view->display();
	}

	function update_purge()
	{
		// Purge updates
		$model = &$this->getModel('update');
		$model->purge();
		$this->setRedirect('index.php?option=com_installer&task=manage&type=update', $model->_message);
	}
}