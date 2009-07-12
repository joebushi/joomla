<?php
/**
 * @version		$Id: featured.php 12175 2009-06-19 23:52:21Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 */
class QuickIconsTableSection extends JTable
{
	/**
	 * @var string Primary key
	 */
	var $id	= null;

	/**
	 * @var string Section name
	 */
	var $name	= null;

	/**
	 * @var int
	 */
	var $ordering	= null;

	/**
	 * @var int
	 */
	var $published	= null;

	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__quickicons_sections', 'id', $db);
	}
}
