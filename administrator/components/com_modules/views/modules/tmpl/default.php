<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('script', 'multiselect.js');
$user	= &JFactory::getUser();
$userId	= $user->get('id');
?>

<form action="<?php echo JRoute::_('index.php?option=com_modules&view=modules&client_id='.$this->state->get('client.id')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="search"><?php echo JText::_('JSearch_Filter'); ?>:</label>
			<input type="text" name="filter_search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('Weblinks_Search_in_title'); ?>" />
			<button type="submit"><?php echo JText::_('JSearch_Filter_Submit'); ?></button>
			<button type="button" onclick="document.id('search').value='';this.form.submit();"><?php echo JText::_('JSearch_Filter_Clear'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<?php
			echo JHtml::_('filter.assigned', $this->state->get('client'), $this->state->get('filter.assigned'));
			echo JHtml::_('filter.position', $this->state->get('client'), $this->state->get('filter.position'));
			echo JHtml::_('filter.type', $this->state->get('client'), $this->state->get('filter.type'));
			echo JHtml::_('grid.state', $this->state->get('filter.published'));
			?>
		</div>
	</fieldset>
	<div class="clr"> </div>


	<table class="adminlist" cellspacing="1">
	<thead>
	<tr>
		<th width="20">
			<?php echo JText::_('NUM'); ?>
		</th>
		<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items);?>);" />
		</th>
		<th>
			<?php echo JHtml::_('grid.sort', 'Module Name', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
		<th class="nowrap" width="7%">
			<?php echo JHtml::_('grid.sort', 'Published', 'a.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
		<th class="nowrap" width="80" >
			<?php echo JHtml::_('grid.sort', 'Order', 'a.position', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
		<th width="1%">
			<?php echo JHtml::_('grid.order',  $this->items); ?>
		</th>
		<?php if ($this->state->get('client.id') == 0): ?>
			<th class="nowrap" width="7%">
				<?php echo JHtml::_('grid.sort', 'Access', 'access_level', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
			</th>
		<?php endif; ?>
		<th width="7%">
			<?php echo JHtml::_('grid.sort', 'Position', 'a.position', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
		<th class="nowrap" width="5%">
			<?php echo JHtml::_('grid.sort', 'Pages', 'pages', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
		<th class="center" width="10%" >
			<?php echo JHtml::_('grid.sort', 'Type', 'a.module', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
		<th class="nowrap" width="1%">
			<?php echo JHtml::_('grid.sort',   'ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
		</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<td colspan="12">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	<tbody>
		<?php
		$n = count($this->items);
		$ordering = ($this->state->get('list.ordering') == 'a.position');
		foreach ($this->items as $i => $item) :
			$link = JRoute::_('index.php?option=com_modules&task=module.edit&cid[]='. $item->id);
			$checkedOut	= JTable::isCheckedOut($userId, $item->checked_out);
		?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="center">
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>
			<td width="20">
				<?php echo JHtml::_('grid.checkedout', $item, $i); ?>
			</td>
			<td>
				<?php if (JTable::isCheckedOut($userId, $item->checked_out)) : ?>
					<?php echo $item->title; ?>
				<?php else : ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('JCommon_Edit_item');?>::<?php echo $item->title; ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $item->title; ?></a></span>
				<?php endif; ?>
			</td>
			<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->published, $i, 'modules.');?>
			</td>
			<td class="order" colspan="2">
				<span><?php echo $this->pagination->orderUpIcon($i, ($item->position == @$this->items[$i-1]->position), 'modules.orderup', 'JGrid_Move_Up', $ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->position == @$this->items[$i+1]->position), 'modules.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
			</td>
			<?php if ($this->state->get('client.id') == 0): ?>
			<td class="center">
				<?php echo $item->access_level; ?>
			</td>
			<?php endif; ?>
			<td class="center">
				<?php echo $item->position; ?>
			</td>
			<td class="center">
				<?php
				if (is_null($item->pages)) {
					echo JText::_('None');
				} else if ($item->pages != 0) {
					echo JText::_('Varies');
				} else {
					echo JText::_('All');
				}
				?>
			</td>
			<td class="center">
				<?php echo $item->module ? $item->module : JText::_('User'); ?>
			</td>
			<td class="center">
				<?php echo $item->id; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>
