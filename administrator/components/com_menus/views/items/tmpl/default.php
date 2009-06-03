<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');

$user	= &JFactory::getUser();
$userId	= $user->get('id');
$n = count($this->items);
?>
<form action="<?php echo JRoute::_('index.php?option=com_menus&view=items');?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left">
			<label for="search">
				<?php echo JText::_('JSearch_Filter_Label'); ?>
			</label>
			<input type="text" name="filter_search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" size="60" title="<?php echo JText::_('Menus_Items_search_filter'); ?>" />

			<button type="submit">
				<?php echo JText::_('JSearch_Filter_Submit'); ?></button>
			<button type="button" onclick="$('search').value='';this.form.submit();">
				<?php echo JText::_('JSearch_Filter_Clear'); ?></button>
		</div>

		<div class="right">
			<?php echo JHtml::_('access.assetgroups', 'filter_access', $this->state->get('filter.access'), 'class="inputbox" onchange="this.form.submit();"', array('title' => ''));?>
			<?php echo JHtml::_('jgrid.filterPublished', 'filter_published', $this->state->get('filter.published'));?>
			<?php echo JHtml::_('menu.type', 'menutype', $this->state->get('filter.menutype'), 'class="inputbox" onchange="this.form.submit();"');?>

			<?php echo JHtml::_('select.integerlist', 0, 4, 1, 'filter_level', 'class="inputbox" onchange="this.form.submit();"', $this->state->get('filter.level'));?>

		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JCommon_Heading_Title', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JCommon_Heading_Published', 'a.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  'JCommon_Heading_Ordering', 'a.ordering', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					<?php echo JHtml::_('grid.order',  $this->items); ?>
				</th>
				<th width="10%"  class="title">
					<?php echo JHtml::_('grid.sort',  'JCommon_Heading_Access', 'category', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  'JCommon_Heading_ID', 'a.left_id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$item->max_ordering = 0; //??
			$ordering	= ($this->state->get('list.ordering') == 'a.ordering');
			$checkedOut	= JTable::isCheckedOut($userId, $item->checked_out);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td style="text-align:center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td style="padding-left:<?php echo intval($item->level*15)+4; ?>px">
					<?php if ($item->home == 1) : ?>
						<img src="templates/khepri/images/menu/icon-16-default.png" alt="<?php echo JText::_('Default'); ?>" title="<?php echo JText::_('Default'); ?>" />
					<?php endif; ?>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_menus&task=item.edit&cid[]='.$item->id);?>" title="<?php echo $this->escape($item->path);?>">
						<?php echo $item->title; ?></a>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.');?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, true, 'items.orderup', 'JGrid_Move_Up', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'items.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $item->access_level; ?>
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php $i++; endforeach; ?>
		</tbody>
	</table>

	<?php echo $this->loadTemplate('batch'); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
