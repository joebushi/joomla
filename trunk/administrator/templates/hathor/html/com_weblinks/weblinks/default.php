<?php
/**
 * @version		$Id: default.php 12290 2009-06-22 10:13:13Z pentacle $
 * @package		Hathor Accessible Administrator Template
 * @since		1.6
 * @version  	1.04
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * add accessibility, labels on input forms 
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('script', 'multiselect.js');
$user	= &JFactory::getUser();
$userId	= $user->get('id');
?>

<form action="<?php echo JRoute::_('index.php?option=com_weblinks'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter-bar">
	<legend class="element-invisible"><?php echo JText::_('Filters'); ?></legend>
		<div class="filter-search">
			<label for="filter_search">
				<?php echo JText::_('JSearch_Filter_Label'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" size="35" title="<?php echo JText::_('Weblinks_Search_in_title'); ?>" />

			<button type="submit">
				<?php echo JText::_('JSearch_Filter_Submit'); ?></button>
			<button type="button" onclick="$('search').value='';this.form.submit();">
				<?php echo JText::_('JSearch_Filter_Clear'); ?></button>
		</div>
		<div class="filter-select">
			<label class="selectlabel" for="filter_access">
				<?php echo JText::_('Filter_Access'); ?>
			</label>
			<select name="filter_access" id="filter_access" class="inputbox">
				<option value=""><?php echo JText::_('JOption_Select_Access');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<label class="selectlabel" for="filter_published">
				<?php echo JText::_('Filter_State'); ?>
			</label> 
			<select name="filter_published" id="filter_published" class="inputbox">
				<option value=""><?php echo JText::_('JOption_Select_Published');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			
			<label class="selectlabel" for="filter_category_id">
				<?php echo JText::_('Filter_Category'); ?>
			</label>
			<select name="filter_category_id" id="filter_category_id" class="inputbox">
				<option value=""><?php echo JText::_('JOption_Select_Category');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_weblinks'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
			
			<button type="button" id="filter-go" onclick="this.form.submit();">
				<?php echo JText::_('Go'); ?></button>
			
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="checkmark-col">
					<input type="checkbox" name="toggle" id="toggle" value="" title="<?php echo JText::_('Checkmark_All'); ?>" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Title', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap state-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Published', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap title category-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Category', 'category_title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap ordering-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Ordering', 'a.ordering', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'weblinks.saveorder'); ?>
				</th>
				<th class="title access-col" >
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Access', 'a.access', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="hits-col">
					<?php echo JHtml::_('grid.sort',  'Weblinks_Hits_Heading', 'a.hits', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="width-5">
					<?php echo JHtml::_('grid.sort', 'JGrid_Heading_Language', 'a.language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="nowrap id-col">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$ordering	= ($this->state->get('list.ordering') == 'a.ordering');
			$item->cat_link 	= JRoute::_('index.php?option=com_categories&extension=com_weblinks&task=edit&type=other&cid[]='. $item->catid);
			$canCreate	= $user->authorise('core.create',		'com_weblinks.category.'.$item->catid);
			$canEdit	= $user->authorise('core.edit',			'com_weblinks.category.'.$item->catid);
			$canChange	= $user->authorise('core.edit.state',	'com_weblinks.category.'.$item->catid);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<th>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</th>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<?php if ($canCreate || $canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_weblinks&task=weblink.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub">
						(<span><?php echo JText::_('JFIELD_ALIAS_LABEL'); ?>:</span> <?php echo $this->escape($item->alias);?>)</p>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'weblinks.');?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, true, 'weblinks.orderup', 'JGrid_Move_Up', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'weblinks.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" title="<?php echo $item->title; ?> order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo $item->hits; ?>
				</td>
				<td class="center">
					<?php echo $item->language ? $this->escape($item->language) : JText::_('JDefault'); ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
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
