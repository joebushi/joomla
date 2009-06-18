<?php
/**
 * @version		$Id: default.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	mod_latestnews
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<ul class="blah<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($list as $item) :  ?>
	<li class="latestnews<?php echo $params->get('moduleclass_sfx'); ?>">
		<?php echo str_repeat('&nbsp;', $item->level - 1); ?><a href="<?php echo $item->link; ?>" class="blah<?php echo $params->get('moduleclass_sfx'); ?>">
			<?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ul>