<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');

/**
 * @package		Joomla.Administrator
 * @subpackage	Modules
 */
class ModulesModelModule extends JModelForm
{
	var $_xml;

	/**
	 * Override to get the module table
	 */
	public function getTable()
	{
		return JTable::getInstance('Module');
	}

	/**
	 * Method to get the group form.
	 *
	 * @return	mixed	JForm object on success, false on failure.
	 * @since	1.0
	 */
	public function getForm()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();

		// Get the form.
		$form = parent::getForm('module', 'com_modules.module', array('array' => 'jform', 'event' => 'onPrepareForm'));

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_modules.edit.module.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState()
	{
		$app		= &JFactory::getApplication('administrator');
		$params		= &JComponentHelper::getParams('com_modules');

		// Load the User state.
		if (JRequest::getWord('layout') === 'edit') {
			$id = (int) $app->getUserState('com_modules.edit.module.id');
			$this->setState('module.id', $id);
		}
		else {
			$id = (int) JRequest::getInt('module_id');
			$this->setState('module.id', $id);
		}

		// Load the parameters.
		$this->setState('params', $params);

		$client = JRequest::getInt('client');
		$this->setState('client.id', $client);
		$this->setState('client', JApplicationHelper::getClientInfo($client));
	}

	/**
	 * Method to get a module item.
	 *
	 * @param	integer	The id of the module to get.
	 * @return	mixed	User data object on success, false on failure.
	 * @since	1.0
	 */
	public function getItem($id = null)
	{
		$id	= (!empty($id)) ? $id : (int) $this->getState('module.id');
		$false		= false;

		// Get a member row instance.
		$table = &$this->getTable();

		// Attempt to load the row.
		$return = $table->load($id);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		// Convert the params field to an array.
		$xml = $this->_getXML($table->module);
		$table->params = $this->_getParams($table->params, $xml);

		$value = JArrayHelper::toObject($table->getProperties(1), 'JObject');
		return $value;
	}

	protected function _getXML($module)
	{
		$path		= ($this->getState('client.id') == 1) ? 'mod1_xml' : 'mod0_xml';
		$xmlpath 	= JApplicationHelper::getPath($path, $module);
		$return = false;

		if (file_exists($xmlpath))
		{
			$parser = &JFactory::getXMLParser('Simple');
			if ($parser->loadFile($xmlpath)) {
				$return = &$parser;
			}
		}

		return $return;
	}

	/**
	 *
	 * @param $params Module parameters in JSON
	 * @return JParameter
	 */
	protected function _getParams($params, $xml)
	{
		$params	= new JParameter($params);

		if ($xml)
		{
			if ($ps = &$xml->document->params) {
				foreach ($ps as $p) {
					$params->setXML($p);
				}
			}
		}
		return $params;
	}

	/**
	 * Method to store the module
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function store($data)
	{
		$row = &JTable::getInstance('module');

		// Bind the form fields to the web link table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the data table is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// if new item, order last in appropriate group
		if (!$row->id) {
			$where = 'position='.$this->_db->Quote($row->position).' AND client_id='.(int) $this->_client->id ;
			$row->ordering = $row->getNextOrder($where);
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$menus = JRequest::getVar('menus', '', 'post', 'word');
		$selections = JRequest::getVar('selections', array(), 'post', 'array');
		JArrayHelper::toInteger($selections);

		// delete old module to menu item associations
		$query = 'DELETE FROM #__modules_menu'
		. ' WHERE moduleid = '.(int) $row->id
		;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return JError::raiseWarning(500, $row->getError());
		}

		// check needed to stop a module being assigned to `All`
		// and other menu items resulting in a module being displayed twice
		if ($menus == 'all') {
			// assign new module to `all` menu item associations
			$query = 'INSERT INTO #__modules_menu'
			. ' SET moduleid = '.(int) $row->id.' , menuid = 0'
			;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return JError::raiseWarning(500, $row->getError());
			}
		}
		else
		{
			$sign = ($menus == 'deselect') ? -1 : 1;
			foreach ($selections as $menuid)
			{
				/*
				 * This checks for the blank spaces in the select box that have
				 * been added for cosmetic reasons.
				 */
				$menuid = (int) $menuid;
				if ($menuid >= 0) {
					// assign new module to menu item associations
					$query = 'INSERT INTO #__modules_menu'
					. ' SET moduleid = ' . (int) $row->id . ', menuid = ' . ($sign * $menuid)
					;
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						return JError::raiseWarning(500, $row->getError());
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to remove a module
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	public function delete($cid = array())
	{
		JArrayHelper::toInteger($cid);

		$cids = implode(',', $cid);
		// remove mappings first (lest we leave orphans)
		$query = 'DELETE FROM #__modules_menu'
			. ' WHERE moduleid IN ('.$cids.')'
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// remove module
		$query = 'DELETE FROM #__modules'
			. ' WHERE id IN ('.$cids.')'
			;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to copy modules
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function copy($cid = array())
	{
		$row 	= &JTable::getInstance('module');
		$tuples	= array();

		foreach ($cid as $id)
		{
			// load the row from the db table
			$row->load((int) $id);
			$row->title 		= JText::sprintf('Copy of', $row->title);
			$row->id 			= 0;
			$row->iscore 		= 0;
			$row->published 	= 0;

			if (!$row->check()) {
				return JError::raiseWarning(500, $row->getError());
			}
			if (!$row->store()) {
				return JError::raiseWarning(500, $row->getError());
			}
			$row->checkin();

			$row->reorder('position='.$this->_db->Quote($row->position).' AND client_id='.(int) $client->id);

			$query = 'SELECT menuid'
			. ' FROM #__modules_menu'
			. ' WHERE moduleid = '.(int) $cid[0]
			;
			$this->_db->setQuery($query);
			$rows = $this->_db->loadResultArray();

			foreach ($rows as $menuid) {
				$tuples[] = '('.(int) $row->id.','.(int) $menuid.')';
			}
		}

		if (!empty($tuples))
		{
			// Module-Menu Mapping: Do it in one query
			$query = 'INSERT INTO #__modules_menu (moduleid,menuid) VALUES '.implode(',', $tuples);
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return JError::raiseWarning(500, $row->getError());
			}
		}

		return true;
	}

	/**
	 * Method to move a module
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function move($direction)
	{
		$row = &JTable::getInstance('module');
		if (!$row->load($this->_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move($direction, 'position = '.$this->_db->Quote($row->position).' AND client_id='.(int) $client->id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param	int		$weblinkId	The numeric id of the weblink to move.
	 * @param	int		$direction	The direction to move the row (-1/1).
	 * @return	bool	True on success/false on failure
	 */
	public function reorder($id, $direction)
	{
		// Get a WeblinksTableWeblink instance.
		$table = &$this->getTable();

		$id	= (int) $id;

		if ($id === 0) {
			$id = $this->getState('module.id');
		}

		// Attempt to check-out and move the row.
		if (!$this->checkout($id)) {
			return false;
		}

		// Load the row.
		if (!$table->load($id)) {
			$this->setError($table->getError());
			return false;
		}

		// Move the row.
		$table->move($direction, 'position = '.$this->_db->Quote($table->position).' AND client_id='.(int) $table->client_id);

		// Check-in the row.
		if (!$this->checkin($id)) {
			return false;
		}

		return true;
	}


	/**
	 * Method to move a module
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function saveorder($cid, $order)
	{
		$total		= count($cid);

		$row 		= &JTable::getInstance('module');
		$groupings = array();

		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load((int) $cid[$i]);
			// track postions
			$groupings[] = $row->position;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseWarning(500, $this->_db->getErrorMsg());
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique($groupings);
		foreach ($groupings as $group){
			$row->reorder('position = '.$this->_db->Quote($group).' AND client_id = '.(int) $client->id);
		}

		return true;
	}
}
