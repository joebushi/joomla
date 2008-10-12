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
 * Plugin table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTablePlugin extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	public $id = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	public $name = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	public $element = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	public $folder = null;

	/**
	 *
	 *
	 * @var tinyint unsigned
	 */
	public $access = null;

	/**
	 *
	 *
	 * @var int
	 */
	public $ordering = null;

	/**
	 *
	 *
	 * @var tinyint
	 */
	public $published = null;

	/**
	 *
	 *
	 * @var tinyint
	 */
	public $iscore = null;

	/**
	 *
	 *
	 * @var tinyint
	 */
	public $client_id = null;

	/**
	 *
	 *
	 * @var int unsigned
	 */
	public $checked_out = 0;

	/**
	 *
	 *
	 * @var datetime
	 */
	public $checked_out_time = 0;

	/**
	 *
	 *
	 * @var text
	 */
	public $params = null;

	protected function __construct(& $db) {
		parent::__construct('#__plugins', 'id', $db);
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
		if (isset( $array['params'] ) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
