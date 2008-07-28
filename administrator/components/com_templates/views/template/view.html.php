<?php
/**
* @version		$Id: $
* @package		Joomla
* @subpackage	Templates
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
 * HTML View class for the Templates component
 *
 * @static
 * @package		Joomla
 * @subpackage	Templates
 * @since 1.6
 */
class TemplatesViewTemplate extends JView
{
	function display($tpl = null)
	{
		global $option;
		
		if(JRequest::getVar('layout') == 'edit')
		{
			$this->_edit($tpl);
			return;
		}
		jimport('joomla.filesystem.path');
		$this->loadHelper('template');

		JToolBarHelper::title( JText::_( 'Template' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'thememanager' );
		JToolBarHelper::custom('preview', 'preview.png', 'preview_f2.png', 'Preview', false, false);
		JToolBarHelper::custom( 'edit_source', 'html.png', 'html_f2.png', 'Edit HTML', false, false );
		JToolBarHelper::custom( 'choose_css', 'css.png', 'css_f2.png', 'Edit CSS', false, false );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply();
		JToolBarHelper::cancel( 'cancel', 'Close' );
		JToolBarHelper::help( 'screen.templates' );

		$row		=& $this->get('Data');
		$params		=& $this->get('Params');
		$client		=& $this->get('Client');
		$template	=& $this->get('Template');
		$assignments =& $this->get('Assignments');

		if (!$template) {
			return JError::raiseWarning( 500, JText::_('Template not specified') );
		}

		if($client->id == '1')  {
			$lists['selections'] =  JText::_('Cannot assign an administrator template');
		} else {
			$lists['selections'] = TemplatesHelper::createMenuList($template);
		}

		$list =& JHTML::_('menu.menulist');
		$mitems = array();

		$lastMenuType	= null;
		$tmpMenuType	= null;

		foreach ($list as $list_a)
		{
			if ($list_a->menutype != $lastMenuType)
			{
				if ($tmpMenuType) {
					$mitems[] = '</ul></li>';
				}
				$mitems[] = '<li>'.$list_a->menutype.'<ul>';
				$lastMenuType = $list_a->menutype;
				$tmpMenuType  = $list_a->menutype;
			}

			if(isset($assignments[$list_a->id]))
			{
				$mitems[] = '<li><a href="index.php?option=com_templates&view=template&layout=edit&cid[]='.$row->directory.'&menuid='.$list_a->id.'"><img src="tick.png" width="16" height="16" />'.$list_a->treename.'</a></li>';
			} else {
				$mitems[] = '<li>'.$list_a->treename.'</li>';
			}

		}
		if ($lastMenuType !== null) {
			$mitems[] = '</ul></li></ul>';
		}

		$this->assignRef('lists',		$lists);
		$this->assignRef('row',			$row);
		$this->assignRef('option',		$option);
		$this->assignRef('client',		$client);
		$this->assignRef('template',	$template);
		$this->assignRef('assignments', $mitems);
		$this->assignRef('params',		$params);

		parent::display($tpl);
	}
	
	function _edit($tpl = null)
	{
		global $option;
		$this->setLayout('edit');
		
		JToolBarHelper::title( JText::_( 'Template' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'thememanager' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.template_edit' );

		$client		=& $this->get('Client');
		$template	=& $this->get('Template');
		$row		=& $this->get('Data');
		
		$params		=& $this->get('Params');
		if($client->id == '1')  {
			$lists['selections'] =  JText::_('Cannot assign an administrator template');
		} else {
			$lists['selections'] = TemplatesHelper::createMenuList($template);
		}
				
		$this->assignRef('row',			$row);
		$this->assignRef('lists',		$lists);
		$this->assignRef('params', $params);
		$this->assignRef('client',		$client);
		$this->assignRef('option',		$option);

		parent::display($tpl);		
	}
}