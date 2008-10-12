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
 * Module table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableModule extends JTable
{
	/** @var int Primary key */
	public $id					= null;
	/** @var string */
	public $title				= null;
	/** @var string */
	public $showtitle			= null;
	/** @var int */
	public $content			= null;
	/** @var int */
	public $ordering			= null;
	/** @var string */
	public $position			= null;
	/** @var boolean */
	public $checked_out		= 0;
	/** @var time */
	public $checked_out_time	= 0;
	/** @var boolean */
	public $published			= null;
	/** @var string */
	public $module				= null;
	/** @var int */
	public $numnews			= null;
	/** @var int */
	public $access				= null;
	/** @var string */
	public $params				= null;
	/** @var string */
	public $iscore				= null;
	/** @var string */
	public $client_id			= null;
	/** @var string */
	public $control				= null;

	/**
	 * Contructore
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	protected function __construct( &$db ) {
		parent::__construct( '#__modules', 'id', $db );
	}

	/**
	* Overloaded check function
	*
	* @access public
	* @return boolean True if the object is ok
	* @see JTable:bind
	*/
	public function check()
	{
		// check for valid name
		if (trim( $this->title ) == '') {
			$this->setError(JText::sprintf( 'must contain a title', JText::_( 'Module') ));
			return false;
		}

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

		if (isset( $array['control'] ) && is_array( $array['control'] ))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['control']);
			$array['control'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
