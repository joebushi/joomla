<?php
/**
 * @version		$Id: default.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if ($this->user->get('guest')):
	echo $this->loadTemplate('login');
else:
	echo $this->loadTemplate('logout');
endif;
?>