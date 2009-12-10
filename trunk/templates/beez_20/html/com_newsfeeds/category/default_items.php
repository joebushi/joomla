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

		<div class="limit-box">
			<?php echo JText::_('Display Num') .'&nbsp;'; ?>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>

		<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
</form>

	<table class="newsfeeds">



		<thead>
			<tr>

				<th class="num">
					<?php echo JText::_('Num'); ?>
				</th>

				<th class="title">
					<?php echo JHtml::_('grid.sort',  'News Feed', 'title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>

<th class="count_articles"><?php echo JText::_('Num Articles'); ?></th>

			</tr>
		</thead>


		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>



				<tr class="<?php echo $i % 2 ? 'odd' : 'even';?>">

<td class="num">
                              <?php echo $this->pagination->getRowOffset($i); ?>
                                </td>





					<td class="item-title">
						<a href="<?php echo $item->link; ?>">
							<?php echo $item->name; ?></a>
					</td>

						<td class="count_articles">

							<? echo $item->numarticles; ?>
						</td>




				</tr>
			<?php endforeach; ?>
		</tbody>
		</table>

		           <? if($this->pagination->get('pages.total')>1): ?>
               <div class="pagination">

                                        <p><?php echo $this->pagination->getPagesCounter(); ?></p>


                                <?php echo $this->pagination->getPagesLinks(); ?>

                </div>
                  <?php endif; ?>


