<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('script', 'multiselect.js');
$user	= JFactory::getUser();
$userId	= $user->get('id');
?>
<form action="<?php echo JRoute::_('index.php?option=com_banners&view=tracks'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOption_Select_Category');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_banners'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_client_id" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', JFormFieldBannerClient::getOptions(), 'value', 'text', $this->state->get('filter.client_id'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort',  'Banners_Heading_Name', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="20%" class="nowrap">
					<?php echo JHtml::_('grid.sort',   'Banners_Heading_Client', 'client_name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="20%">
					<?php echo JHtml::_('grid.sort',  'JGrid_Heading_Category', 'category_title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JHtml::_('grid.sort',   'Banners_Heading_Type', 'track_type', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JHtml::_('grid.sort',   'Banners_Heading_Count', 'count', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JHtml::_('grid.sort',   'Banners_Heading_Date', 'date', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($this->state->get('list.ordering') == 'ordering');
			?>
			<tr class="row<?php echo $i % 2; ?>">
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
