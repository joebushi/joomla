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
<?php foreach ($this->folders as &$folder) : ?>
		<tr>
			<td class="imgTotal">
				<a href="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $folder->path_relative; ?>" target="folderframe">
					<img src="../media/media/images/folder_sm.png" width="16" height="16" border="0" alt="<?php echo $folder->name; ?>" /></a>
			</td>
			<td class="description">
				<a href="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $folder->path_relative; ?>" target="folderframe"><?php echo $folder->name; ?></a>
			</td>
			<td>&nbsp;

			</td>
			<td>&nbsp;

			</td>
			<td>
				<a class="delete-item" href="index.php?option=com_media&amp;task=folder.delete&amp;tmpl=component&amp;folder=<?php echo $this->state->folder; ?>&amp;<?php echo JUtility::getToken(); ?>=1&amp;rm[]=<?php echo $folder->name; ?>" rel="<?php echo $folder->name; ?>' :: <?php echo $folder->files+$folder->folders; ?>"><img src="../media/media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_('Delete'); ?>" /></a>
				<input type="checkbox" name="rm[]" value="<?php echo $folder->name; ?>" />
			</td>
		</tr>
<?php endforeach; ?>
