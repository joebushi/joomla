<?php
/**
 * @version		$Id: mod_stats.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	mod_stats
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

$serverinfo = $params->get('serverinfo');
$siteinfo 	= $params->get('siteinfo');

$list = modStatsHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_stats');
