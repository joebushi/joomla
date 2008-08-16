<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class UsersViewGroupdetail extends JView
{
	function display()
	{
		if(JRequest::getInt('id', 0) == 0)
		{
			return false; 
		}
		$group = JAuthorizationUsergroup::getInstance();
		$group->load(JRequest::getInt('id'));
		$this->assignRef('group', $group);
		
		parent::display();	
	}
}
?>