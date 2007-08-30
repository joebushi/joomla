<?php defined('_JEXEC') or die('Restricted access'); ?>
		<tr>
			<td>
				<a>
					<img src="<?php echo $this->_tmp_doc->icon_16; ?>" width="16" height="16" border="0" alt="<?php echo $this->_tmp_doc->name; ?>" />
				</a>
			</td>
			<td class="description">
				<?php echo $this->_tmp_doc->name; ?>
			</td>
			<td>&nbsp;

			</td>
			<td>
				<?php echo $this->_tmp_doc->size; ?>
			</td>
			<td>
				<img src="components/com_media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" onclick="confirmDeleteImage('<?php echo $this->_tmp_doc->name; ?>');" />
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			</td>
		</tr>
