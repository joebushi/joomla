<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class UsersViewAccess extends JView
{
	function display($tpl = null)
	{
		if($this->getLayout() == 'edit')
		{
			$this->_accessView($tpl);
			return;
		}
		$document = JFactory::getDocument();
		JRequest::setVar('tmpl', 'component');
		JHTML::_('behavior.mootools');
		JHTML::_('behavior.modal');
		$document->addScript(JURI::root().'media/system/js/mootree_packed.js');
		$document->addStyleSheet(JURI::root().'media/system/css/mootree.css');
		$usergroups = new JAuthorizationUsergroup();
		$component = '';
		if(JRequest::getVar('component', 0) != 0)
		{
			$component = '&component='.JRequest::getVar('component');
		}
		$javascript = 'var tree;
			window.onload = function() {
	
			tree = new MooTreeControl({
				div: \'mytree\',
				grid: true,
				theme: \'../media/system/images/mootree.gif\',
				onSelect: function(node, state) {
					if (state) window.location.href = \'index.php?option=com_users&tmpl=component&view=access&layout=edit'.$component.'\'+node.data.url
				}
			},{
				text: \'Root Node\',
				open: true
			});
			tree.adopt(\'groups\');
			tree.expand();
		}';
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'helper'.DS.'helper.php');
		$usergrouphelper = new UsersHelper();
		$document->addScriptDeclaration($javascript);
		$this->assignRef('usergroups', $usergroups);
		$this->assignRef('usergrouphelper', $usergrouphelper);
		parent::display($tpl);
	}
	
	function _accessView($tpl = null)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'helper'.DS.'helper.php');
		$access = new AccessParameters(JRequest::getVar('component'), JRequest::getInt('id'));
		$this->assignRef('access', $access);
		parent::display();	
	}
}
