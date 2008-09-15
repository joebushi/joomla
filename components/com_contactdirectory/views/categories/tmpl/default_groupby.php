<?php
/** $Id: default_form.php 10094 2008-03-02 04:35:10Z instance $ */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php foreach($this->categories as $this->category): ?>
	<?php if(isset($this->data[$this->category->title]) && count($this->data[$this->category->title]) > 0): ?>
		<tr>
			<td colspan="4"class="directorycategory<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
				<?php if($this->params->get('linkcat')): ?>	
					<a href="<?php echo $this->category->link; ?>"><?php echo $this->category->title; ?></a>
				<?php else: echo $this->category->title; ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<?php if(count($this->data[$this->category->title]) > 0): ?>
					<?php  foreach($this->data[$this->category->title] as $this->contact):?>
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
	<?php endif; ?>
<?php endforeach; ?>