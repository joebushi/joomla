<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'models'.DS.'_prototypeitem.php');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 */
class AccessModelGroup extends AccessModelPrototypeItem
{
	/**
	 * The current item
	 *
	 * @var JTableAcl
	 */
	protected $_item = null;

	/**
	 * Proxy for getTable
	 */
	function &getTable()
	{
		$type = $this->getState('group_type');
		echo $type;
		return JTable::getInstance($type.'Group');
	}

	/**
	 * @param	boolean	True to resolve foreign data relationship
	 *
	 * @return	JStdClass
	 */
	function &getItem()
	{
		if (empty($this->_item))
		{
			$session = &JFactory::getSession();
			$id = (int) $session->get( 'com_acl.group.id', $this->getState('id') );

			$table = $this->getTable();
			if (!$table->load($id)) {
				$this->setError($table->getError());
			}
			$this->_item = JArrayHelper::toObject($table->getProperties(1), 'JStdClass');
		}
		return $this->_item;
	}

	/**
	 * Save override
	 */
	function save( $input )
	{
		$result	= true;
		$user	= &JFactory::getUser();
		$table	= &$this->getTable();

		if (!$table->save( $input )) {
			$result	= JError::raiseWarning( 500, $table->getError() );
		}
		else {
			//$table->rebuild();
		}
		// Set the new id (if new)
		$this->setState('id', $table->id);

		return $result;
	}
}
