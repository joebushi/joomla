<?php // no direct access
defined('_JEXEC') or die; ?>
<div style="padding: 10px;">
	<div style="text-align:right">
		<a href="javascript: void window.close()">
			<?php echo JText::_('CLOSE_WINDOW'); ?> <?php echo JHTML::_('image', 'mailto/close-x.png', NULL, array('border' => 0), true); ?></a>
	</div>

	<h2>
		<?php echo JText::_('EMAIL_SENT'); ?>
	</h2>
</div>
