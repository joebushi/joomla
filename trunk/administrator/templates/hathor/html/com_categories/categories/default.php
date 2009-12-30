<?php
/**
 * @version	 $Id: default.php 13471 2009-11-12 00:38:49Z eddieajau $
 * @package		Joomla.Administrator
 * @subpackage	com_categories
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
$extension	= $this->escape($this->state->get('filter.extension'));
$n = count($this->items);
?>
<div class="categories">
<form action="<?php echo JRoute::_('index.php?option=com_categories&view=categories');?>" method="post" name="adminForm">

	<fieldset id="filter-bar">
		<legend class="element-invisible"><?php echo JText::_('JSearch_Filter_Label'); ?></legend>
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSearch_Filter_Label'); ?>:</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('Categories_Items_search_filter'); ?>" />
			
			<button type="submit" class="btn"><?php echo JText::_('JSearch_Filter_Submit'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSearch_Filter_Clear'); ?></button>
		</div>
		
		<div class="filter-select">
			<label class="selectlabel" for="filter_access"><?php echo JText::_('JOption_Select_Access'); ?></label>
			<select name="filter_access" class="inputbox" id="filter_access">
				<option value=""><?php echo JText::_('JOption_Select_Access');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<label class="selectlabel" for="filter_published"><?php echo JText::_('JOption_Select_Published'); ?></label> 
			<select name="filter_published" class="inputbox" id="filter_published">
				<option value=""><?php echo JText::_('JOption_Select_Published');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			
			<button type="button" id="filter-go" onclick="this.form.submit();">
				<?php echo JText::_('Go'); ?></button>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="checkmark-col">
					<input type="checkbox" name="toggle" id="toggle" value="" title="<?php echo JText::_('Checkmark_All'); ?> " onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Title', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap state-col">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Published', 'a.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="title ordering-col">
					<?php echo JText::_('JGrid_Heading_Ordering'); ?>
				</th>
				<th class="title access-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Access', 'access_level', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap id-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_ID', 'a.lft', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
			foreach ($this->items as $i => $item) :
				$ordering = ($this->state->get('list.ordering') == 'a.lft');
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<th class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</th>
					<td class="indent-<?php echo intval(($item->level-1)*15)+4; ?>">
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $item->editor, $item->checked_out_time); ?>
						<?php endif; ?>
						<a href="<?php echo JRoute::_('index.php?option=com_categories&task=category.edit&cid[]='.$item->id.'&extension='.$extension);?>">
							<?php echo $this->escape($item->title); ?></a>
						<p class="smallsub" title="<?php echo $this->escape($item->path);?>">
							(<span><?php echo JText::_('JFIELD_ALIAS_LABEL'); ?>:</span> <?php echo $this->escape($item->alias);?>)</p>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.');?>
					</td>
					<td class="order">
						<span><?php echo $this->pagination->orderUpIcon($i, $item->order_up, 'categories.orderup', 'JGrid_Move_Up', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, $item->order_dn, 'categories.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
					</td>
					<td class="center">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="center">
						<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
							<?php echo (int) $item->id; ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="extension" value="<?php echo $extension;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div>