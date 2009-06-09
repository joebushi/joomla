<?php defined('_JEXEC') or die; ?>
<table class="contentpaneopen<?php echo $this->params->get('pageclass_sfx'); ?>">
	<tr>
		<td>
		<?php
		foreach($this->results as $result) : ?>
			<fieldset class="<?php echo $result->class_suffix; ?>">
				<h3><a href="<?php echo $result->link; ?>"><?php echo $this->escape($result->title); ?></a></h3>
				<span><?php echo $result->subtitle; ?></span>
				<?php echo $result->body; ?>
				<span><?php echo $result->date; ?></span>
			</fieldset>
		<?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div align="center">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</td>
	</tr>
</table>