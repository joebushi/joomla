<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');

// Load the Plugin helper file.
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left"></div>
		<div class="right">
			<ol>
				<li>
					<label for="filter_state">
						<?php echo JText::_('Plugins_Filter_State'); ?>
					</label>
					<?php echo JHtml::_('plugin.filterstate', $this->state->get('filter.state'));?>
				</li>
			</ol>
		</div>
	</fieldset>
	
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<?php echo JText::_('Num'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'Plugin Name', 'p.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap" width="5%">
					<?php echo JHtml::_('grid.sort', 'Published', 'p.published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="8%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Order', 'p.ordering', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					<?php echo JHtml::_('grid.order', $this->items); ?>
				</th>
				<th nowrap="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'Access', 'groupname', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap"  width="10%" class="title">
					<?php echo JHtml::_('grid.sort', 'Type', 'p.folder', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap"  width="10%" class="title">
					<?php echo JHtml::_('grid.sort', 'File', 'p.element', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th nowrap="nowrap"  width="1%" class="title">
					<?php echo JHtml::_('grid.sort', 'ID', 'p.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		
		<tbody>
			<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
				$ordering = ($this->state->get('list.ordering') == 'p.ordering');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo JHtml::_('grid.checkedout', $item, $i); ?>
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_('JCommon_Edit_item');?>::<?php echo $item->name; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_plugins&taskplugin.edit&id='.(int) $item->id); ?>">
							<?php echo $item->name; ?>
						</a>
					</span>
				</td>
				<td align="center">
					<?php echo JHtml::_('plugin.state', $item->published, $i);?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'plugins.orderup', 'JGrid_Move_Up', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'plugins.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $item->access; ?>
				</td>
				<td align="center">
					<?php echo $item->folder; ?>
				</td>
				<td align="center">
					<?php echo $item->element; ?>
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		
		
	</table>
</form>