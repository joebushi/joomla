<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_quickicons'.DS.'helpers'.DS.'quickicons.php';

$sections = &QuickIconsHelper::getPublishedSections();

require JModuleHelper::getLayoutPath('mod_quickicon');
