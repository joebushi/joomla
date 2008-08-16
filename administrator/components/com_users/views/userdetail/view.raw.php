<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class UsersViewUserdetail extends JView
{
	function display()
	{
		if(JRequest::getInt('id', 0) == 0)
		{
			return false; 
		}
		$user =& JUser::getInstance(JRequest::getInt('id'));
		
		$acluser = JAuthorizationUser::getInstance();
		
		$this->assignRef('user', $user);
		$this->assignRef('acluser', $acluser);

		parent::display();
	}
}

?>