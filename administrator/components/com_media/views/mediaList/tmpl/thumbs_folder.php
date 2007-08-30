<?php defined('_JEXEC') or die('Restricted access'); ?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a href="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe">
						<img src="components/com_media/images/folder.png" width="80" height="80" border="0" />
					</a>
				</div>
			</div>
			<div class="controls">
				<img src="components/com_media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" onclick="confirmDeleteFolder('<?php echo $this->_tmp_folder->path_relative; ?>', <?php echo $this->_tmp_folder->files+$this->_tmp_folder->folders; ?>);" />
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_folder->path_relative; ?>" />
			</div>
			<div class="imginfoBorder">
				<a href="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="folderframe"><?php echo substr( $this->_tmp_folder->name, 0, 10 ) . ( strlen( $this->_tmp_folder->name ) > 10 ? '...' : ''); ?></a>
			</div>
		</div>
