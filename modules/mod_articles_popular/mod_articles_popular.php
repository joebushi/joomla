<?php
/**
 * @version		$Id: mod_articles_popular.php 13055 2009-10-04 13:58:27Z pentacle $
 * @package		Joomla.Site
 * @subpackage	mod_articles_popular
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

$list = modArticlesPopularHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_articles_popular');