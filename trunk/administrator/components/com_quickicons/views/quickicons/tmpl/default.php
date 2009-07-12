<?php defined('_JEXEC') or die; ?>
<?php
	// Add specific helper files for html generation
	JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('JGrid_Heading_Row_Number'); ?>
				</th>
				<th width="20" style="text-align:center">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JText::_('JGrid_Heading_Title'); ?>
				</th>
				<th>
					<?php echo JText::_('QuickIcons_Section'); ?>
				</th>
				<th>
					<?php echo JText::_('QuickIcons_Access'); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('JGrid_Heading_Published'); ?>
				</th>
				<th width="10%" nowrap="nowrap">
					<?php echo JText::_('JGrid_Heading_Ordering');?>
					<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'quickicons.saveorder'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item):?>
			<tr class="item<?php echo $i % 2;?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td width="20" style="text-align:center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo JText::_($item->name);?>
				</td>
				<td>
					<?php echo JText::_($item->section);?>
				</td>
				<td>
					<?php echo JText::_($item->access);?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'quickicons.'); ?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, ($item->sid == @$this->items[$i-1]->sid), 'quickicons.orderup', 'JGrid_Move_Up'); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->sid == @$this->items[$i+1]->sid), 'quickicons.orderdown', 'JGrid_Move_Down'); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="text_area" style="text-align: center" />
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
