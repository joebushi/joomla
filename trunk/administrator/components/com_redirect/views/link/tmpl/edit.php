<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Invalid Request.');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the behaviors.
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');


// Build the toolbar.
$this->buildDefaultToolBar();

// Get the form fields.
$fields	= $this->form->getFields();
?>
<form action="<?php echo JRoute::_('index.php?option=com_redirect');?>" method="post" name="adminForm" class="form-validate">
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('URLs'); ?></legend>
				<?php echo $fields['old_url']->label; ?>
				<?php echo $fields['old_url']->input; ?>

				<?php echo $fields['new_url']->label; ?>
				<?php echo $fields['new_url']->input; ?>

				<?php echo $fields['comment']->label; ?>
				<?php echo $fields['comment']->input; ?>
			</fieldset>
		</div>

		<div class="width-40 fltrt">
			<fieldset class="adminform">
				<legend><?php echo JText::_('Details'); ?></legend>
				
					<?php echo $fields['published']->label; ?>
					<?php echo $fields['published']->input; ?>
				
					<?php echo $fields['created_date']->label; ?><br />
					<?php echo $fields['created_date']->input; ?>
					
					<?php echo $fields['updated_date']->label; ?><br />
					<?php echo $fields['updated_date']->input; ?>
			</fieldset>
		</div>
		<div class="clr"></div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>