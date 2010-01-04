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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.framework', true);
JHtml::script('mediamanager.js', 'media/media/js/');
JHtml::stylesheet('mediamanager.css', 'media/media/css/');

JHtml::_('behavior.modal');
$this->document->addScriptDeclaration("
window.addEvent('domready', function() {
	document.preview = SqueezeBox;
});");

JHtml::script('mootree.js');
JHtml::stylesheet('mootree.css');

// Optionally enable the flash uploader.
if ($this->state->params->get('enable_flash')) :
	echo JHtml::_('media.flash');
endif;

JHtml::_('behavior.keepalive');

?>
<table width="100%">
	<tr valign="top">
		<td width="200">
			<fieldset id="treeview">
				<legend><?php echo JText::_('Folders'); ?></legend>
				<div id="media-tree_tree"></div>
				<?php echo $this->loadTemplate('folders'); ?>
			</fieldset>
		</td>
		<td>
			<?php if ($this->require_ftp): ?>
				<?php echo $this->loadTemplate('ftp'); ?>
			<?php endif; ?>

			<form action="index.php?option=com_media" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data" >
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="cb1" id="cb1" value="0" />
				<input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->folder; ?>" />
			</form>

 			<form action="<?php echo JRoute::_('index.php?option=com_media&task=folder.create');?>'" name="folderForm" id="folderForm" method="post">
				<fieldset id="folderview">
					<div class="view">
						<iframe src="index.php?option=com_media&amp;view=folder&amp;tmpl=component&amp;folder=<?php echo $this->state->get('media.folder');?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0"></iframe>
					</div>
					<legend><?php echo JText::_('Files'); ?></legend>
					<div class="path">
						<input class="inputbox" type="text" id="folderpath" readonly="readonly" />/
						<input class="inputbox" type="text" id="foldername" name="foldername"  />
						<input class="update-folder" type="hidden" name="folderbase" id="folderbase" value="<?php echo $this->state->get('media.folder'); ?>" />
						<button type="submit"><?php echo JText::_('Create Folder'); ?></button>
					</div>

				</fieldset>
				<?php echo JHtml::_('form.token'); ?>
			</form>

			<?php echo $this->loadTemplate('upload'); ?>
		</td>
	</tr>
</table>



