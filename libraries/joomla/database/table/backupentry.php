<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

/**
 * @package		Joomla.Framework
 * @subpackage	Table
 */
class JTableBackupEntry extends JTable
{
	/**
	 * @var int unsigned
	 */
	protected $entryid = null;
	/**
	 * @var int unsigned
	 */
	protected $backupid = null;
	/**
	 * @var varchar
	 */
	protected $type = null;
	/**
	 * @var varchar
	 */
	protected $name = null;
	/**
	 * @var text
	 */
	protected $data = null;
	/** @var text */
	protected $params = null;

	/*
	 * Constructor
	 * @param object Database object
	 */
	protected function __construct(&$db)
	{
		parent::__construct('#__backup_entries', 'entryid', $db);
	}
}