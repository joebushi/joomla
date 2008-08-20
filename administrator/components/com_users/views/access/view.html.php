<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class UsersViewAccess extends JView
{
	function display()
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'helper'.DS.'helper.php');
		$access = new AccessParameters(JRequest::getVar('component'));
		$this->assignRef($access);
		parent::display();	
	}
}