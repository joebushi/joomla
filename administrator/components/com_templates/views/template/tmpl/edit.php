<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

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
		<table class="admintable" cellspacing="1">
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Menus' ); ?>:
				</td>
				<td>
					<?php if ($this->client->id == 1) {
							echo JText::_('Cannot assign administrator template');
						  } elseif ($this->row->pages == 'all') {
							echo JText::_('Cannot assign default template');
							echo '<input type="hidden" name="default" value="1" />';
						  } elseif ($this->row->pages == 'none') { ?>
					<label for="menus-none"><input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" checked="checked" /><?php echo JText::_( 'None' ); ?></label>
					<label for="menus-select"><input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" /><?php echo JText::_( 'Select From List' ); ?></label>
					<?php } else { ?>
					<label for="menus-none"><input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" /><?php echo JText::_( 'None' ); ?></label>
					<label for="menus-select"><input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" checked="checked" /><?php echo JText::_( 'Select From List' ); ?></label>
					<?php } ?>
				</td>
			</tr>
			<?php if ($this->row->pages != 'all' && $this->client->id != 1) : ?>
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Menu Selection' ); ?>:
				</td>
				<td>
					<?php echo $this->lists['selections']; ?>
					<?php if ($this->row->pages == 'none') { ?>
					<script type="text/javascript">disableselections();</script>
					<?php } ?>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</fieldset>
</div>

<div class="col width-50">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Parameters' ); ?></legend>
			<?php
			jimport('joomla.html.pane');
			$pane	=& JPane::getInstance('sliders');
			echo $pane->startPane("menu-pane");
			$groups = $this->params->getGroups();
			if($groups && count($groups)) {
				foreach($groups as $groupname => $group) {
					if($groupname == '_default') {
						$title = 'Template';
					} else {
						$title = ucfirst($groupname);
					}
					if($this->params->getNumParams($groupname)) {
						echo $pane->startPanel(JText :: _('Parameters - '.$title), $groupname.'-page');
						echo $this->params->render('params', $groupname);
						echo $pane->endPanel();
					}
				}
			} else {
				echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
			}
			echo $pane->endPane();
			?>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="id" value="<?php echo $this->row->directory; ?>" />
<input type="hidden" name="option" value="<?php echo $this->option;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="client" value="<?php echo $this->client->id;?>" />
<input type="hidden" name="old_menu_id" value="<?php echo JRequest::getVar('menuid'); ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
