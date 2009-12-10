<?php
/**
 * @version		$Id: default_login.php 12432 2009-07-04 06:05:14Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
<h1 class="<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php echo $this->params->get('page_title'); ?>
</h1>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post">

	<?php foreach ($this->form->getGroups() as $group): ?>
	<fieldset>
	<legend><?php echo JText::_('LOGIN'); ?></legend>

		<?php foreach ($this->form->getFields($group, $group) as $name => $field): ?>
			<?php if (!$field->hidden): ?>
				<div class="formelm">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

	</fieldset>
	<?php endforeach; ?>

	<button type="submit">Submit</button>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<?php echo JHtml::_('form.token'); ?>
</form>