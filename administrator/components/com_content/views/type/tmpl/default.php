<?php /* $Id:default.php 455 2008-08-07 00:12:40Z pentacle $ */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$editor =& JFactory::getEditor(); ?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (form.name.value == "") {
		alert( "<?php echo JText::_( 'name missing', true ); ?>" );
	} else if (form.table_name.value == ""){
		alert( "<?php echo JText::_( 'table name missing', true ); ?>" );
	} else if (!form.table_name.value.match(/^[a-z0-9]{3,24}$/)){
		alert( "<?php echo JText::_( 'Can only have a-z0-9{3,24}', true ); ?>" );
	} else {
		<?php echo $editor->save('description'); ?>
		submitform( pressbutton );
	}
}
</script>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col width-70">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Details' ); ?></legend>

	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="name"><?php echo JText::_( 'Name' ); ?></label>
			</td>
			<td>
				<input class="text_area required" type="text" name="name" id="name" size="64"
				maxlength="250" value="<?php echo $this->type->name; ?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias"><?php echo JText::_( 'Table Name' ); ?></label>
			</td>
			<td>
				<?php $disabled = ($this->type->id > 0 ? 'disabled="disabled"' : ''); ?>
				<?php echo $this->tablePrefix; ?><input class="text_area required" type="text" <?php echo $disabled; ?> name="table_name" id="table_name" size="64"
				maxlength="250" value="<?php echo $this->type->table_name; ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key">
				<label for="ordering">
					<?php echo JText::_( 'Ordering' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['ordering']; ?>
			</td>
		</tr>
	</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Description' ); ?></legend>

		<table class="admintable">
			<tr>
				<td valign="top" colspan="3">
					<?php
					// parameters : areaname, content, width, height, cols, rows, show xtd buttons
					echo $editor->display( 'description', $this->type->description, '550', '300', '60', '20', array('pagebreak', 'readmore') ) ;
					?>
				</td>
			</tr>
			</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_content" />
<input type="hidden" name="controller" value="types" />
<input type="hidden" name="id" value="<?php echo $this->type->id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token'); ?>
</form>