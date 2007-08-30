<?php defined('_JEXEC') or die('Restricted access'); ?>
<table width="100%" cellspacing="0">
	<tr valign="top">
		<td width="200px">
			<fieldset id="treeview">
				<legend><?php echo JText::_( 'Folders' ); ?></legend>
				<?php echo $this->loadTemplate('folders'); ?>
			</fieldset>
		</td>
		<td>
			<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
				<fieldset id="folderview">
					<legend><?php echo JText::_( 'Files' ); ?></legend>
					<div class="path">
						<input class="inputbox" type="text" id="folderpath" readonly="readonly" />/
						<input class="inputbox" type="text" id="foldername" name="foldername"  />
						<button type="button" onclick="document.mediamanager.oncreatefolder()" /><?php echo JText::_( 'Create Folder' ); ?></button>
					</div>
					<div class="view">
						<iframe src="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->current;?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0"></iframe>
					</div>
				</fieldset>

				<?php if ($this->require_ftp): ?>
				<fieldset title="<?php echo JText::_('DESCFTPTITLE'); ?>">
					<legend><?php echo JText::_('DESCFTPTITLE'); ?></legend>
					<?php echo JText::_('DESCFTP'); ?>
					<table class="adminform nospace">
						<tbody>
							<tr>
								<td width="120">
									<label for="username"><?php echo JText::_('Username'); ?>:</label>
								</td>
								<td>
									<input type="text" id="username" name="username" class="input_box" size="70" value="" />
								</td>
							</tr>
							<tr>
								<td width="120">
									<label for="password"><?php echo JText::_('Password'); ?>:</label>
								</td>
								<td>
									<input type="password" id="password" name="password" class="input_box" size="70" value="" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<?php endif; ?>

				<input type="hidden" name="option" value="com_media" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="cb1" id="cb1" value="0" />
				<input type="hidden" name="dirpath" id="dirpath" value="<?php echo $this->current; ?>" />
			</form>

			<!-- File Upload Form -->
			<form action="<?php echo JURI::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>" id="uploadForm" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><?php echo JText::_( 'Upload File' ); ?> [ <?php echo JText::_( 'Max' ); ?>&nbsp;<?php echo ($this->config->get('upload_maxsize') / 1000000); ?>M ]</legend>
					<fieldset class="actions">
						<input type="file" id="file-upload" name="Filedata" />
						<input type="submit" id="file-upload-submit" value="<?php echo JText::_('Start Upload'); ?>"/>
						<span id="upload-clear"></span>
					</fieldset>
					<ul class="upload-queue" id="upload-queue">
						<li style="display: none" />
					</ul>
				</fieldset>
				<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
			</form>
		</td>
	</tr>
</table>
