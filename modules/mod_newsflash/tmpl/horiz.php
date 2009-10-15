<?php
/**
 * @version		$Id: horiz.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	mod_newsflash
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<table class="moduletable<?php echo $params->get('moduleclass_sfx') ?>">
	<tr>
	<?php foreach ($list as $item) : ?>
		<td>
			<?php modNewsFlashHelper::renderItem($item, $params, $access); ?>
		</td>
	<?php endforeach; ?>
	</tr>
</table>