<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * QuickIcons Component QuickIcons Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 * @since		1.6
 */
class QuickIconsModelQuickIcons extends JModelList
{
	public function &getTable()
	{
		return JTable::getInstance('QuickIcons', 'QuickIconsTable');
	}
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_quickicons.quickicons';
	
	/**
	 * Method to get the option
	 *
	 * @access public
	 * @return object
	 */
	function &getOption()
	{
		$option = $this->getState('option');
		return $option;
	}
	
	/**
	 * Method to publish the icons
	 *
	 * @access public
	 * @return void
	 */
	function publish()
	{
		$selected = $this->getState('selected');
		$table = $this->getTable();
		return $table->publish($selected,true);
	}	
	
	/**
	 * Method to unpublish the icons
	 *
	 * @access public
	 * @return void
	 */
	function unpublish()
	{
		$selected = $this->getState('selected');
		$table = $this->getTable();
		return $table->publish($selected,false);
	}	
	
	/**
	 * Method to save the order of icons
	 *
	 * @access public
	 * @return void
	 */
	function saveorder()
	{
		$table = & $this->getTable();
		$selected = $this->getState('selected');
		$order = $this->getState('order');
		if (empty($selected))
		{
			$this->setError('QuickIcons_No_Icons_Selected');
			return false;
		}
		$sections=array();
		$total = count($selected);
		for ($i = 0;$i < $total;$i++)
		{
			$table->load((int)$selected[$i]);
			if ($table->ordering != $order[$i])
			{
				if (!array_key_exists($table->sid,$sections)) {
					$sections[$table->sid]=true;
				}
				$table->ordering = $order[$i];
				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}
			}
		}
		foreach ($sections as $sid => $bool) {
			$table->reorder('sid='.$sid);
		}
		return true;
	}
	
	/**
	 * Method to reorder an icon
	 *
	 * @param int $direction direction
	 * @access public
	 * @return void
	 */
	 function reorder($direction=1)
	 {
		$table = & $this->getTable();
		$selected = $this->getState('selected');
		if (empty($selected))
		{
			$this->setError('QuickIcons_No_Icons_Selected');
			return false;
		}
		// Load the row.
		if (!$table->load((int)$selected[0]))
		{
			$this->setError($table->getError());
			return false;
		}

		// Move the row.
		if (!$table->move($direction,'sid='.$table->sid))
		{
			$this->setError($table->getError());
			return false;
		}
		return true;
	 }
	 
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function _getListQuery() {
		$query = new JQuery();
		$query->select('q.id as id, q.text as name, s.name as section, a.title as access, q.published as published, q.ordering as ordering, s.ordering as s_ordering, s.id as sid');
		$query->from('`#__quickicons` AS q');
		$query->join('LEFT', '#__access_actions AS a ON a.name=q.access');
		$query->join('LEFT', '#__quickicons_sections AS s ON s.key=q.skey');
		$query->order('`s_ordering` ASC');
		$query->order('`ordering` ASC');
		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
		/**
		 * Compute the pagination state
		 */
		$app = & JFactory::getApplication('administrator');
		$this->setState('list.start', $app->getUserStateFromRequest($this->_context . 'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($this->_context . 'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		/**
		 * Compute the selected state
		 */
		$cid = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cid);
		$this->setState('selected', $cid);
		/**
		 * Compute the option state
		 */
		$option = & JRequest::getCmd('option', 'com_quickicons');
		$this->setState('option',$option);
		/**
		 * Compute the order state
		 */
		$order = JRequest::getVar('order', array(), 'post', 'array');
		$this->setState('order',$order);
		 
	}
}

