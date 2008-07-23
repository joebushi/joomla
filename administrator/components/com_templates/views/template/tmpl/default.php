<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	JHTML::_('behavior.tooltip');
?>
<div class="col width-50">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td valign="top" class="key">
				<?php echo JText::_( 'Name' ); ?>:
			</td>
			<td>
				<strong>
					<?php echo JText::_($this->row->name); ?>
				</strong>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key">
				<?php echo JText::_( 'Description' ); ?>:
			</td>
			<td>
				<?php echo JText::_($this->row->description); ?>
			</td>
		</tr>
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Menu Assignment' ); ?></legend>
		<script type="text/javascript">
			function disableselections() {
				var e = document.getElementById('selections');
					e.disabled = true;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = true;
					e.options[i].selected = false;
				}
			}
		</script>
		<table class="admintable" cellspacing="1">
			<?php if ($this->row->pages != 'all' && $this->client->id != 1) : ?>
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Menu Selection' ); ?>:
				</td>
				<td>
					<?php echo $this->lists['selections']; ?>
					<script type="text/javascript">disableselections();</script>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</fieldset>
</div>

<div class="col width-50">
	<fieldset class="adminform">
<?php if($this->client->id == 1) { ?>
		<legend><?php echo JText::_( 'Parameters' ); ?></legend>
		<table class="admintable">
		<tr>
			<td>
			<?php
			if (!is_null($this->params)) {
				echo $this->params->render();
			} else {
				echo '<i>' . JText :: _('No Parameters') . '</i>';
			}
			?>
			</td>
		</tr>
		</table>
<?php } else { ?>
		<legend><?php echo JText::_( 'Assignments' ); ?></legend>
		<?php echo implode($this->assignments, ''); ?>
<?php } ?>
	</fieldset>
</div>
<div class="clr"></div>
