<?php
/**
 * @version		$Id: login.php 12471 2009-07-06 00:41:03Z eddieajau $
 * @package		Hathor Accessible Administrator Template
 * @since		1.6
 * @version  	1.04
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Login screen for the backend Administrator
 */

// no direct access
defined('_JEXEC') or die;

$app = &JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />

<!-- Load system style CSS -->
<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />

<!-- Load Template CSS -->
<link href="templates/<?php echo $this->template ?>/css/template.css" rel="stylesheet" type="text/css" />

<!-- Load additional CSS styles for rtl sites -->
<?php  if ($this->direction == 'rtl') : ?>
	<link href="templates/<?php echo $this->template ?>/css/template_rtl.css" rel="stylesheet" type="text/css" />
<?php  endif; ?>

<!-- Load additional CSS styles for High Contrast colors -->
<?php if ($this->params->get('highContrast')) : ?>
	<link href="templates/<?php echo $this->template ?>/css/highcontrast.css" rel="stylesheet" type="text/css" />
<?php  endif; ?>

<!-- Load additional CSS styles for bold Text -->
<?php if ($this->params->get('boldText')) : ?>
	<link href="templates/<?php echo $this->template ?>/css/boldtext.css" rel="stylesheet" type="text/css" />
<?php  endif; ?>

<!-- Load additional CSS styles for Internet Explorer -->
<!--[if IE 7]>
	<link href="templates/<?php echo  $this->template ?>/css/ie7.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lte IE 6]>
	<link href="templates/<?php echo  $this->template ?>/css/ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->

<!-- Load Template JavaScript -->
<script type="text/javascript" src="templates/<?php  echo  $this->template  ?>/js/template.js"></script>

</head>
<body id="login-page">
	<div id="containerwrap">
		
		<!-- Header Logo -->
		<div id="header">
			<h1 class="title"><?php echo $this->params->get('showSiteName') ? $app->getCfg('sitename') . " " . JText::_('Administration') : JText::_('Administration'); ?></h1>	      		
		</div><!-- end header -->
	
		<!-- Content Area -->
		<div id="content">

			<!-- Beginning of Actual Content -->
			<div id="element-box" class="login">		
				<div class="pagetitle"><h2><?php echo JText::_('Joomla_Administration_Login') ?></h2></div>
					
					<!-- System Messages -->
					<jdoc:include type="message" />
					
					<div class="login-inst">
					<p><?php echo JText::_('DESCUSEVALIDLOGIN') ?></p>
					<div id="lock"></div>		
					<a href="<?php echo JURI::root(); ?>"><?php echo JText::_('Go_to_site_Home_Page') ?></a>
					</div>
					
					<!-- Login Component -->
					<div class="login-box">
						<jdoc:include type="component" />
					</div>
				<div class="clr"></div>
			</div><!-- end element-box -->
		
		<noscript>
			<?php echo JText::_('WARNJAVASCRIPT') ?>
		</noscript>		
		
		</div><!-- end content -->
		<div class="clearfooter"></div>	
	</div><!-- end of containerwrap -->
	
	<!-- Footer -->
	<div id="footer">
		<p class="copyright">
			<a href="http://www.joomla.org">Joomla!</a>
			<?php echo JText::_('ISFREESOFTWARE') ?>
		</p>
	</div>
</body>
</html>
