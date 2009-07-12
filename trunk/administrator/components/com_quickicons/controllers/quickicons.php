<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.theartofjoomla.com
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 */
class QuickIconsControllerQuickIcons extends JController
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('orderup',		'reorder');
		$this->registerTask('orderdown',	'reorder');
	}

	/**
	 * Method to publish icons
	 *
	 * @access public
	 * @return void
	 */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model = & $this->getModel('quickicons');
		if ($model->publish())
		{
			$msg = JText::_('QuickIcons_QuickIcons_Published');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=quickicons',$msg,$type);
	}

	/**
	 * Method to unpublish icons
	 *
	 * @access public
	 * @return void
	 */
	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model = & $this->getModel('quickicons');
		if ($model->unpublish())
		{
			$msg = JText::_('QuickIcons_QuickIcons_Unpublished');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=quickicons',$msg,$type);
	}

	/**
	 * Method to save the order of icons
	 *
	 * @access public
	 * @return void
	 */
	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model = & $this->getModel('quickicons');
		if ($model->saveorder())
		{
			$msg = JText::_('QuickIcons_QuickIcons_Ordered');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=quickicons',$msg,$type);
	}
	
	/**
	 * Method to reorder an icon
	 *
	 * @access public
	 * @return void
	 */
	function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model	= & $this->getModel('quickicons');
		if($model->reorder($this->getTask() == 'orderup' ? -1 : 1))
		{
			$msg = JText::_('QuickIcons_QuickIcons_Ordered');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=quickicons',$msg,$type);		
	}
}
