<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$step = JRequest::getCmd('step');
$next = JRequest::getCmd('next');
$prev = JRequest::getCmd('prev');
$refr = JRequest::getCmd('refresh');

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
							4 : <?php echo JText::_('INSTALL_STEP_DBCONFIG') ?>
						</div>
						<div class="step-<?php echo ($step == 'ftpconfig') ? 'on' : 'off' ?>">
							5 : <?php echo JText::_('INSTALL_STEP_FTPCONFIG') ?>
						</div>
						<div class="step-<?php echo ($step == 'loaddata') ? 'on' : 'off' ?>">
							6 : <?php echo JText::_('INSTALL_STEP_LOADDATA') ?>
						</div>
						<div class="step-<?php echo ($step == 'mainconfig') ? 'on' : 'off' ?>">
							7 : <?php echo JText::_('INSTALL_STEP_MAINCONFIG') ?>
						</div>
						<div class="step-<?php echo ($step == 'finish') ? 'on' : 'off' ?>">
							8 : <?php echo JText::_('INSTALL_STEP_FINISH') ?>
						</div>
						<div class="box"></div>
				  	</div> 
			
					<div class="b">
						<div class="b">
							<div class="b"></div>
						</div>
					</div>
			
				</div> <!-- End Step Bar Left Block -->
	
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
									<?php if ( $this->direction == 'ltr' ) : ?>
										<?php if ( $prev ) : ?>
										<div class="button1-right"><div class="prev"><a onclick="submitForm( adminForm, '<?php echo $prev ?>' );" alt="<?php echo JText::_('Previous', true ) ?>"><?php echo JText::_('Previous') ?></a></div></div>
										<?php endif; ?>
										<?php if ( $refr ) : ?>
										<div class="button1-left"><div class="refresh"><a onclick="submitForm( adminForm, '<?php echo $step ?>' );" alt="<?php echo JText::_('Check Again' ,true ) ?>"><?php echo JText::_('Check Again') ?></a></div></div>
										<?php endif; ?>
										<?php if ( $next ) : ?>
										<div class="button1-left"><div class="next"><a onclick="submitForm( adminForm, '<?php echo $next ?>' );" alt="<?php echo JText::_('Next' ,true ) ?>"><?php echo JText::_('Next') ?></a></div></div>
										<?php endif; ?>
									<?php else: ?>
										<?php if ( $next ) : ?>
										<div class="button1-right"><div class="prev"><a onclick="submitForm( adminForm, '<?php echo $next ?>' );" alt="<?php echo JText::_('Next' ,true ) ?>"><?php echo JText::_('Next') ?></a></div></div>
										<?php endif; ?>
										<?php if ( $next ) : ?>
										<div class="button1-left"><div class="next"><a onclick="submitForm( adminForm, '<?php echo $prev ?>' );" alt="<?php echo JText::_('Previous' ,true ) ?>"><?php echo JText::_('Previous') ?></a></div></div>
										<?php endif; ?>
										<?php if ( $refr ) : ?>
										<div class="button1-left"><div class="refresh"><a onclick="submitForm( adminForm, '<?php echo $step ?>' );" alt="<?php echo JText::_('Check Again' ,true ) ?>"><?php echo JText::_('Check Again') ?></a></div></div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<span class="step"><?php echo JText::_('INSTALL_STEP_'.strtoupper($step) ) ?></span>
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
			</div>

			<div class="clr"></div>
				
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
