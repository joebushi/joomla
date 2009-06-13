<?php
/**
 * @version		$Id: component.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Administrator
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');

/**
 * @package		Joomla.Administrator
 * @subpackage	Config
 */
class ConfigModelApplication extends JModelForm
{
	/**
	 * Method to get a form object.
	 */
	public function getForm()
	{
		// Get the form.
		$form = parent::getForm('application', array('array' => true, 'event' => 'onPrepareConfigForm'));
		
		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return false;
		}
		
		return $form;
	}
	
	/**
	 * Method to get the form data.
	 */
	public function getData()
	{
		$data = new JConfig();
		
		return $data;
	}
}