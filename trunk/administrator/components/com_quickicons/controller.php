<?php
/**
 * @version		$Id: controller.php 12268 2009-06-22 00:05:11Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Component Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 */
class QuickIconsController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		// Load the submenu.
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'quickicons.php';
		$vName		= JRequest::getWord('view', 'quickicons');
		QuickIconsHelper::addSubmenu($vName);
		parent::display();
	}
}
