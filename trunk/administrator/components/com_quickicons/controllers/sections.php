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
class QuickIconsControllerSections extends JController
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
	 * Method to publish sections
	 *
	 * @access public
	 * @return void
	 */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model = & $this->getModel('sections');
		if ($model->publish())
		{
			$msg = JText::_('QuickIcons_Sections_Published');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=sections',$msg,$type);
	}

	/**
	 * Method to unpublish sections
	 *
	 * @access public
	 * @return void
	 */
	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model = & $this->getModel('sections');
		if ($model->unpublish())
		{
			$msg = JText::_('QuickIcons_Sections_Unpublished');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=sections',$msg,$type);
	}

	/**
	 * Method to save the order of sections
	 *
	 * @access public
	 * @return void
	 */
	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model = & $this->getModel('sections');
		if ($model->saveorder())
		{
			$msg = JText::_('QuickIcons_Sections_Ordered');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=sections',$msg,$type);
	}
	
	/**
	 * Method to reorder a section
	 *
	 * @access public
	 * @return void
	 */
	function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));
		$model	= & $this->getModel('sections');
		if($model->reorder($this->getTask() == 'orderup' ? -1 : 1))
		{
			$msg = JText::_('QuickIcons_Sections_Ordered');
			$type = 'message';
		}
		else
		{
			$msg = & $this->getError();
			$type = 'error';
		}
		$this->setredirect('index.php?option=com_quickicons&view=sections',$msg,$type);
		
	}
}
