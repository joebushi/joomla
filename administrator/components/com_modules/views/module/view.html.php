<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Modules component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @since 1.6
 */
class ModulesViewModule extends JView
{
	public $state;
	public $item;
	public $form;

	public $params;
	public $positions;

	public function display($tpl = null)
	{
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$form		= $this->get('Form');
		$client		= $state->get('client');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Bind the label to the form.
		$form->bind($item);

		$this->assignRef('state',	$state);
		$this->assignRef('item',	$item);
		$this->assignRef('form',	$form);

		// Initialize some variables
		$db 	= &JFactory::getDbo();
		$user 	= &JFactory::getUser();

		$module = JRequest::getVar('module', '', '', 'cmd');
		$isNew		= ($item->id < 1);
		if ($isNew) {
			$item->module 	= $module;
		}

		if ($item->module == 'mod_custom') {
			JToolBarHelper::Preview('index.php?option=com_modules&tmpl=component&client='.$client->id.'&pollid='.$row->id);
		}

		$lists 	= array();

		if ($client->id == 1)
		{
			$where 				= 'client_id = 1';
			$path				= 'mod1_xml';
		}
		else
		{
			$where 				= 'client_id = 0';
			$path				= 'mod0_xml';
		}

		$query = 'SELECT position, ordering, showtitle, title'
		. ' FROM #__modules'
		. ' WHERE '. $where
		. ' ORDER BY ordering'
		;
		$db->setQuery($query);
		if (!($orders = $db->loadObjectList())) {
			echo $db->stderr();
			return false;
		}

		$orders2 	= array();

		$l = 0;
		$r = 0;
		for ($i=0, $n=count($orders); $i < $n; $i++) {
			$ord = 0;
			if (array_key_exists($orders[$i]->position, $orders2)) {
				$ord =count(array_keys($orders2[$orders[$i]->position])) + 1;
			}

			$orders2[$orders[$i]->position][] = JHtml::_('select.option',  $ord, $ord.'::'.addslashes($orders[$i]->title));
		}

		// get selected pages for $lists['selections']
		if (!$isNew) {
			$row->pages = 'select';
			$query = 'SELECT menuid AS value'
			. ' FROM #__modules_menu'
			. ' WHERE moduleid = '.(int) $row->id
			;
			$db->setQuery($query);
			$lookup = $db->loadObjectList();
			$row->pages = 'select';
			if (empty($lookup)) {
				$lookup = array(JHtml::_('select.option', '-1'));
				$row->pages = 'none';
			} elseif (count($lookup) == 1 && $lookup[0]->value == 0) {
				$row->pages = 'all';
			} else {
				/*
				 * If any menu value is negative, make the type "deselect". This
				 * has the side-effect of hiding any corruption in the values
				 * (i.e. a mix of positive and negative).
				 */
				foreach ($lookup as $key => $modMenu) {
					if ($modMenu->value < 0) {
						$lookup[$key]->value = -$modMenu->value;
						$row->pages = 'deselect';
					}
				}
			}
		} else {
			$lookup = array(JHtml::_('select.option', 0, JText::_('All')));
			$row->pages = 'all';
		}

		if ($item->client_id == 1) {
			$lists['selections'] = 'N/A';
		} else {
			if ($client->id == '1') {
				$lists['selections'] = 'N/A';
			} else {

				$selections = JHtml::_('menu.linkoptions');
				$lists['selections'] = JHtml::_(
					'select.genericlist',
					$selections,
					'jform[selections][]',
					'class="inputbox" size="15" multiple="multiple"',
					'value',
					'text',
					$lookup,
					'selections'
				);
			}
		}

		$row->description = '';

		$lang = &JFactory::getLanguage();
		if ($client->id != '1') {
			$lang->load(trim($item->module), JPATH_SITE);
			$lang->load('joomla', JPATH_SITE.DS.'modules'.DS.trim($item->module));
		} else {
			$lang->load(trim($item->module));
			$lang->load('joomla', JPATH_ADMINISTRATOR.DS.'modules'.DS.trim($item->module));
		}

		// xml file for module
		if ($item->module == 'mod_custom') {
			$xmlfile = JApplicationHelper::getPath($path, 'mod_custom');
		} else {
			$xmlfile = JApplicationHelper::getPath($path, $item->module);
		}

		$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
		if ($data)
		{
			foreach($data as $key => $value) {
				$item->$key = $value;
			}
		}


		/*if (!in_array($item->position, $positions)) {
			array_push($positions, $item->position);
		}
		sort($positions);*/

		$this->assignRef('lists',		$lists);
		$this->assignRef('orders2',		$orders2);
		$this->assignRef('client',		$client);

		parent::display($tpl);
		$this->_setToolbar();
	}

	/**
	 * Setup the Toolbar
	 *
	 * @since	1.6
	 */
	protected function _setToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);

		JToolBarHelper::title(JText::_('Module_Manager').': '.JText::_('Edit_Module'), 'module.png');
		JToolBarHelper::save('module.save');
		JToolBarHelper::apply('module.apply');
		JToolBarHelper::addNew('module.save2new', 'JToolbar_Save_and_new');
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('module.cancel');
		}
		else {
			JToolBarHelper::cancel('module.cancel', 'JToolbar_Close');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.modules.edit');
	}
}
