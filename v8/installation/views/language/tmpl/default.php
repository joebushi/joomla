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
		<?php echo JHtml::_('installation.stepbar', 1); ?>
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
						<div class="button1-left"><div class="next"><a onclick="validateForm(adminForm, 'setup.setlanguage');" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a></div></div>
					<?php elseif ($this->document->direction == 'rtl') : ?>
						<div class="button1-right"><div class="prev"><a onclick="validateForm(adminForm, 'setup.setlanguage');" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a></div></div>
					<?php endif; ?>
					</div>
					<span class="step"><?php echo JText::_('Instl_Language_Title'); ?></span>
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
					<h2><?php echo JText::_('Instl_Select_Language_Title'); ?></h2>
					<div class="install-text">
						<?php echo JText::_('Instl_Select_Language_Desc'); ?>
					</div>
					<div class="install-body">
						<div class="t">
							<div class="t">
								<div class="t"></div>
							</div>
						</div>
						<div class="m">
							<fieldset>
								<?php echo $this->form->getInput('language'); ?>
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