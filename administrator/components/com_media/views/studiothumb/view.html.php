<?php
/**
Author : Nakul Ganesh S
Joomla GSoC 2008
Mentor : Deborah Susan Clarkson
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
 
 class MediaViewStudiothumb extends JView
 {

	function display($tpl = null)
	{
		global $mainframe;
		// Do not allow cache
		$config =& JComponentHelper::getParams('com_media');
		JResponse::allowCache(false);
		$document = &JFactory::getDocument();
		
		$this->assignRef('state', $this->get('state'));
		parent::display($tpl);

				JHTML::_('behavior.mootools');


}
}
