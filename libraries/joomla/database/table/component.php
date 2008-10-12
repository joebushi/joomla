<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Table
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Component table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableComponent extends JTable
{
	/** @var int Primary key */
	public $id					= null;
	/** @var string */
	public $name				= null;
	/** @var string */
	public $link				= null;
	/** @var int */
	public $menuid				= null;
	/** @var int */
	public $parent				= null;
	/** @var string */
	public $admin_menu_link	= null;
	/** @var string */
	public $admin_menu_alt		= null;
	/** @var string */
	public $option				= null;
	/** @var string */
	public $ordering			= null;
	/** @var string */
	public $admin_menu_img		= null;
	/** @var int */
	public $iscore				= null;
	/** @var string */
	public $params				= null;
	/** @var int */
	public $enabled			= null;

	/**
	* @param database A database connector object
	*/
	protected function __construct( &$db ) {
		parent::__construct( '#__components', 'id', $db );
	}

	/**
	 * Loads a data row by option
	 *
	 * @param string The component option value
	 * @return boolean
	 */
	public function loadByOption( $option )
	{
		$db = &$this->getDBO();
		$query = 'SELECT id' .
				' FROM #__components' .
				' WHERE ' . $db->nameQuote( 'option' ) . '=' . $db->Quote( $option ) .
				' AND parent = 0';
		$db->setQuery( $query, 0, 1 );
		$id = $db->loadResult();

		if ($id === null) {
			return false;
		} else {
			return $this->load( $id );
		}
	}

	/**
	 * Validate and filter fields
	 */
	public function check()
	{
		$this->parent = intval( $this->parent );
		$this->ordering = intval( $this->ordering );
		return true;
	}

	/**
	* Overloaded bind function
	*
	* @access public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	public function bind($array, $ignore = '')
	{
		if (is_array( $array['params'] ))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
