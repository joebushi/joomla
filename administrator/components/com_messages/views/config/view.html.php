<?php
/**
* @version		$Id: $
* @package		Joomla
* @subpackage	Messages
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Messages component
 *
 * @static
 * @package		Joomla
 * @subpackage	Messages
 * @since 1.0
 */
class MessagesViewConfig extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
	
		$db					=& JFactory::getDBO();
	
		// Set toolbar items for the page
		JToolBarHelper::title(  JText::_( 'Private Messaging Configuration' ), 'inbox.png' );
		JToolBarHelper::save( 'saveconfig' );
		JToolBarHelper::cancel( 'cancelconfig' );
		JToolBarHelper::help( 'screen.messages.conf' );

		// Get data from the model
		$vars		= & $this->get( 'Data');
	
		$this->assignRef('vars',	$vars);

		parent::display($tpl);
	}
}