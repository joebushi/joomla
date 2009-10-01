<?php // no direct access
defined('_JEXEC') or die; ?>
<script language="javascript" type="text/javascript">
	function tableOrdering(order, dir, task) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit(task);
}
</script>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm">
	<table class="jlist">
		<thead>
				<tr>
					<td align="right" colspan="4">
						<?php echo JText::_('Display Num'); ?>
						<?php echo $this->pagination->getLimitBox(); ?>
					</td>
				</tr>
				<?php if ($this->params->def('show_headings', 1)) : ?>
				<tr>
					<th width="10">
						<?php echo JText::_('Num'); ?>
					</th>
					<th width="90%">
						<?php echo JHtml::_('grid.sort',  'News Feed', 'title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					</th>
							</tr>
				<?php endif; ?>
		</thead>
					<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="4" class="sectiontablefooter<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php echo $this->pagination->getPagesLinks(); ?>
					</td>
				</tr>
		</tfoot>	
		<tbody>
			
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="<?php echo $i % 2 ? 'odd' : 'even';?>">
	
						
							<?php if ($this->params->get('show_name')) : ?>
								<td height="20" width="90%" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>">
									<?php echo JText::_('Feed Name'); ?>
								</td>
							<?php endif; ?>
							<?php  if ($this->params->get('show_articles')) : ?>
								<td height="20" width="10%" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" align="center" nowrap="nowrap">
									<?php echo JText::_('Num Articles'); ?>
								</td>
						<?php  endif; ?>
				</tr>
				<tr>
					<td height="20" width="90%">
						<a href="<?php echo $item->link; ?>" class="category<?php echo $this->params->get('pageclass_sfx'); ?>">
							<?php echo $item->name; ?></a>
					</td>
					<?php  if ($this->params->get('show_articles')) : ?>
					<td height="20" width="10%" align="center">
						<?php echo $item->numarticles; ?>
					</td>
					<?php  endif; ?>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td align="center" colspan="4" class="sectiontablefooter<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php 	echo $this->pagination->getPagesLinks(); 		?>
				</td>
		
		
			</tr>
		</tbody>	
</table>
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
</form>