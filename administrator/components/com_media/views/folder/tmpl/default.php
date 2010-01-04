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

JHtml::stylesheet('medialist-', 'media/media/css/');
$this->document->addScriptDeclaration("
window.addEvent('domready', function() {
	window.top.document.updateUploader && window.top.document.updateUploader();
	$$('a.img-preview').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			window.top.document.preview.fromElement(el);
		});
	});
});");

?>
<?php echo $this->state->get('media.folder');?>

<form action="<?php echo JRoute::_('index.php?option=com_media&tmpl=component&folder='.$this->state->get('media.folder')); ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<div class="manager">
		<table width="100%" cellspacing="0">
			<thead>
				<tr>
					<th><?php echo JText::_('Preview'); ?></th>
					<th><?php echo JText::_('Name'); ?></th>
					<th><?php echo JText::_('Pixel_Dimensions'); ?></th>
					<th><?php echo JText::_('File_Size'); ?></th>
					<th><?php echo JText::_('Delete'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($this->state->get('media.folder.parent')) : ?>
					<?php echo $this->loadTemplate('up'); ?>
				<?php endif; ?>

				<?php echo $this->loadTemplate('folders'); ?>

				<?php echo $this->loadTemplate('files'); ?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="username" value="" />
	<input type="hidden" name="password" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

