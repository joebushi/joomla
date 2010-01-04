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
	<!-- File Upload Form -->
	<form action="<?php echo JURI::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component" id="uploadForm" method="post" enctype="multipart/form-data">
		<fieldset id="uploadform">
			<legend><?php echo JText::_('UPLOAD_FILE'); ?> (<?php echo JText::_('MAXIMUM_SIZE'); ?>:&nbsp;<?php echo ($this->state->params->get('upload_maxsize') / 1000000); ?>MB)</legend>
			<fieldset id="upload-noflash" class="actions">
				<label for="upload-file" class="hidelabeltxt"><?php echo JText::_('UPLOAD_FILE'); ?></label>
				<input type="file" id="upload-file" name="Filedata" />
				<label for="upload-submit" class="hidelabeltxt"><?php echo JText::_('START_UPLOAD'); ?></label>
				<input type="submit" id="upload-submit" value="<?php echo JText::_('START_UPLOAD'); ?>"/>
			</fieldset>
			<div id="upload-flash" class="hide">
				<ul>
					<li><a href="#" id="upload-browse">Browse Files</a></li>
					<li><a href="#" id="upload-clear">Clear List</a></li>
					<li><a href="#" id="upload-start">Start Upload</a></li>
				</ul>
				<div class="clr"> </div>
				<p class="overall-title"></p>
				<img src="../media/media/images/bar.gif" alt="<?php echo JText::_('OVERALL_PROGRESS'); ?>" class="progress overall-progress" />
				<div class="clr"> </div>
				<p class="current-title"></p>
				<img src="../media/media/images/bar.gif" alt="<?php echo JText::_('CURRENT_PROGRESS'); ?>" class="progress current-progress" />
				<p class="current-text"></p>
			</div>
			<ul class="upload-queue" id="upload-queue">
				<li style="display:none;" />
			</ul>
		</fieldset>
		<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
