<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'saveconfig') {
		if (confirm ("<?php echo JText::_( 'Are you sure?' ); ?>")) {
			submitform( pressbutton );
		}
	} else {
		document.location.href = 'index.php?option=<?php echo $option;?>';
	}
}
</script>
<form action="index.php" method="post" name="adminForm">

<div id="editcell">
	<table class="adminform">
	<tr>
		<td width="20%">
			<?php echo JText::_( 'Lock Inbox' ); ?>:
		</td>
		<td>
			<?php echo JHTML::_('select.booleanlist',  "vars[lock]", '', $this->vars['lock'], 'yes', 'no', 'varslock' ); ?>
		</td>
	</tr>
	<tr>
		<td width="20%">
			<?php echo JText::_( 'Mail me on new Message' ); ?>:
		</td>
		<td>
			<?php echo JHTML::_('select.booleanlist',  "vars[mail_on_new]", '', $this->vars['mail_on_new'], 'yes', 'no', 'varsmail_on_new' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo JText::_( 'Auto Purge Messages' ); ?>:
		</td>
		<td>
			<input type="text" name="vars[auto_purge]" size="5" value="<?php echo $this->vars['auto_purge']; ?>" class="inputbox" />
			<?php echo JText::_( 'days old' ); ?>
		</td>
	</tr>
	</table>
</div>

<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
