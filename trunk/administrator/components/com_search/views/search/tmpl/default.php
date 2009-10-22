<?php defined('_JEXEC') or die; ?>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl"><?php echo JText::_('Filter'); ?>:</label>
			<input type="text" name="search" id="search" value="<?php echo $this->escape($this->search); ?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
			</div>
		<div class="filter-select fltrt">
			<span class="adminlist-status"><?php echo JText::_('Search Logging'); ?>:</span>
			<?php echo $this->enabled ? '<span class="enabled">'. JText::_('Enabled') .'</span>' : '<span class="disabled">'. JText::_('Disabled') .'</span>' ?>
			
			<span class="adminlist-searchstatus">
			<?php if ($this->showResults) : ?>
				<a href="index.php?option=com_search&amp;search_results=0"><?php echo JText::_('Hide Search Results'); ?></a>
			<?php else : ?>
				<a href="index.php?option=com_search&amp;search_results=1"><?php echo JText::_('Show Search Results'); ?></a>
			<?php endif; ?>
			</span>
		</div>
	</fieldset>
	<div class="clr"> </div>

		<table class="adminlist">
			<thead>
				<tr>
					<th width="10"><?php echo JText::_('NUM'); ?></th>
					<th><?php echo JHtml::_('grid.sort',   'Search Text', 'search_term', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
					<th class="nowrap" width="20%"><?php echo JHtml::_('grid.sort',   'Times Requested', 'hits', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
					<?php
					if ($this->showResults) : ?>
						<th class="nowrap" width="20%"><?php echo JText::_('Results Returned'); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4"><?php echo $this->pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n = count($this->items); $i < $n; $i++) {
				$row = &$this->items[$i];
				?>
				<tr class="row<?php echo $k;?>">
					<td class="right">
						<?php echo $i+1+$this->pageNav->limitstart; ?>
					</td>
					<td>
						<?php echo htmlspecialchars($row->search_term, ENT_QUOTES, 'UTF-8'); ?>
					</td>
					<td class="center">
						<?php echo $row->hits; ?>
					</td>
					<?php if ($this->showResults) : ?>
					<td class="center">
						<?php echo $row->returns; ?>
					</td>
					<?php endif; ?>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>