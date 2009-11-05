<?php defined('_JEXEC') or die; ?>

<fieldset class="adminform">
	<legend><?php echo JText::_('Custom Output'); ?></legend>

	<?php
	// parameters : areaname, content, width, height, cols, rows
	echo $editor->display('content', $this->row->content, '100%', '400', '60', '20', array('pagebreak', 'readmore')) ;
	?>
</fieldset>