<?php defined('_JEXEC') or die; ?>

<?php
echo $pane->startPane('menu-pane');
echo $pane->startPanel(JText::_('Module Parameters'), 'param-page');

if ($params = $this->params->render('params')): ?>
	<fieldset class="panelform-legacy"><?php echo $params ?></fieldset>
<?php
else: ?>
	<div class="noparams-notice"><?php echo JText::_('There are no parameters for this item'); ?></div>
<?php
endif;
echo $pane->endPanel();

if ($this->params->getNumParams('advanced')) {
	echo $pane->startPanel(JText::_('Advanced Parameters'), 'advanced-page');
	if ($params = $this->params->render('params', 'advanced')): ?>
		<fieldset class="panelform-legacy"><?php echo $params ?></fieldset>
	<?php else : ?>
		<div class="noparams-notice"><?php echo JText::_('There are no advanced parameters for this item'); ?></div>
	<?php endif;
	echo $pane->endPanel();
}

if ($this->params->getNumParams('legacy')) {
	echo $pane->startPanel(JText::_('Legacy Parameters'), 'legacy-page');
	if ($params = $this->params->render('params', 'legacy')): ?>
		<fieldset class="panelform-legacy"><?php echo $params ?></fieldset>
	<?php else : ?>
		<div class="noparams-notice"><?php echo JText::_('There are no legacy parameters for this item'); ?></div>
	<?php endif;
	echo $pane->endPanel();
}


if ($this->params->getNumParams('other')) {
	echo $pane->startPanel(JText::_('Other Parameters'), 'other-page');
	if ($params = $this->params->render('params', 'other')) : ?>
		<fieldset class="panelform-legacy"><?php echo $params ?></fieldset>
	<?php else : ?>
		<div class="noparams-notice"><?php echo JText::_('There are no other parameters for this item'); ?></div>
	<?php endif;
	echo $pane->endPanel();
}

echo $pane->endPane();