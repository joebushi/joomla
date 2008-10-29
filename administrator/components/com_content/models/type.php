<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.database.query');

class ContentModelType extends JModel
{
	protected $_data = null;

	public function getTable()
	{
		return parent::getTable('ContentType');
	}

	public function getTablePrefix()
	{
		return $this->_db->replacePrefix('#__content_type_');
	}

	public function getData()
	{
		if (empty($this->_data)) {
			$query = 'SELECT * FROM #__content_types WHERE id = ' . (int) $this->getState('id', 0);
			$this->_db->setQuery($query);

			try {
				$this->_data = $this->_db->loadObject();
			} catch (JException $e) {
				$this->_data = null;
			}
		}

		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->name = null;
			$this->_data->table_name = null;
			$this->_data->description = null;
			$this->_data->ordering = 0;
			$this->_data->params = null;
		}
		return $this->_data;
	}

	public function store($data)
	{
		$table = $this->getTable();

		if (!$table->bind($data)) {
			throw new JException($table->getError());
		}

		if (!$table->check()) {
			throw new JException($table->getError());
		}

		if (!$table->store()) {
			throw new JException($table->getError());
		}

		$this->setState('id', (int) $table->id);

		return $table->id;
	}

	public function delete($cid)
	{
		$table = $this->getTable();

		foreach ($cids as $c) {
			if (!$table->canDelete($c) || !$table->delete($c)) {
				throw new JException($table->getError());
			}
		}

		return true;
	}

	public function saveorder($cid = array(), $order)
	{
		$row = $this->getTable();
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		$row->reorder();

		return true;
	}


	public function move($direction)
	{
		$table = $this->getTable();

		if (!$table->load($this->_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		if (!$table->move( $direction, '' )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}