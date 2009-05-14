<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	JHTML::_('behavior.tooltip');

	// Set toolbar items for the page
	$edit		= JRequest::getVar('edit',true);
	$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
	JToolBarHelper::title(   JText::_( 'Contact' ).': <small><small>[ ' . $text.' ]</small></small>' );
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

		// do contact validation
		if (form.name.value == ""){
			alert( "<?php echo JText::_( 'Contact item must have a name', true ); ?>" );
		}
		if(form.categories.value ==""){
			alert( "<?php echo JText::_( 'At least one category must be selected', true ); ?>" );
		}
		<?php 
			foreach ($this->fields as $this->field) {
				if($this->field->params->get('required')){;
					$name = $this->field->name;
					$title = $this->field->title;
					echo "else if (form.$name.value == \"\") {"
							."alert( \"The $title is required.\" ); "
							."}";
				}
			}
		?>
		
		else {
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
					<label for="name"><?php echo JText::_( 'Name' ); ?>:</label>
				</td>
				<td>
					<input class="text_area" type="text" name="name" id="name" size="32" maxlength="250" value="<?php echo $this->contact->name;?>" />
				</td>
			</tr>	
			<tr>
				<td width="100" align="right" class="key">
					<label for="alias"><?php echo JText::_( 'Alias' ); ?>:</label>
				</td>
				<td>
					<input class="text_area" type="text" name="alias" id="alias" size="32" maxlength="250" value="<?php echo $this->contact->alias;?>" />
				</td>
			</tr>		
			<tr>
				<td valign="top" align="right" class="key">
					<label for="published"><?php echo JText::_( 'Published' ); ?>:</label>
				</td>
				<td>
					<?php echo $this->lists['published']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="user_id">
						<?php echo JText::_( 'Linked to User' ); ?>:
					</label>
				</td>
				<td >
					<?php echo $this->lists['user_id'];?>
				</td>
			</tr>		
			<tr>
				<td valign="top" align="right" class="key">
					<label for="access"><?php echo JText::_( 'Access Level' ); ?>:</label>
				</td>
				<td>
					<?php echo $this->lists['access']; ?>
				</td>
			</tr>
			<?php
				if ($this->contact->id) {
					?>
					<tr>
						<td class="key">
							<label>
								<?php echo JText::_( 'ID' ); ?>:
							</label>
						</td>
						<td>
							<strong><?php echo $this->contact->id;?></strong>
						</td>
					</tr>
					<?php
				}
			?>		
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Categories' ); ?></legend>

		<table class="admintable">
			<tr>
				<td valign="top" align="right" class="key">
					<label for="categories"><?php echo JText::_( 'Categories' ); ?>:</label>
				</td>
				<td>
					<?php echo $this->lists['category']; ?>
				</td>
			</tr>
			<?php for($i=0; $i<count($this->categories); $i++) { ?>
			<tr>
				<td valign="top" align="right" class="key">
					<label for="ordering"><?php echo JText::_( 'Ordering '.$this->categories[$i]->title ); ?>:</label>
				</td>
				<td>
					<?php echo $this->lists['ordering'.$i]; ?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Information' ); ?></legend>
		
		<table class="admintable">
			<?php for($i=0; $i<count($this->fields); $i++)
				  	{ 
					  	$field = &$this->fields[$i];
					  	if($field->params->get('required')){
							$star = '* ';
						}else{
							$star ='';
						}
			?>
			<tr>
				<td valign="top" align="right" class="key">
					<label for="<?php echo $field->title; ?>">
						<?php echo JText::_( $star.$field->title ); ?>:
					</label>
				</td>
				<td>
					<?php
						if($field->type == 'text' || $field->type == 'email' || $field->type == 'url'){
							echo  '<input class="text_area" type="text" name="fields[]" 
										id="'.$field->name.'" size="32" maxlength="250" 
										value="'.$field->data.'" />';
						}else if($field->type == 'textarea'){
							echo '<textarea class="inputbox" name="fields[]" 
									rows="5" cols="50" id="'.$field->name.'">'.
									$field->data.'</textarea>';
						}else if($field->type == 'editor'){
							$editor =& JFactory::getEditor();
							echo $editor->display('fields[]', $field->data, '100%', '100%', '50', '5');
						}else if($field->type == 'image'){
							echo JHTML::_('list.images',  'fields[]', $field->data );
						}
					
					?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</fieldset>
</div>
<div class="col width-40">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Parameters' ); ?></legend>
		<?php
			jimport('joomla.html.pane');
			$pane =& JPane::getInstance('sliders');
			
			echo $pane->startPane("menu-pane");
			echo $pane->startPanel(JText :: _('Contact Parameters'), "param-page");
			echo $this->params->render();
			$i = 0;
		?>
		<table class="paramlist admintable" cellspacing="1" width="100%">
			<tbody>
			<?php foreach($this->fields as $this->field){ ?>
				<tr>
					<td class="paramlist_key" width="40%">
						<span class="editlinktip">
							<label id="paramsshow_contact-lbl" for="paramsshow_contact" 
									  class="hasTip" title="<?php echo JText::_($this->field->title).'::'.JText::_('Show/Hide the '.strtolower(JText::_($this->field->title)).' information in the contact form page');?>">
									  <?php echo JText::_($this->field->title); ?>
							</label>
						</span>
					</td>
					<td class="paramlist_value">
						<?php echo $this->lists['showContact'.$i]; ?>
					</td>
				</tr>
				<tr>
			<?php $i++; } ?>
			</tbody>
		</table>
		<?php
			echo $pane->endPanel();
			echo $pane->startPanel(JText :: _('Directory Parameters'), "param-page");
			echo $this->params->render('params', 'directory');
			$i = 0;
		?>
		<table class="paramlist admintable" cellspacing="1" width="100%">
			<tbody>
			<?php foreach($this->fields as $this->field){ ?>
				<tr>
					<td class="paramlist_key" width="40%">
						<span class="editlinktip">
							<label id="paramsshow_directory-lbl" for="paramsshow_directory" 
									  class="hasTip" title="<?php echo JText::_($this->field->title).'::'.JText::_('Show/Hide the '.strtolower(JText::_($this->field->title)).' information in the directory');?>">
									  <?php echo JText::_($this->field->title); ?>
							</label>
						</span>
					</td>
					<td class="paramlist_value">
						<?php echo $this->lists['showDirectory'.$i]; ?>
					</td>
				</tr>
				<tr>
			<?php $i++; } ?>
			</tbody>
		</table>
		<?php
			echo $pane->endPanel();
			echo $pane->startPanel(JText :: _('E-mail Parameters'), "param-page");
			echo $this->params->render('params', 'email');
			echo $pane->endPanel();
			echo $pane->endPane();
		?>
	</fieldset>
</div>

<div class="clr"></div>

	<input type="hidden" name="controller" value="contact" />
	<input type="hidden" name="option" value="com_contacts" />
	<input type="hidden" name="cid[]" value="<?php echo $this->contact->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>