<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Banner table
 *
 * @package		Joomla.Framework
 * @subpackage	com_banners
 * @since		1.6
 */
class BannersTableBanner extends JTable
{
	/** @var int */
	var $id				= null;
	/** @var int */
	var $cid				= null;
	/** @var string */
	var $type				= '';
	/** @var string */
	var $name				= '';
	/** @var string */
	var $alias				= '';
	/** @var int */
	var $imptotal			= 0;
	/** @var int */
	var $impmade			= 0;
	/** @var int */
	var $clicks				= 0;
	/** @var string */
	var $imageurl			= '';
	/** @var string */
	var $clickurl			= '';
	/** @var date */
	var $date				= null;
	/** @var int */
	var $state			= 0;
	/** @var int */
	var $checked_out		= 0;
	/** @var date */
	var $checked_out_time	= 0;
	/** @var string */
	var $custombannercode	= '';
	/** @var int */
	var $catid				= null;
	/** @var string */
	var $description		= null;
	/** @var int */
	var $sticky				= null;
	/** @var int */
	var $ordering			= null;
	/** @var date */
	var $publish_up			= null;
	/** @var date */
	var $publish_down		= null;
	/** @var string */
	var $tags				= null;
	/** @var string */
	var $params				= null;

	function __construct(&$_db)
	{
		parent::__construct('#__banners', 'id', $_db);

		$this->set('date', JFactory::getDate()->toMySQL());
	}

	function clicks()
	{
		$query = 'UPDATE #__banners'
		. ' SET clicks = (clicks + 1)'
		. ' WHERE id = ' . (int) $this->id
		;
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		/*if (trim($this->imageurl) == '') {
			$this->setError(JText::_('BNR_IMAGE'));
			return false;
		}
		if (trim($this->clickurl) == '' && trim($this->custombannercode) == '') {
			$this->setError(JText::_('BNR_URL'));
			return false;
		}*/

		return true;
	}
	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param	mixed	An optional array of primary key values to update.  If not
	 * 					set the instance property value is used.
	 * @param	integer The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param	integer The user id of the user performing the operation.
	 * @return	boolean	True on success.
	 * @since	1.0.4
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialize variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('No_Rows_Selected'));
				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k.'='.implode(' OR '.$k.'=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
			$checkin = '';
		}
		else {
			$checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE `'.$this->_tbl.'`' .
			' SET `state` = '.(int) $state .
			' WHERE ('.$where.')' .
			$checkin
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->state = $state;
		}

		$this->setError('');
		return true;
	}
	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '') 
	{
		if (isset($array['params']) && is_array($array['params'])) 
		{
			// Convert the params field to an string.
			$parameter = new JParameter;
			$parameter->loadArray($array['params']);
			$array['params'] = $parameter->toString();
		}
		return parent::bind($array, $ignore);
	}
	/**
	 * Overloaded load function
	 *
	 * @param	int $pk primary key
	 * @param	boolean $reset reset data
	 * @return	boolean
	 * @see JTable:load
	 */
	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{
			// Convert the params field to a parameter.
			$parameter = new JParameter;
			$parameter->loadJSON($this->params);
			$this->params = $parameter;
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	 * method to store a row
	 *
	 * @param boolean $updateNulls True to update fields even if they are null.
	 */
	function store($updateNulls = false) 
	{
		if (empty($this->id)) 
		{
			$this->ordering = $this->getNextOrder('`catid`=' . $this->_db->Quote($this->catid));
			return parent::store($updateNulls);
		}
		else
		{
			$oldrow = & JTable::getInstance('Banner', 'BannersTable');
			if (!$oldrow->load($this->id) && $oldrow->getError()) 
			{
				$this->setError($oldrow->getError());
				return false;
			}
			if ($oldrow->catid != $this->catid) 
			{
				$this->ordering = $this->getNextOrder('`catid`=' . $this->_db->Quote($this->catid));
				if (!parent::store($updateNulls)) 
				{
					return false;
				}
				if (!$oldrow->reorder('`catid`=' . $this->_db->Quote($oldrow->catid))) 
				{
					$this->setError($oldrow->getError());
					return false;
				}
				return true;
			}
			else
			{
				return parent::store($updateNulls);
			}
		}
	}
}

