<?php defined('_JEXEC') or die; ?>

<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post" name="searchForm">

			<fieldset class="word">
				<label for="search_searchword">
					<?php echo JText::_('Search Keyword'); ?>:
				</label>
				<input type="text" name="searchword" id="search_searchword" size="30" maxlength="20" value="<?php echo $this->escape($this->searchword); ?>" class="inputbox" />
				<button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_('Search');?></button>
			</fieldset>

			<fieldset class="phrases">
			<legend><?php echo JText::_('Search Phrase');?>:</legend>
				<?php echo $this->lists['searchphrase']; ?>

				<label for="ordering">
					<?php echo JText::_('Ordering');?>:
				</label>
				<?php echo $this->lists['ordering'];?>
			</fieldset>

	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only">
		<legend><?php echo JText::_('Search Only');?>:</legend>
		<?php foreach ($this->searchareas['search'] as $val => $txt) :
			$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
		?>
		<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area_<?php echo $val;?>" <?php echo $checked;?> />
			<label for="area_<?php echo $val;?>">
				<?php echo JText::_($txt); ?>
			</label>
		<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>


	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">

			<p><?php echo JText::_('Search Keyword') .' <strong>'. $this->escape($this->searchword) .'</strong>'; ?>

			<?php echo $this->result; ?></p>

</div>


<?php if ($this->total > 0) : ?>

	<div class="form_limit">
		<label for="limit">
			<?php echo JText::_('Display Num'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
<p class="counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>

<?php endif; ?>

<input type="hidden" name="task"   value="search" />
</form>
