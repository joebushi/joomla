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
class QuickIconsTableQuickIcons extends JTable
{
	/**
	 * @var string Primary key
	 */
	var $id	= null;

	/**
	 * @var int section id
	 */
	var $sid = null;
	
	/**
	 * @var string icon name
	 */
	var $text = null;
	
	/**
	 * @var string icon link
	 */
	var $link = null;
	
	/**
	 * @var string icon image
	 */
	var $image = null;
	
	/**
	 * @var int
	 */
	var $ordering	= null;

	/**
	 * @var int
	 */
	var $published	= null;
	
	/**
	 * @var string title
	 */
	var $title = null;

	/**
	 * @var string component
	 */
	var $component = null;

	/**
	 * @var string alt key
	 */
	var $key = null;

	/**
	 * @var string access authorization
	 */
	var $access = null;

	/**
	 * @var string default path for image
	 */
	var $default_path = null;

	/**
	 * @var string template path for image
	 */
	var $template_path = null;	

	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__quickicons', 'id', $db);
	}
}
