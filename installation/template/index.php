<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$step = JRequest::getCmd('step', 'lang');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
		<jdoc:include type="head" />

		<link href="template/css/template.css" rel="stylesheet" type="text/css" />
		<?php if($this->direction == 'rtl') : ?>
		<link href="template/css/template_rtl.css" rel="stylesheet" type="text/css" />
		<?php endif; ?>

		<script type="text/javascript" src="template/js/mootools.js"></script>
		<script type="text/javascript" src="includes/js/installation.js"></script>
		<script type="text/javascript" src="template/js/validation.js"></script>

		<script type="text/javascript">
			window.addEvent('domready', function() 
				{ new Accordion($$('h3.moofx-toggler'), $$('div.moofx-slider'), {onActive: function(toggler, i) { toggler.addClass('moofx-toggler-down'); },onBackground: function(toggler, i) { toggler.removeClass('moofx-toggler-down'); },duration: 300,opacity: false, alwaysHide:true, show: 1}); 
			});
  		</script>
	</head>
	<body>
		<div id="header1">
			<div id="header2">
				<div id="header3">
					<div id="version"><?php echo JText::_('Version#') ?></div>
					<span><?php echo JText::_('Installation') ?></span>
				</div>
			</div>
		</div>
		<div id="content-box">
			<div id="content-pad">
				
<div id="stepbar">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>

	<div class="m">
			<h1><?php echo JText::_('Steps') ?></h1>
			<div class="step-<?php echo ($step == 'lang') ? 'on' : 'off' ?>">
				1 : <?php echo JText::_('Language') ?>
			</div>
			<div class="step-<?php echo ($step == 'preinstall') ? 'on' : 'off' ?>">
				2 : <?php echo JText::_('Pre-Installation check') ?>
			</div>
			<div class="step-<?php echo ($step == 'license') ? 'on' : 'off' ?>">
				3 : <?php echo JText::_('License') ?>
			</div>
			<div class="step-<?php echo ($step == 'dbconfig') ? 'on' : 'off' ?>">
				4 : <?php echo JText::_('Database') ?>
			</div>
			<div class="step-<?php echo ($step == 'ftpconfig') ? 'on' : 'off' ?>">
				5 : <?php echo JText::_('FTP') ?>
			</div>
			<div class="step-<?php echo ($step == 'loaddata') ? 'on' : 'off' ?>">
				6 : <?php echo JText::_('Data') ?>
			</div>
			<div class="step-<?php echo ($step == 'mainconfig') ? 'on' : 'off' ?>">
				7 : <?php echo JText::_('Configuration') ?>
			</div>
			<div class="step-<?php echo ($step == 'finish') ? 'on' : 'off' ?>">
				8 : <?php echo JText::_('Finish') ?>
			</div>
		<div class="box"></div>
  	</div>

	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>

</div>
				
				<jdoc:include type="installation" />
			</div>
		</div>
		<div id="footer1">
			<div id="footer2">
				<div id="footer3"></div>
			</div>
		</div>
		<div id="copyright"><a href="http://www.joomla.org">Joomla!</a>
			<?php echo JText::_('ISFREESOFTWARE') ?>
		</div>
	</body>
</html>
