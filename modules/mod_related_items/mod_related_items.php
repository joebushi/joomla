<?php
/**
 * @version		$Id: mod_related_items.php 12668 2009-08-31 00:00:25Z pentacle $
 * @package		Joomla.Site
 * @subpackage	mod_related_items
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

$list = modRelatedItemsHelper::getList($params);

if (!count($list)) {
	return;
}

$showDate = $params->get('showDate', 0);

require JModuleHelper::getLayoutPath('mod_related_items');
