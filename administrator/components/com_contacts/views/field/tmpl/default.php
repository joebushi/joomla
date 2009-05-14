<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>

<?php
	// Set toolbar items for the page
	$edit		= JRequest::getVar('edit',true);
	$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
	JToolBarHelper::title(   JText::_( 'Field' ).': <small><small>[ ' . $text.' ]</small></small>' );
	JToolBarHelper::save();
	JToolBarHelper::apply();
	if (!$edit)  {
		JToolBarHelper::cancel();
	} else {
		// for existing items the button is renamed `close`
		JToolBarHelper::cancel( 'cancel', 'Close' );
	}
?>

<script language="javascript" type="text/javascript">	
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		var id = <?php if($this->field->id)echo $this->field->id;else echo 0;?>;
		if (form.title.value == ""){
			alert( "<?php echo JText::_( 'Field item must have a title', true ); ?>" );
		}else if (id == 1 && form.title.value != "Email") {
			alert( "<?php echo JText::_( 'You are not authorized to change this field\'s title. Title: Email', true ); ?>" );
		}else if (id == 1 && form.type.value != "text"){
			alert( "<?php echo JText::_( 'You are not authorized to change this field\'s type. Type: Text Field', true ); ?>" );
		}else {
			submitform( pressbutton );
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col width-60">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Title' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="32" maxlength="250" value="<?php echo $this->field->title;?>" />
			</td>
		</tr>		
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="position">
					<?php echo JText::_( 'Position' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['pos']; ?>
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
		<tr>
			<td valign="top" align="right" class="key">
				<label for="access">
					<?php echo JText::_( 'Access Level' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['access']; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
			<label for="type">
				<?php echo JText::_( 'Type' ); ?>:
			</label>
			</td>
			<td>
				<?php echo $this->lists['type']; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="description">
					<?php echo JText::_( 'Description' ); ?>:
				</label>
			</td>
			<td>
				<textarea class="inputbox" name="description" rows="5" cols="50" id="description"><?php echo $this->field->description; ?></textarea>
			</td>
		</tr>
		<?php
			if ($this->field->id) {
				?>
				<tr>
					<td class="key">
						<label>
							<?php echo JText::_( 'ID' ); ?>:
						</label>
					</td>
					<td>
						<strong><?php echo $this->field->id;?></strong>
					</td>
				</tr>
				<?php
			}
		?>		
	</table>
	</fieldset>
</div>
<div class="col width-40">
	<fieldset class="adminform">
		<legend>
			<?php echo JText::_( 'Parameters' ); ?>
		</legend>
		<?php 
			jimport('joomla.html.pane');
			$pane =& JPane::getInstance('sliders');
			
			echo $pane->startPane("menu-pane");
			echo $pane->startPanel(JText :: _('Field Parameters'), "param-page");
			echo $this->params->render();
			echo $pane->endPanel();
			echo $pane->endPane();
		?>
	</fieldset>
</div>

<div class="clr"></div>

	<input type="hidden" name="controller" value="field" />
	<input type="hidden" name="option" value="com_contacts" />
	<input type="hidden" name="cid[]" value="<?php echo $this->field->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>