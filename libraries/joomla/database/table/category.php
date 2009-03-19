<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Table
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License, see LICENSE.php
*/

// No direct access
defined('JPATH_BASE') or die();

JLoader::register('JHardlinkedTable', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'nbs'.DS.'hardlinked.php');

/**
 * Category table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableCategory extends JHardlinkedTable
{
	/** @var int Primary key */
	protected $id					= null;
	protected $lft					= null;
	protected $rgt					= null;
	/** @var int */
	protected $extension			= null;
	protected $lang					= null;
	/** @var string The menu title for the category (a short name)*/
	protected $title				= null;
	/** @var string The the alias for the category*/
	protected $alias				= null;
	/** @var string */
	protected $description			= null;
	/** @var boolean */
	protected $published			= null;
	/** @var boolean */
	protected $checked_out			= 0;
	/** @var time */
	protected $checked_out_time		= 0;
	/** @var int */
	protected $access				= null;
	/** @var string */
	protected $params				= null;

	/**
	* @param database A database connector object
	*/
	public function __construct(&$db)
	{
		parent::__construct(array('table' => '#__categories', 'key' => 'id', 'db' => $db, 'nbsTable' => '#__categories'));
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->title) == '') {
			$this->setError(JText::sprintf('must contain a title', JText::_('Category')));
			return false;
		}

		// check for existing name
		/*$query = 'SELECT id'
		. ' FROM #__categories '
		. ' WHERE title = '.$this->_db->Quote($this->title)
		. ' AND section = '.$this->_db->Quote($this->section)
		;
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->_error = JText::sprintf('WARNNAMETRYAGAIN', JText::_('Category'));
			return false;
		}*/

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}
}
