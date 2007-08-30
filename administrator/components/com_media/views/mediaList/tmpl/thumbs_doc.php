<?php defined('_JEXEC') or die('Restricted access'); ?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
				 	<a style="display: block; width: 100%; height: 100%">
						<img border="0" src="<?php echo $this->_tmp_doc->icon_32 ?>" alt="<?php echo $this->_tmp_doc->name; ?>" />
					</a>
				</div>
			</div>
			<div class="controls">
				<img src="components/com_media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" onclick="confirmDeleteImage('<?php echo $this->_tmp_doc->name; ?>');" />
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			</div>
			<div class="imginfoBorder">
				<?php echo $this->_tmp_doc->name; ?>
			</div>
		</div>
