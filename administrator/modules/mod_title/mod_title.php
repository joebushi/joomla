<?php
/**
 * @version		$Id: mod_title.php 12746 2009-09-13 22:04:27Z hackwar $
 * @package		Joomla.Administrator
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Get the component title div
$title = JFactory::getApplication()->get('JComponentTitle');

// Echo title if it exists
if (!empty($title)) {
	echo $title;
}