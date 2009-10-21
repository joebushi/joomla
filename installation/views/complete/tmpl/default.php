<?php
/**
 * @version		$Id$
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the JavaScript behaviors.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<script language="JavaScript" type="text/javascript">
<!--
	function validateForm(frm, task) {
		Joomla.submitform(task);
	}
// -->
</script>

<div id="stepbar">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
		<?php echo JHtml::_('installation.stepbar', 7); ?>
		<div class="box"></div>
  	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<form action="index.php" method="post" name="adminForm" class="form-validate">
<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<div class="far-right">
<?php if ($this->document->direction == 'ltr') : ?>
					<div class="button1-left"><div class="site"><a href="<?php echo JURI::root(); ?>" title="<?php echo JText::_('Instl_Finish_Site'); ?>"><?php echo JText::_('Instl_Finish_Site'); ?></a></div></div>
					<div class="button1-left"><div class="admin"><a href="<?php echo JURI::root(); ?>administrator/" title="<?php echo JText::_('Instl_Finish_Admin'); ?>"><?php echo JText::_('Instl_Finish_Admin'); ?></a></div></div>
<?php elseif ($this->document->direction == 'rtl') : ?>
					<div class="button1-left"><div class="admin"><a href="<?php echo JURI::root(); ?>administrator/" title="<?php echo JText::_('Instl_Finish_Admin'); ?>"><?php echo JText::_('Instl_Finish_Admin'); ?></a></div></div>
					<div class="button1-left"><div class="site"><a href="<?php echo JURI::root(); ?>" title="<?php echo JText::_('Instl_Finish_Site'); ?>"><?php echo JText::_('Instl_Finish_Site'); ?></a></div></div>
<?php endif; ?>
				</div>
				<span class="step"><?php echo JText::_('Instl_Finish_Title'); ?></span>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
		<div id="installer">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<h2><?php echo JText::_('Instl_Finish_CONGRATULATIONS'); ?></h2>
				<div class="install-text">
					<?php echo JText::_('Instl_Finish_finishButtons'); ?>
					<?php echo JText::_('Instl_Finish_languageinfo'); ?>
				</div>
				<div class="install-body">
					<div class="t">
						<div class="t">
							<div class="t"></div>
						</div>
					</div>
					<div class="m">
						<fieldset>
							<table class="final-table">
								<tr>
									<td class="error" align="center">
										<?php echo JText::_('Instl_Finish_removeInstallation'); ?>
									</td>
								</tr>
								<tr>
									<td align="center">
										<h3>
										<?php echo JText::_('Instl_Finish_Administration_Login_Details'); ?>
										</h3>
									</td>
								</tr>
								<tr>
									<td align="center" class="notice">
										<?php echo JText::_('Instl_Finish_Username'); ?>: <?php echo $this->options['admin_user']; ?>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td align="center" class="notice">
										<div id="cpanel">
											<div>
												<div class="icon">
													<a href="http://help.joomla.org/content/view/1651/243/" target="_blank">
													<br />
													<b><?php echo JText::_('Instl_Finish_languagebuttonlineone'); ?></b>
													<br />
													<?php echo JText::_('Instl_Finish_languagebuttonlinetwo'); ?>
													<br /><br />
													</a>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<?php if ($this->config) : ?>
								<tr>
									<td class="small">
										<?php echo JText::_('Instl_Finish_confProblem'); ?>
									</td>
								</tr>
								<tr>
									<td align="center">
										<textarea rows="5" cols="60" name="configcode" onclick="this.form.configcode.focus();this.form.configcode.select();" ><?php echo $this->config; ?></textarea>
									</td>
								</tr>
								<?php endif; ?>
							</table>
						</fieldset>
						<div class="clr"></div>
					</div>
					<div class="b">
						<div class="b">
							<div class="b"></div>
						</div>
					</div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
