<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<dl class="menu_types">
<?php foreach ($this->types as $name => $list) : ?>
	<dt><?php echo $name; ?></dt>
	<dd>
		<ul>
<?php foreach ($list as $item) : ?>
			<li>
				<a class="choose_type" href="index.php?option=com_menus&amp;task=item.setType&amp;type=<?php echo base64_encode(json_encode($item->request)); ?>" title="<?php echo $item->description; ?>"><?php echo $item->title; ?></a>
			</li>
<?php endforeach; ?>
		</ul>
	</dd>
<?php endforeach; ?>
</dl>
