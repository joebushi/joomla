<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


/**
 * @package		Joomla.Administrator
 * @subpackage	Modules
 */
class ContactTableModulesMenu extends JTable
{
	/** @var int  */
	public $moduleid 					= null;
	/** @var int */
	public $menuid 				= null;
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__modules_menu', 'moduleid', $db);
	}


}
