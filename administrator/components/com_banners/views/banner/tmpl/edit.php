<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
<!--
	function submitbutton(task)
	{
		if (task == 'banner.cancel' || document.formvalidator.isValid(document.id('banner-form'))) {
			submitform(task);
		}
		// @todo Deal with the editor methods
		submitform(task);
	}
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_banners'); ?>" method="post" name="adminForm" id="banner-form" class="form-validate">
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo empty($this->item->id) ? JText::_('Banners_New_Banner') : JText::sprintf('Banners_Edit_Banner', $this->item->id); ?></legend>

		<?php foreach($this->form->getFields() as $field): ?>
			<?php if (!$field->hidden): ?>
				<?php echo $field->label; ?>
			<?php endif; ?>
			<?php echo $field->input; ?>
		<?php endforeach; ?>

	</fieldset>
</div>
<div class="width-40 fltrt">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Banners_Options'); ?></legend>

		<?php foreach($this->form->getFields('params') as $field): ?>
			<div class="paramrow">
			<?php if (!$field->hidden): ?>
				<?php echo $field->label; ?>
			<?php endif; ?>
			<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>

	</fieldset>
</div>

<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
