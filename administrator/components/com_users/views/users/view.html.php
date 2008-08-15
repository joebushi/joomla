<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	Users
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @static
 * @package		Joomla
 * @subpackage	Users
 * @since 1.0
 */
class UsersViewUsers extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$document = JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$document->addScript(JURI::root().'media/system/js/mootree_packed.js');
		$usergroups = JAuthorizationUsergroup::getInstance();
		$usergroups->load();
		$javascript = 'var tree;

window.onload = function() {
	
	tree = new MooTreeControl({
		div: \'mytree\',
		grid: true,
		theme: \''.JURI::root().'media/system/images/mootree.gif'.'\'
	},{
		text: \'Root Node\',
		open: true
	});
	tree.adopt(\'users\');
	tree.expand();
}';
		$document->addScriptDeclaration($javascript);
		$this->assignRef('usergroups', $usergroups);
		parent::display($tpl);
	}
	
	function getTree($first = false)
	{
		if($first)
		{
		$html = '<ul id="users">';
		} else {
			$html = '<ul>';
		}
foreach($this->usergroups->getChildren() as $usergroups)
{
	$html .= '<li><a href="test.html">'.$usergroups->name.'</a>';
	$this->usergroups->load($usergroups->id);
	if($this->usergroups->getChildren())
	{
		$html .= $this->getTree();
	}
	$this->usergroups->load($usergroups->parent);
	$html .= '</li>';
}
$html .= '</ul>';
return $html;
	}
}