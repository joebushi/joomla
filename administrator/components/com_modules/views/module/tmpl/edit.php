<?php defined('_JEXEC') or die; ?>

<?php
	JHtml::_('behavior.combobox');

	jimport('joomla.html.pane');
	$pane = &JPane::getInstance('sliders');
	$editor = &JFactory::getEditor();

	//JHtml::_('behavior.tooltip');
?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	if ((pressbutton == 'save' || pressbutton == 'apply') && (document.adminForm.title.value == "")) {
		alert("<?php echo JText::_('Module must have a title', true); ?>");
	} else {
		<?php
		if ($this->row->module == '' || $this->row->module == 'mod_custom') {
			echo $editor->save('content');
		}
		?>
		submitform(pressbutton);
	}
}
var originalOrder 	= '<?php echo $this->row->ordering;?>';
var originalPos 	= '<?php echo $this->row->position;?>';
var orders 			= new Array();	// array in the format [key,value,text]
<?php	$i = 0;
$orders = array();
foreach ($this->orders2 as $k=>$items) {
	foreach ($items as $v) {
		$orders[] = array($k, $v->value, $v->text);
	}
}
foreach ($this->orders2 as $k=>$items) {
	foreach ($items as $v) {
		echo "\n	orders[".$i++."] = new Array(\"$k\",\"$v->value\",\"$v->text\");";
	}
}
?>
var orders2 = <?php echo json_encode($orders); ?>
</script>
<form action="<?php echo JRoute::_('index.php');?>" method="post" name="adminForm">
<div class="width-50 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Details'); ?></legend>
			<?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?>

			<?php //echo $this->form->getLabel('type'); ?>
			<?php //echo $this->form->getInput('type'); ?>

			<?php echo $this->form->getLabel('showtitle'); ?>
			<?php echo $this->form->getInput('showtitle'); ?>
			<?php // TODO n/a for admin? ?>

			<?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?>

			<?php echo $this->form->getLabel('position'); ?>
			<?php echo $this->form->getInput('position'); ?>

			<br />ordering<br />

			<?php echo $this->form->getLabel('access'); ?>
			<?php echo $this->form->getInput('access'); ?>
			<?php // TODO n/a for admin? ?>

			<label id="jform_position-lbl" class="hasTip" for="jform_position" title="<?php echo JText::_('MODULE_POSITION_TIP_TITLE', true); ?>::<?php echo JText::_('MODULE_POSITION_TIP_TEXT', true); ?>">
				<?php echo JText::_('Position'); ?>:
			</label>

			<select id="jform_position" class="inputbox" size="1" name="jform[position]">
			<?php
					foreach ($this->positions as $position) {
						echo '<option value="'.$position.'"'.($this->row->position == $position ? ' selected="selected"' : '').'>'.$position.'</option>';
					}
					?>
					</select>

			<label id="jform_ordering-lbl" class="hasTip" for="jform_ordering"><?php echo JText::_('Order'); ?>:</label>
					<script language="javascript" type="text/javascript">
					<!--
					writeDynaList('class="inputbox" name="ordering" id="ordering" size="1"', orders, originalPos, originalPos, originalOrder);
					//-->
					</script>

			<?php echo JText::_('Description'); ?>:
			<p class="jform_desc"><?php echo JText::_($this->item->description); ?></p>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('Menu Assignment'); ?></legend>
		<script type="text/javascript">
			function allselections() {
				var e = document.getElementById('selections');
					e.disabled = true;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = true;
					e.options[i].selected = true;
				}
			}
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
			function enableselections() {
				var e = document.getElementById('selections');
					e.disabled = false;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = false;
				}
			}
		</script>
	<!-- TO DO: Need to rework UI for this section -->
			<label id="jform_menus-lbl" class="hasTip" for="jform_menus"><?php echo JText::_('Menus'); ?>:</label>
				<?php if ($this->row->client_id != 1) : ?>

			<fieldset id="jform_menus" class="radio">
				<label id="jform_menus-all-lbl" for="menus-all"><?php echo JText::_('All'); ?></label>
				<input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" <?php
						echo ($this->row->pages == 'all') ? 'checked="checked"' : ''; ?> />

				<label id="jform_menus-none-lbl" for="menus-none"><?php echo JText::_('None'); ?></label>
				<input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" <?php
						echo ($this->row->pages == 'none') ? 'checked="checked"' : ''; ?> />

				<label id="jform_menus-select-lbl" for="menus-select"><?php echo JText::_('Select From List'); ?></label>
				<input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" <?php
						echo ($this->row->pages == 'select') ? 'checked="checked"' : ''; ?> />

				<label id="jform_menus-deselect-lbl" for="menus-deselect"><?php echo JText::_('Deselect From List'); ?></label>
				<input id="menus-deselect" type="radio" name="menus" value="deselect" onclick="enableselections();" <?php
						echo ($this->row->pages == 'deselect') ? 'checked="checked"' : ''; ?> />
			</fieldset>
				<?php endif; ?>

			<label id="jform_menuselect-lbl" class="hasTip" for="jform_menuselect"><?php echo JText::_('Menu Selection'); ?>:</label>
					<?php echo $this->lists['selections']; ?>

		<?php if ($this->row->client_id != 1) : ?>
			<?php if ($this->row->pages == 'all') : ?>
			<script type="text/javascript">allselections();</script>
			<?php elseif ($this->row->pages == 'none') : ?>
			<script type="text/javascript">disableselections();</script>
			<?php endif; ?>
		<?php endif; ?>
	</fieldset>
</div>

<div class="width-50 fltrt">
	<?php echo $this->loadTemplate('parameters'); ?>
</div>
<div class="clr"></div>

<?php
if (!$this->item->module || $this->item->module == 'custom' || $this->item->module == 'mod_custom') {
	echo $this->loadTemplate('custom');
}
?>


<input type="hidden" name="original" value="<?php echo $this->row->ordering; ?>" />
<input type="hidden" name="module" value="<?php echo $this->row->module; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="client" value="<?php echo $this->client->id ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>
