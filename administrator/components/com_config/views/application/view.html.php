<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * @package		Joomla.Administrator
 * @subpackage	Config
 * @version		1.6
 */
class ConfigViewApplication extends JView
{
	public $state;
	public $form;
	public $data;

	/**
	 * Method to display the view.
	 */
	public function display($tpl = null)
	{
		$form	= $this->get('Form');
		$data	= $this->get('Data');
		
		// Check for model errors.
		if ($errors = $this->get('Errors')) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Bind the form to the data.
		if ($form && $data) {
			$form->bind($data);
		}
		
		// Get other component parameters.
		$table = JTable::getInstance('component');
		
		// Get the params for com_users.
		$table->loadByOption('com_users');
		$usersParams = new JParameter($table->params, JPATH_ADMINISTRATOR.'/components/com_users/config.xml');
		
		// Get the params for com_media.
		$table->loadByOption('com_media');
		$mediaParams = new JParameter($table->params, JPATH_ADMINISTRATOR.'/components/com_media/config.xml');

		$this->assignRef('form',		$form);
		$this->assignRef('usersParams', $usersParams);
		$this->assignRef('mediaParams', $mediaParams);

		// Load settings for the FTP layer.
		jimport('joomla.client.helper');
		$ftp = &JClientHelper::setCredentialsFromRequest('ftp');
		
		parent::display($tpl);
	}

	function WarningIcon()
	{
		global $mainframe;

		$tip = '<img src="'.JURI::root().'includes/js/ThemeOffice/warning.png" border="0"  alt="" />';

		return $tip;
	}
}
