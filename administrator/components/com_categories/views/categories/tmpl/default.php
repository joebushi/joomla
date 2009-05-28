<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
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
<form action="<?php echo JRoute::_('index.php?option=com_categories&view=categories');?>" method="post" name="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<label for="search"><?php echo JText::_('JSearch_Filter'); ?></label>
			<input type="text" name="filter_search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" size="30" title="<?php echo JText::_('Category_Search_in_title'); ?>" />
			<button type="submit"><?php echo JText::_('JSearch_Filter_Submit'); ?></button>
			<button type="button" onclick="$('search').value='';this.form.submit();"><?php echo JText::_('JSearch_Filter_Clear'); ?></button>
		</div>
		<div class="right">
			<ol>
				<li>
					<label for="filter_state">
						<?php echo JText::_('Category_Filter_State'); ?>
					</label>
					<?php echo JHtml::_('categories.filterstate', $this->state->get('filter.state'));?>
				</li>
			</ol>
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
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JCommon_Heading_Hits', 'a.hits', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  'JCommon_Heading_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
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
		foreach ($this->items as $i => $item) :
			$ordering	= ($this->state->get('list.ordering') == 'a.ordering');
			$checkedOut	= JTable::isCheckedOut($userId, $item->checked_out);
			?>
			<tr class="row<?php echo $i++ % 2; ?>">
				<td style="text-align:center">
					<?php echo JHTML::_('grid.id', $item->id, $item->id); ?>
				</td>
				<td style="padding-left:<?php echo intval($item->level*15)+4; ?>px">
					<?php if ($item->checked_out) : ?>
						<?php echo JHTML::_('smartgrid.checkedout', $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_categories&task=category.edit&cid[]='.$item->id);?>">
						<?php echo $item->title; ?></a>
				</td>
				<td align="center">
					<?php echo JHtml::_('categories.state', $item->published, $i);?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, ($item->ordering != 0),'weblinks.orderup', 'JGrid_Move_Up', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->ordering < $item->max_ordering), 'weblinks.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $item->access_level; ?>
				</td>
				<td align="center">
					<?php echo $item->hits; ?>
				</td>
				<td align="center">
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
	<?php echo JHTML::_('form.token'); ?>
</form>
