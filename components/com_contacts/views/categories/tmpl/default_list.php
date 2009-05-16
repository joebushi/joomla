<?php /* $Id$ */ defined('_JEXEC') or die('Restricted access'); ?>

<tr>
	<td colspan="4">
		<?php if(count($this->data) > 0): ?>
			<?php foreach($this->data as $this->contact):?>
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