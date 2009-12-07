<?php
/**
 * @version		$Id: default.php 12237 2009-06-21 12:13:14Z eddieajau $
 * @package		Hathor Accessible Administrator Template
 * @since		1.6
 * @version  	1.04
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_content&view=featured');?>" method="post" name="adminForm">
	<fieldset class="filter clearfix">
	<legend class="element-invisible"><?php echo JText::_('Filters'); ?></legend>
		<div class="left">
			<label for="filter_search">
				<?php echo JText::_('JSearch_Filter_Label'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" size="60" title="<?php echo JText::_('Content_Filter_Search_Desc'); ?>" />

			<button type="submit">
				<?php echo JText::_('JSearch_Filter_Submit'); ?></button>
			<button type="button" onclick="$('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSearch_Filter_Clear'); ?></button>
		</div>

		<div class="right">
		<label class="selectlabel" for="filter_access">
				<?php echo JText::_('Filter_Access'); ?>
			</label>
			<select name="filter_access" id="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOption_Select_Access');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<label class="selectlabel" for="filter_published">
				<?php echo JText::_('Filter_State'); ?>
			</label> 
			<select name="filter_published" id="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOption_Select_Published');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="checkmark-col">
					<input type="checkbox" name="toggle" value="" title="<?php echo JText::_('Checkmark_All'); ?> onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Title', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap state-col">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Published', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="title category-col">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Category', 'a.catid', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="title created-by-col">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Created_by', 'a.created_by', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap ordering-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Ordering', 'fp.ordering', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					<?php echo JHtml::_('grid.order',  $this->items); ?>
				</th>
				<th class="title access-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Access', 'category', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="title date-col">
					<?php echo JHtml::_('grid.sort',  'Content_Heading_Date', 'a.created', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="hits-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Hits', 'a.hits', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap id-col">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$item->max_ordering = 0; //??
			$ordering	= ($this->state->get('list.ordering') == 'a.ordering');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<th class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</th>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_content&task=article.edit&cid[]='.$item->id);?>">
						<?php echo $this->escape($item->title); ?></a>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'featured.'); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->author_name); ?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, true, 'items.orderup', 'JGrid_Move_Up', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'items.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" title="<?php echo $item->title; ?> order" />
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo JHtml::date($item->created, '%Y.%m.%d'); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->hits; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
