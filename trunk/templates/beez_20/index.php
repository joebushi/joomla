<?php
/**
 * @author      Angie Radtke <a.radtke@derauftritt.de>
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');


$showRightColumn = $this->countModules('login or right');
JHTML::_( 'behavior.mootools' );
$color = $this->params->get('templatecolor');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
        <jdoc:include type="head" />
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/template.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/position.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/layout.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/print.css" type="text/css" media="Print" />
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/general.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/<?php echo $color; ?>.css" type="text/css" />
        <?php if($this->direction == 'rtl') : ?>
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_20/css/template_rtl.css" type="text/css" />
        <?php endif; ?>
        <!--[if lte IE 6]>
                <link href="<?php echo $this->baseurl ?>/templates/beez_20/css/ieonly.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <!--[if IE 7]>
                <link href="<?php echo $this->baseurl ?>/templates/beez_20/css/ie7only.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/beez_20/javascript/md_stylechanger.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/beez_20/javascript/hide.js"></script>
         <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/beez_20/javascript/html5.js"></script>


        <script type="text/javascript">

//       read params.ini

        var big ='<?php echo $this->params->get('wrapperLarge');?>%';
        var small='<?php echo $this->params->get('wrapperSmall'); ?>%';
        var altopen='<?php echo JText::_('auf'); ?>';
        var altclose='<?php echo JText::_('zu'); ?>';
        var rightopen='<?php echo JText::_('Open Info'); ?>';
        var rightclose='<?php echo JText::_('Close'); ?>';
        </script>

<?php $navposition=$this->params->get('navposition');

?>



</head>
<body>
<div id="all">


<div id="back">

<header id="header">
             <div class="logoheader">
               <h1 id="logo">

               <img  src="<?php echo $this->baseurl ?>/templates/beez_20/images/beez.gif"  alt="<?php echo JText::_('Logo Beez, Three little Bees'); ?>" />

                 <span class="header1"><?php echo JText::_('Joomla Accessible Template'); ?></span>
                  </h1>

                 </div>
<!-- end logoheader -->

<ul class="skiplinks">
<li><a href="#content" class="u2"><?php echo JText::_('Skip to Content'); ?></a></li>
<li><a href="#nav" class="u2"><?php echo JText::_('Jump to Main Navigation and Login'); ?></a></li>
<li><a href="#additional" class="u2"><?php echo JText::_('Jump to additional Information'); ?></a></li>
</ul>


<h2 class="unseen">
<?php echo JText::_('VIEW, NAVIGATION AND SEARCH'); ?>
</h2>

<div id="line">
<div id="fontsize">
 <script type="text/javascript">
                                //<![CDATA[
                                        document.write('<h3><?php echo JText::_('Fontsize:'); ?></h3><p class="fontsize">');
                                        document.write('<a href="index.php" title="<?php echo JText::_('Increase size'); ?>" onclick="changeFontSize(2); return false;" class="larger"><?php echo JText::_('bigger'); ?></a><span class="unseen">&nbsp;</span>');
   document.write('<a href="index.php" title="<?php echo JText::_('Revert styles to default'); ?>" onclick="revertStyles(); return false;" class="reset"> <?php echo JText::_('reset'); ?></a>');

                                        document.write(' <a href="index.php" title="<?php echo JText::_('Decrease size'); ?>" onclick="changeFontSize(-2); return false;" class="smaller"><?php echo JText::_('smaller'); ?></a><span class="unseen">&nbsp;</span></p>');

                                //]]>
                                </script>
                        </div>
<jdoc:include type="modules" name="user3" />
</div>
<jdoc:include type="modules" name="user4" />

</header>



              <div id="<?php echo $showRightColumn ? 'contentarea2' : 'contentarea'; ?>">
             <div id="breadcrumbs">
                                <p>
                                        <?php echo JText::_('You are here:'); ?>
                                        <jdoc:include type="modules" name="breadcrumb" />
                                </p>
             </div>

<?php if($navposition=='left') : ?>



                        <nav class="left1" id="nav">
                         <jdoc:include type="modules" name="user1" style="beezDivision" headerLevel="3" />
                        <jdoc:include type="modules" name="left" style="beezHide" headerLevel="3"  />
                        <jdoc:include type="modules" name="user2" style="beezDivision" headerLevel="3" />

                        </nav>

<?php endif; ?>



   <div id="<?php echo $showRightColumn ? 'wrapper' : 'wrapper2'; ?>"><?php if($this->countModules('top')) : ?>

                <div class="news">
                <jdoc:include type="modules" name="top" style="beezDivision" headerLevel="1" />
                </div>
<?php endif ; ?>

                        <div id="main">
                                <?php if ($this->getBuffer('message')) : ?>
                                <div class="error">
                                        <h2>
                                                <?php echo JText::_('Message'); ?>
                                        </h2>
                                        <jdoc:include type="message" />
                                </div>
                                <?php endif; ?>

                                <jdoc:include type="component" />
                        </div><!-- end main -->


                        </div><!-- wrapper -->
<?php if($navposition=='center') : ?>

                        <nav id="nav" class="left">
                        <jdoc:include type="modules" name="user1" style="beezDivision" headerLevel="3" />
                        <jdoc:include type="modules" name="left" style="beezHide" headerLevel="3"  />
                       <jdoc:include type="modules" name="user1" style="beezDivision" headerLevel="3" />
                        </nav>

<?php endif; ?>

<!-- right -->
 <?php if ($showRightColumn) : ?>

                                <h2 class="unseen">
                                <?php echo JText::_('Additional Information'); ?>
                                </h2>
<div id="close">   <a href="#" onclick="auf('right')">
<span id="bild">Close</span> </a>

</div>
                       <aside id="right">



                                <jdoc:include type="modules" name="right" style="beezDivision" headerLevel="3" />

                        </aside>  <?php endif; ?>




                        <div class="wrap"></div>


                </div> <!-- back -->
<footer id="footer">
                        <p class="syndicate">
                                <jdoc:include type="modules" name="syndicate" />
                        </p>

                        <p>
                                <?php echo JText::_('Powered by');?> <a href="http://www.joomla.org/">Joomla!</a>
                        </p>
</footer><!-- footer -->
                </div><!-- contentarea -->

        </div><!-- all -->

        <jdoc:include type="modules" name="debug" />

</body>
</html>