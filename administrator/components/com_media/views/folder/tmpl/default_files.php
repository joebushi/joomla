<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<?php foreach ($this->files as &$file) : ?>
		<tr>
			<td>
				<a>
					<img src="<?php echo $file->icon_16; ?>" width="16" height="16" border="0" alt="<?php echo $file->name; ?>" /></a>
			</td>
			<td class="description">
				<a href="<?php echo  COM_MEDIA_BASEURL.$file->path_relative; ?>" title="<?php echo $file->name; ?>" rel="preview"><?php echo $this->escape($file->name); ?></a>
			</td>
			<td>
				<?php echo $file->width; ?> x <?php echo $file->height; ?>
			</td>
			<td>
				<?php echo MediaHelper::parseSize($file->size); ?>
			</td>
			<td>
				<a class="delete-item" href="index.php?option=com_media&amp;task=file.delete&amp;tmpl=component&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $file->name; ?>" rel="<?php echo $file->name; ?>">
					<img src="../media/media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_('Delete'); ?>" /></a>
				<input type="checkbox" name="rm[]" value="<?php echo $file->name; ?>" />
			</td>
		</tr>
<?php endforeach; ?>
