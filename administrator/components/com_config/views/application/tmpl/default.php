<?php
// Load tooltips behavior
		JHtml::_('behavior.tooltip');
		JHtml::_('behavior.switcher');

		// Build the component's submenu
		$contents = '';
		$tmplpath = dirname(__FILE__).DS.'tmpl';
		ob_start();
		require_once('navigation.php');
		$contents = ob_get_contents();
		ob_end_clean();

		// Set document data
		$document = JFactory::getDocument();
		$document->setBuffer($contents, 'modules', 'submenu');

		$document->addScriptDeclaration("
			document.switcher = null;
			window.addEvent('domready', function(){
			 	toggler = $('submenu')
			  	element = $('config-document')
			  	if (element) {
			  		document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getAttribute('class')});
			  	}
			});
		");

?>	

<form action="index.php" method="post" name="adminForm">
	<?php if ($this->ftp) {
		require_once('ftp.php');
	} ?>
	<div id="config-document">
		<div id="page-site">
			<table class="noshow">
				<tr>
					<td width="65%">
						<?php require_once('config_site.php'); ?>
						<?php require_once('config_metadata.php'); ?>
					</td>
					<td width="35%">
						<?php require_once('config_seo.php'); ?>
					</td>
				</tr>
			</table>
		</div>
		<div id="page-system">
			<table class="noshow">
				<tr>
					<td width="60%">
						<?php require_once('config_system.php'); ?>
						<fieldset class="adminform">
							<legend><?php echo JText::_('User Settings'); ?></legend>
							<?php echo $this->usersParams->render('userparams'); ?>
						</fieldset>
						<fieldset class="adminform">
							<legend><?php echo JText::_('Media Settings'); ?>
			<span class="error hasTip" title="<?php echo JText::_('Warning');?>::<?php echo JText::_('WARNPATHCHANGES'); ?>">
				<?php echo $this->WarningIcon(); ?>
			</span>
							</legend>
							<?php echo $this->mediaParams->render('mediaparams'); ?>
						</fieldset>
					</td>
					<td width="40%">
						<?php require_once('config_debug.php'); ?>
						<?php require_once('config_cache.php'); ?>
						<?php require_once('config_session.php'); ?>
					</td>
				</tr>
			</table>
		</div>
		<div id="page-server">
			<table class="noshow">
				<tr>
					<td width="60%">
						<?php require_once('config_server.php'); ?>
						<?php require_once('config_locale.php'); ?>
						<?php require_once('config_ftp.php'); ?>
					</td>
					<td width="40%">
						<?php require_once('config_database.php'); ?>
						<?php require_once('config_mail.php'); ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="c" value="global" />
	<input type="hidden" name="live_site" value="<?php echo isset($this->data->live_site) ? $this->data->live_site : ''; ?>" />
	<input type="hidden" name="option" value="com_config" />
	<input type="hidden" name="secret" value="<?php echo $this->data->secret; ?>" />
	<input type="hidden" name="root_user" value="<?php echo $this->data->root_user; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>