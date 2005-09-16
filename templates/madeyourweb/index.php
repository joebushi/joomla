<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php $iso = split( '=', _ISO );
echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />

<?php
echo '<link rel="stylesheet" href="' .$GLOBALS['mosConfig_live_site']. '/templates/' .$GLOBALS['cur_template'] .'/css/template_css.css" type="text/css"/>' ; 
echo '<link rel="stylesheet" href="' .$GLOBALS['mosConfig_live_site']. '/templates/' .$GLOBALS['cur_template'] .'/css/css_color_green.css" type="text/css"/>' ; 


if ((mosCountModules( 'user1' )) && (mosCountModules( 'user2' ))) {  
//if both modules are loaded, we need a 50%-layout for them
	$usera = 'user1';
	$userb = 'user2';
} else if ((mosCountModules( 'user1' )) || (mosCountModules( 'user2' ))) { 
// if only one, then 100% no matter which one.
	$usera = 'user3';
	$userb = 'user3';
}
?>
</head>
<body>
<div id="accessibility">
<a href="index.php#menu"> Menu</a>
<a href="index.php#content"> Content/Inhalt</a>
</div>
<div id="pagewidth-800" >
<div id="header" >
<div id="top-top">
<?php if (mosCountModules( "user4" )) { ?>
<div id="search">
<?php mosLoadModules ( 'user4',-1); ?>
</div>
<?php } ?>
 <script type="text/javascript">
      <!--//--><![CDATA[//><!--
      sfHover = function() {
      	var sfEls = document.getElementById("topmenu").getElementsByTagName("li");
      	for (var i=0; i<sfEls.length; i++) {
      		sfEls[i].onmouseover=function() {
      			this.className+="sfhover";
      		}
      		sfEls[i].onmouseout=function() {
      			this.className=this.className.replace(new RegExp("sfhover\\b"), "");
      		}
      	}
      }
      if (window.attachEvent) window.attachEvent("onload", sfHover);
      
      //--><!]]>
 </script>
<div id="topmenu">
<?php if (mosCountModules( "user3" )) { ?><?php mosLoadModules ( 'user3',-1); ?><?php } ?>
</div>
</div>
<div class="clr"></div>
<div id="top-bottom">
<a href="index.php"><?php echo '<img src="' .$GLOBALS['mosConfig_live_site']. '/templates/' .$GLOBALS['cur_template']. '/images_green/logo.gif" border="0" width="250" height="80" alt="logo" /></a>'; ?>
</div>
<?php if (mosCountModules( "banner" )) { ?><div id="banner"><?php mosLoadModules ( 'banner',-1); ?></div><?php } ?> 
</div>
<div id="outer-800" >
<div id="pathway"> <?php mospathway() ?> </div>
<div id="leftcol"><a name="menu"></a>
<?php if (mosCountModules( "left" )) { ?><?php mosLoadModules ( 'left',-3); ?><?php } ?> 
</div>
<?php if ((mosCountModules( "right" )) || (mosCountModules( "top" ))) { ?>
<div id="maincol-broad-800" >
<?php } else { ?> 
<div id="maincol-wide-800" >

<?php } 
if (mosCountModules( "user1" )) { ?><div id="<?php echo $usera; ?>"><?php mosLoadModules ( 'user1',-2); ?></div><?php }
if (mosCountModules( "user2" )) { ?><div id="<?php echo $userb; ?>"><?php mosLoadModules ( 'user2',-2); ?></div><?php } 
?> 
<div class="clr"></div>
<div class="content"><a name="content"></a><?php mosMainBody(); ?></div>
</div>
<?php if ((mosCountModules( "right" )) || (mosCountModules( "top" ))) { ?>
<div id="rightcol-broad">
<?php mosLoadModules ( 'top',-3); ?>
<?php mosLoadModules ( 'right',-3); ?>
</div>
<?php } ?> 
<div class="clr"></div>
</div>
<div id="footer-800" ><?php include_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/footer.php' ); ?>
<?php mosLoadModules( 'debug', -1 );?>
</div>
</div>
<div id="source">designed by <a title="professionelle joomla templates" href="http://www.madeyourweb.com">www.madeyourweb.com | joomla templates</a></div>
</body>
</html>