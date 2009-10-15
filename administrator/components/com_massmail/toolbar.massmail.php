<?php
/**
 * @version		$Id: toolbar.massmail.php 13109 2009-10-08 18:15:33Z ian $
 * @package		Joomla.Administrator
 * @subpackage	Massmail
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JApplicationHelper::getPath('toolbar_html');

switch ($task)
{
	default:
		TOOLBAR_massmail::_DEFAULT();
		break;
}