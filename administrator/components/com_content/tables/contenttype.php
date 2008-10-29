<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class TableContentType extends JTable
{
	protected $id = null;

	protected $name = null;

	protected $table_name = null;

	protected $description = null;

	protected $ordering = null;

	protected $params = null;

	protected function __construct(&$db)
	{
		parent::__construct('#__content_types', 'id', $db);
	}

	public function check()
	{
		/*
		 * @todo check for existing table name, mark table_name unique in db
		 */
		// check for valid name
		if (trim($this->name) == '') {
			$this->setError(JText::_('Content type must have a Name'));
			return false;
		}

		if (!$this->id && !preg_match('#^[a-z0-9]{3,24}$#', $this->table_name)) {
			$this->setError(JText::_('Table name is not valid'));
			return false;
		}

		if ($this->id) {
			unset($this->table_name);
		}

		return true;
	}

	public function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	public function store( $updateNulls=false )
	{
		$k = $this->_tbl_key;
		$isNew = !($this->$k);

		if (!parent::store($updateNulls)) {
			return false;
		}

		if ($isNew) {
			if (!$this->_createTable($this->table_name)) {
				return false;
			}
		}

		return true;
	}

	public function delete($oid = null)
	{
		$query = 'SELECT table_name FROM #__content_types'
			. ' WHERE id = ' . (int) $oid;
		$this->_db->setQuery($query, 0, 1);
		$table = $this->_db->loadResult();

		$query = 'DROP TABLE IF EXISTS ' . $this->_db->nameQuote('#__content_types_'.$table);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!parent::delete($oid)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// @todo move this to model
		$query = 'UPDATE #__content SET type = 0'
			. ' WHERE type = ' . (int) $oid;
		$this->_db->setQuery($query);
		try {
			$this->_db->query();
		} catch (JException $e) {
			return true;
		}

		return true;
	}

	public function canDelete($oid = null)
	{
		// @todo checks - wrap in try catch
		return true;
		$query = 'SELECT COUNT(*) FROM #__content_fields'
			. ' WHERE article_type = ' . (int) $oid;
		$this->_db->setQuery($query);
		$count = (int) $this->_db->loadResult();
		if ($count > 0) {
			$this->setError(JText::_('contains fields'));
			return false;
		}

		return true;
	}

	protected function _createTable($table)
	{
		$query = 'CREATE TABLE ' . $this->_db->nameQuote('#__content_type_'.$table) . ' ('
			. '`cid` int(11) unsigned NOT NULL,'
			. ' PRIMARY KEY (`cid`)'
			. ' ) DEFAULT CHARACTER SET utf8;';
		$this->_db->setQuery($query);
		try {
			$this->_db->query();
		} catch (JException $e) {
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}
}