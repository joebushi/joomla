<fieldset class="adminform">
	<legend><?php echo JText::_('Locale Settings'); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<?php
			foreach ($this->form->getFields('locale') as $field):
			?>
			<tr>
				<td width="185" class="key">
					<?php echo $field->label; ?>
				</td>
				<td>
					<?php echo $field->input; ?>
				</td>
			</tr>
			<?php
			endforeach;
			?>
		</tbody>
	</table>
</fieldset>
