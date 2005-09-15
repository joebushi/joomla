<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php $iso = split( '=', _ISO );
echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<?php mosShowHead(); 
global $color, $size, $screen;
if ((!$color) || (!$size) || (!$screen)) {
include ("templates/$GLOBALS[cur_template]/template_configuration.php");
}
?>
<?php
echo '<link rel="stylesheet" href="' .$GLOBALS['mosConfig_live_site']. '/templates/' .$GLOBALS['cur_template'] .'/css/template_css.css" type="text/css"/>' ; 
echo '<link rel="stylesheet" href="' .$GLOBALS['mosConfig_live_site']. '/templates/' .$GLOBALS['cur_template'] .'/css/css_color_' .$color. '.css" type="text/css"/>' ; 
if ( $my->id ) {  //is user logged in? 

// The next query checks if there is something to edit or create. For docman and com_events it is also prepared (different tasks).
// If your editor isn´t loaded in the frontend, check if there is another "task" in the URL than the following. 
// If it doesn´t work, just put // in front of the following line.
//if($_REQUEST["task"] == "edit" || $_REQUEST["task"] == "new" || $_REQUEST["task"] == "modify" || $_REQUEST["task"] == "upload" || $_REQUEST["task"] == "editdoc"){ 
// *** removed because of components which are not standard. Uncomment the lines above to increase speed.

include ("editor/editor.php");
initEditor();}

//} //uncomment if lines above are uncommented, too.
// count some stuff for layout
if ((mosCountModules( "user1" )) && (mosCountModules( "user2" ))) {  //if both modules are loaded, we need a 50%-layout for them
	$usera='user1';
	$userb='user2';
}
	else if ((mosCountModules( "user1" )) || (mosCountModules( "user2" ))) { // if only one, then 100% no matter which one.
		$usera='user3';
		$userb='user3';
	}
?>
</head>
<body>
<div id="accessibility">
<a href="index.php#menu"> Menu</a>
<a href="index.php#content"> Content/Inhalt</a>
</div>
<div id="pagewidth-<?php echo $screen; ?>" >
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
<a href="index.php"><?php echo '<img src="' .$GLOBALS['mosConfig_live_site']. '/templates/' .$GLOBALS['cur_template']. '/images_' .$color. '/logo.gif" border="0" width="250" height="80" alt="logo" /></a>'; ?>
</div>
<?php if (mosCountModules( "banner" )) { ?><div id="banner"><?php mosLoadModules ( 'banner',-1); ?></div><?php } ?> 
</div>
<div id="outer-<?php echo $screen; ?>" >
<div id="pathway"> <?php mospathway() ?> </div>
<div id="leftcol"><a name="menu"></a>
<?php if (mosCountModules( "left" )) { ?><?php mosLoadModules ( 'left',-3); ?><?php } ?> 
</div>
<?php if ((mosCountModules( "right" )) || (mosCountModules( "top" ))) { ?>
<div id="maincol-<?php echo $size; ?>-<?php echo $screen; ?>" >
<?php } else { ?> 
<div id="maincol-wide-<?php echo $screen; ?>" >

<?php } 
if (mosCountModules( "user1" )) { ?><div id="<?php echo $usera; ?>"><?php mosLoadModules ( 'user1',-2); ?></div><?php }
if (mosCountModules( "user2" )) { ?><div id="<?php echo $userb; ?>"><?php mosLoadModules ( 'user2',-2); ?></div><?php } 
?> 
<div class="clr"></div>
<div class="content"><a name="content"></a><?php mosMainBody(); ?></div>
</div>
<?php if ((mosCountModules( "right" )) || (mosCountModules( "top" ))) { ?>
<div id="rightcol-<?php echo $size; ?>">
<?php mosLoadModules ( 'top',-3); ?>
<?php mosLoadModules ( 'right',-3); ?>
</div>
<?php } ?> 
<div class="clr"></div>
</div>
<div id="footer-<?php echo $screen; ?>" ><?php include_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/footer.php' ); ?>
<?php mosLoadModules( 'debug', -1 );?>
</div>
</div>
<div id="source">designed by <a title="professionelle joomla templates" href="http://www.madeyourweb.com">www.madeyourweb.com | joomla templates</a></div>
</body>
</html>