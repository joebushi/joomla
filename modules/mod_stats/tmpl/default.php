<?php
/**
 * @version		$Id: default.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	mod_stats
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($list as $item) : ?>
<strong><?php echo $item->title ?></strong> : <?php echo $item->data ?><br />
<?php endforeach; ?>