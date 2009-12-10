<?php
/**
 * @version		$Id: default.php 12432 2009-07-04 06:05:14Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>



<h1>
<? echo $this->document->title; ?>
</h1>


<form action="<?php echo JRoute::_('index.php?option=com_users&task=member.remind'); ?>" method="post" class="form-validate">

<?php foreach ($this->form->getGroups() as $group): ?>
	<fieldset>
		<legend><?php echo JText::_('PASSWORD_REMIND'); ?></legend>
		<?php foreach ($this->form->getFields($group, $group) as $name => $field): ?>
			<div class="formelm">
			<?php echo $field->label; ?>
			<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>

	</fieldset>
<?php endforeach; ?>

	<button type="submit"><?php echo JText::_('BUTTON_SUBMIT'); ?></button>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="member.remind" />
	<?php echo JHtml::_('form.token'); ?>
</form>