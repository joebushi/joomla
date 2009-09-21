<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
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
		// @todo Validation is currently busted
		//if (task == 'newsfeed.cancel' || document.formvalidator.isValid($('newsfeed-form'))) {
		if (task == 'newsfeed.cancel') {
			submitform(task);
		}
		// @todo Deal with the editor methods
		submitform(task);
	}
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_newsfeeds'); ?>" method="post" name="adminForm" id="newsfeed-form" class="form-validate">
<div class="col width-60">
	<fieldset>
		<legend><?php echo empty($this->item->id) ? JText::_('Newsfeeds_New_Newsfeed') : JText::sprintf('Newsfeeds_Edit_Newsfeed', $this->item->id); ?></legend>

		<div>
			<?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('link'); ?>
			<?php echo $this->form->getInput('link'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('numarticles'); ?>
			<?php echo $this->form->getInput('numarticles'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('cache_time'); ?>
			<?php echo $this->form->getInput('cache_time'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?>
		</div>
		<div>
			<?php echo $this->form->getLabel('rtl'); ?>
			<?php echo $this->form->getInput('rtl'); ?>
		</div>

	</fieldset>
</div>

<div class="col width-40">
	<fieldset>
		<legend><?php echo JText::_('Newsfeeds_Options'); ?></legend>

		
		<?php foreach($this->form->getFields('params') as $field): ?>
			<?php if ($field->hidden): ?>
				<?php echo $field->input; ?>
			<?php else: ?>
				<div>
					<div class="paramlist_key" width="40%">
						<?php echo $field->label; ?>
					</div>
					<div class="paramlist_value">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		

	</fieldset>
</div>

<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>