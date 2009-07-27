<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	Wrapper
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * @package		Joomla.Site
 * @subpackage	Wrapper
 */
class WrapperViewWrapper extends JView
{
	function display($tpl = null)
	{
		$mainframe	= &JFactory::getApplication();
		$document	= &JFactory::getDocument();

		// auto height control
		if ($this->params->def('height_auto')) {
			$this->wrapper->load = 'onload="iFrameHeight()"';
		} else {
			$this->wrapper->load = '';
		}

		// Get the page/component configuration
		$params = &$mainframe->getParams();

		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		$document->setTitle($params->get('page_title'));

		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
}