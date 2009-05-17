<?php /** $Id$ */ defined('_JEXEC') or die('Restricted Access'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_members&task=member.logout'); ?>" method="post" name="login" id="form-login">
<?php if ($params->get('greeting', 1)) : ?>
	<div><?php echo JText::sprintf( 'HINAME', JFactory::getUser()->get('name') ); ?></div>
<?php endif; ?>
	<div align="center">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'BUTTON_LOGOUT'); ?>" />
	</div>

	<input type="hidden" name="option" value="com_members" />
	<input type="hidden" name="task" value="member.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>