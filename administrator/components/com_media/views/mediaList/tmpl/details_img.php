<?php defined('_JEXEC') or die('Restricted access'); ?>
		<tr>
			<td>
				<a class="img-preview" href="<?php echo $this->baseURL.'/images/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>"><img src="<?php echo $this->baseURL.'/images/'.$this->_tmp_img->path_relative; ?>" width="<?php echo $this->_tmp_img->width; ?>" height="<?php echo $this->_tmp_img->height; ?>" alt="<?php echo $this->_tmp_img->name; ?> - <?php echo $this->_tmp_img->size; ?>" border="0" /></a>
			</td>
			<td class="description">
				<a href="<?php echo $this->baseURL.'/images/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" rel="preview"><?php echo htmlspecialchars( $this->_tmp_img->name, ENT_QUOTES ); ?></a>
			</td>
			<td>
				<?php echo $this->_tmp_img->width; ?> x <?php echo $this->_tmp_img->height; ?>
			</td>
			<td>
				<?php echo $this->_tmp_img->size; ?>
			</td>
			<td>
				<img src="components/com_media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" onclick="confirmDeleteImage('<?php echo $this->_tmp_img->name; ?>');" />
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_img->name; ?>" />
			</td>
		</tr>
