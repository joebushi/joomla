<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * @package		Joomla.Framework
 * @subpackage	Table
 */
class JTableACL extends JTable
{
/**
	 * @var int unsigned
	 */
	public $id = null;
	/**
	 * @var varchar
	 */
	public $section_value = null;
	/**
	 * @var int unsigned
	 */
	public $allow = null;
	/**
	 * @var int unsigned
	 */
	public $enabled = null;
	/**
	 * @var varchar
	 */
	public $return_value = null;
	/**
	 * @var varchar
	 */
	public $note = null;
	/**
	 * @var int unsigned
	 */
	public $updated_date = null;

	/*
	 * Constructor
	 * @param object Database object
	 */
	protected function __construct( &$db )
	{
		parent::__construct( '#__core_acl_acl', 'id', $db );
	}
}
