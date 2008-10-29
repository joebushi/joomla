<?php /* $Id$ */
defined('_JEXEC') or die('Restricted access'); ?>
<form action="<?php JRoute::_('index.php?option=com_content&controller=types'); ?>" method="post" name="adminForm">

<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
	</tr>
</table>

<div id="editcell">
<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'Name', 't.name', $this->state->get('filter.orderDir'), $this->state->get('filter.order') ); ?>
			</th>
			<th width="8%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'Order', 'ordering', $this->state->get('filter.orderDir'), $this->state->get('filter.order') ); ?>
				<?php echo JHTML::_('grid.order',  $this->items ); ?>
			</th>
			<th width="15%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'Table Name', 't.table_name', $this->state->get('filter.orderDir'), $this->state->get('filter.order') ); ?>
			</th>
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
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
	<?php
	$k = 0;
	$i = 0;
	$n = count( $this->items );

	foreach ($this->items as $row) {
		$checked = JHTML::_( 'grid.id', $i, $row->id );
		$ordering = ($this->state->get('filter.order') == 'ordering');

		$link = JRoute::_( 'index.php?option=com_content&controller=types&task=edit&cid[]='. $row->id );
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td>
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td>
			<?php echo $checked; ?>
		</td>
		<td>
			<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
		</td>
		<td class="order" nowrap="nowrap">
			<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering); ?></span>
			<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $ordering ); ?></span>
			<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
		</td>
		<td>
			<?php echo $row->tablename; ?>
		</td>
		<td>
			<?php echo $row->id; ?>
		</td>
	</tr>
	<?php
	$k = 1 - $k;
	$i++;
	}
	?>
	</tbody>
</table>
</div>


<input type="hidden" name="option" value="com_content" />
<input type="hidden" name="controller" value="types" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->state->get('filter.order'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('filter.orderDir'); ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>