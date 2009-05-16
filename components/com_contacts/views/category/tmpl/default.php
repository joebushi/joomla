<?php /* $Id$ */ defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
function alphabetFilter(val){
	document.getElementById('alpha').value=val;
	document.contactForm.submit();
}
</script>

<?php if ( $this->params->get( 'show_page_title' ) ) : ?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php 	if ($this->category->title && $this->params->get('show_title') ) : echo $this->escape($this->params->get('page_title')).' - '.$this->escape($this->category->title);
				else : echo $this->escape($this->params->get('page_title'));
				endif; ?>
</div>
<?php endif; ?>

<div class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php if ($this->category->image || $this->category->description) : ?>
		<div class="contentdescription<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
			<?php if ($this->params->get('show_description_image')) : ?>
				<img src="<?php echo $this->baseurl .'/'. $this->cparams->get('image_path') . '/'. $this->category->image; ?>" align="<?php echo $this->category->image_position; ?>" hspace="6" alt="<?php echo JText::_( 'Contacts' ); ?>" />
			<?php endif;
					   if ($this->params->get('show_description')) :  
					   		echo $this->category->description; 
					   endif; ?>
		</div>
	<?php endif; ?>
	
	<form action="<?php echo $this->action; ?>" method="post" name="contactForm">
	<table  width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
		<?php if($this->params->get('alphabet') || $this->params->get('search')): ?>
		<thead>
			<tr>
				<td colspan="3" height="50">
					<?php if($this->params->get('alphabet')): ?>
						<a href="javascript:alphabetFilter('a')">A</a>
						<a href="javascript:alphabetFilter('b')">B</a>
						<a href="javascript:alphabetFilter('c')">C</a>
						<a href="javascript:alphabetFilter('d')">D</a>
						<a href="javascript:alphabetFilter('e')">E</a>
						<a href="javascript:alphabetFilter('f')">F</a>
						<a href="javascript:alphabetFilter('g')">G</a>
						<a href="javascript:alphabetFilter('h')">H</a>
						<a href="javascript:alphabetFilter('i')">I</a>
						<a href="javascript:alphabetFilter('j')">J</a>
						<a href="javascript:alphabetFilter('k')">K</a>
						<a href="javascript:alphabetFilter('l')">L</a>
						<a href="javascript:alphabetFilter('m')">M</a>
						<a href="javascript:alphabetFilter('n')">N</a>
						<a href="javascript:alphabetFilter('o')">O</a>
						<a href="javascript:alphabetFilter('p')">P</a>
						<a href="javascript:alphabetFilter('q')">Q</a>
						<a href="javascript:alphabetFilter('r')">R</a>
						<a href="javascript:alphabetFilter('s')">S</a>
						<a href="javascript:alphabetFilter('t')">T</a>
						<a href="javascript:alphabetFilter('u')">U</a>
						<a href="javascript:alphabetFilter('v')">V</a>
						<a href="javascript:alphabetFilter('w')">W</a>
						<a href="javascript:alphabetFilter('x')">X</a>
						<a href="javascript:alphabetFilter('y')">Y</a>
						<a href="javascript:alphabetFilter('z')">Z</a> |
						<a href="javascript:alphabetFilter('')">Reset</a>
						<input type="text" name="alphabet" id="alpha" value="" style="display:none;"/>
					<?php endif; ?>
				</td>
				<td align="right" nowrap="nowrap" colspan="1">
					<?php if($this->params->get('search')): ?>
						<?php echo JText::_( 'Filter' ); ?>:
				 		<input type="text" name="search" id="searchword" size="15" value="<?php echo $this->lists['search'];?>" class="inputbox" onchange="document.contactForm.submit();"/>
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('searchword').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					<?php endif; ?>
				</td>
			</tr>
		</thead>
		<?php endif; ?>
		<tfoot>
			<tr>
				<td nowrap="nowrap" height="50">
					<?php if ($this->params->get('show_limit')) :
						echo JText::_('Display #') .'&nbsp;';
						echo $this->pagination->getLimitBox();
					endif; ?>
				</td>
				<td align="center" colspan="2" class="sectiontablefooter<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</td>
				<td align="right" class="sectiontablefooter<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td colspan="4">
					<?php if(count($this->contacts) > 0): ?>
						<?php foreach($this->contacts as $this->contact): ?>
							<div class="directorybox<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
								<?php if($this->contact->params->get('show_name')  && $this->contact->name): ?>
									<div class="directorytitle<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
										<div class="contentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
											<?php if($this->params->get('link')): ?>	
												<a href="<?php echo $this->contact->link; ?>">
													<?php echo $this->contact->name; ?>
												</a>
											<?php else: echo $this->contact->name;?>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
								<div class="directoryinfo<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
									<?php foreach($this->contact->fields as $this->contact->field): ?>
										<?php if($this->contact->field->data && $this->contact->field->show_field && $this->contact->field->access <= $this->user->get('aid', 0)): ?>
											<div class="directoryfield<?php echo $this->contact->field->params->get( 'css_tag' ); ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>">
												<span class="directoryfieldtitle<?php echo $this->contact->field->params->get( 'css_tag' ); ?><?php echo $this->params->get( 'pageclass_sfx' ); ?>">
													<?php echo $this->contact->field->params->get( 'marker_title' ); ?>
												</span>
												<span><?php echo $this->contact->field->data; ?></span>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_contacts" />
	<input type="hidden" name="catid" value="<?php echo $this->category->id;?>" />
	</form>
</div>