<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');

$n = count($this->articles);

?>
<?php if (empty($this->articles)) : ?>
	<!--  no articles -->
<?php else : ?>
	<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
	
	<?php if ($this->params->get('filter_field') != 'hide') :?>
	<fieldset class="filter">
	<legend class="element-invisible"><?php echo JText::_('JContent_Filter_Label'); ?></legend>
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_($this->escape($this->params->get('filter_field')) . '_' . 'Filter_Label').'&nbsp;'; ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php /* echo $this->escape($this->lists['filter']);*/ ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('Content_Filter_Search_Desc'); ?>" />
		</div>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_pagination_limit')) : ?>
		<div class="display">
			<?php echo JText::_('Display Num'); ?>&nbsp;
			<!-- @TODO pagination -->
			<?php /* echo $this->pagination->getLimitBox(); */ ?>
		</div>
	<?php endif; ?>
	</fieldset>

<table class="category">	
	<?php if ($this->params->get('show_headings')) :?>
	<thead><tr>
		<?php if ($this->params->get('show_title')) : ?>
		<th class="item-title" id="tableOrdering">
			<!-- @TODO replace with the ordering part -->
			<?php  echo "title ordering here" /* JHTML::_('grid.sort', 'Content_Heading_Title', 'a.title', $this->lists['order_Dir'], $this->lists['order'])*/ ; ?>
		</th>
		<?php endif; ?>
		<?php if ($this->params->get('show_date') != 'hide') : ?>
			<th class="item-date" id="tableOrdering2">
				<?php /*  echo JHTML::_('grid.sort', 'Content_'.$this->params->get('show_date').'_Date', 'a.created', $this->lists['order_Dir'], $this->lists['order']); */ ?>
				<!-- @TODO replace with the ordering line -->
				<?php echo JText::_('Content_'.$this->params->get('show_date').'_Date')?>
			</th>
		<?php endif; ?>
	</tr></thead>
	<?php endif; ?>

	<tbody>
	<!-- why is $article is reference? -->
		<?php foreach ($this->articles as $i => &$article) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="<?php echo JRoute::_(ContentRoute::article($article->slug, $article->catslug)); ?>">
					<?php echo $this->escape($article->title); ?></a>
				</td>
				<?php if ($this->params->get('show_date') != 'hide') : ?>
					<td>
						<?php echo JHTML::_('date', $article->displayDate, $this->escape(
						$this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?>
					</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
	</table>
	
	<?php if ($this->params->get('show_pagination')) : ?>
		<div class="jpagination">
			<div class="jpag-results">
				Page X of X will be here
				<?php // echo $this->pagination->getPagesCounter(); ?>
			</div>
			Pagination Links will be here
			<?php // echo $this->pagination->getPagesLinks(); ?>		
		</div>
	<?php endif; ?>	

<?php endif; ?>

<!-- @TODO add hidden inputs -->

</form>
