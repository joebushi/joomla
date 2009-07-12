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
 * QuickIcons Component Sections Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 * @since		1.6
 */
class QuickIconsModelSections extends JModelList
{
	public function &getTable()
	{
		return JTable::getInstance('Section', 'QuickIconsTable');
	}
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_quickicons.sections';
	
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
	 * Method to publish the sections
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
	 * Method to unpublish the sections
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
	 * Method to save the order of sections
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
			$this->setError('QuickIcons_No_Sections_Selected');
			return false;
		}
		$total = count($selected);
		for ($i = 0;$i < $total;$i++)
		{
			$table->load((int)$selected[$i]);
			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];
				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}
			}
		}
		$table->reorder();
		return true;
	}
	
	/**
	 * Method to reorder a section
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
			$this->setError('QuickIcons_No_Sections_Selected');
			return false;
		}
		// Load the row.
		if (!$table->load((int)$selected[0]))
		{
			$this->setError($table->getError());
			return false;
		}

		// Move the row.
		if (!$table->move($direction))
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
		$query->select('*');
		$query->from('`#__quickicons_sections`');
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

