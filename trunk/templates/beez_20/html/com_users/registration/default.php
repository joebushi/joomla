<?php
/**
 * @version		$Id: default.php 11952 2009-06-01 03:21:19Z robs $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.mootools');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
<h1 class="<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php echo $this->params->get('page_title'); ?>
</h1>
<?php endif; ?>
<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate">
	<?php
	// Iterate through the form fieldsets and display each one.
	foreach ($this->form->getFieldsets() as $group => $fieldset):
	?>
	<fieldset>
		<?php
		// If the fieldset has a label set, display it as the legend.
		if (isset($fieldset['label'])):
		?>
		<legend><?php echo JText::_($fieldset['label']); ?></legend>


		<?php
		endif;

		// Iterate through the fields in the set and display them.
		foreach($this->form->getFields($group) as $field):
			// If the field is hidden, just display the input.
			if ($field->hidden):
				echo $field->input;
			else:
			?>
			<div class="formelm">

					<?php echo $field->label; ?>
					<?php if (!$field->required): ?>
					<span class="optional"><?php echo JText::_('USERS OPTIONAL'); ?></span>
					<?php endif; ?>

					<?php echo $field->input; ?>

			</div>
			<?php
			endif;
		endforeach;
		?>

	</fieldset>
	<?php
	endforeach;
	?>

	<button type="submit" class="validate"><?php echo JText::_('REGISTER'); ?></button>
	<button type="reset" class="validate"><?php echo JText::_('DELETE_FORM'); ?></button>
	<a href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('CANCEL'); ?>"><?php echo JText::_('CANCEL'); ?></a>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="registration.register" />
	<?php echo JHtml::_('form.token'); ?>
</form>