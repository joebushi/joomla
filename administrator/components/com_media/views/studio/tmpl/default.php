<?php defined('_JEXEC') or die('Restricted access'); ?>
 
<h1><?php echo "Joomla! Studio"; ?></h1>
       
<form name="input" action="index.php?option=com_media&amp;task=studio.image&amp;folder=<?php echo $this->state->folder; ?>&amp;im[]=<?php echo $this->state->path;?>&amp;img=<?php echo $this->state->image;?>"  method="POST">
        <td width="80%" valign="top">
<div id="wrapper">
	<div id="content">
	<h3 class="tab" title="Resize"><div class="tabtxt"><a href="#">Resize</a></div></h3>
	<div class="tab"><h3 class="tabtxt" title="Rotate"><a href="#">Rotate</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="Effects"><a href="#">Effects</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="Others"><a href="#">Colorize</a></h3></div>
	<div class="tab"><h3 class="tabtxt" title="Others"><a href="#">Others</a></h3></div>
	<div class="boxholder">
		<div class="box">
				<h3><?php echo "Resize"; ?></h3>
		<fieldset id ="resize">
         <h3>Width</h3>
		<input id="width" name="width" type="text" size="4" />
		 x
        <input id="height" name="height" type="text" size="4" />
        <h3>        Height</h3> <strong>Please enter the size in pixels.Max size is 2000px</strong>
         </fieldset>  
		</div>
		    
  <div class="box">
        	<h3><?php echo "Rotate"; ?></h3>
          <fieldset id="degrees">
          <select id="rotate" name="rotate">
          <option value="0"><?php echo "Select"; ?></option>
          <option value="90">90</option>
          <option value="180">180</option>
          <option value="270">270</option>
        </select>
        </fieldset>
        <h3><?php echo "Flip"; ?></h3>
		<fieldset id="flip">
          <select id="flip" name="flip">
          <option value="0"><?php echo"Select"; ?></option>
          <option value="H"><?php echo "V"; ?></option>
          <option value="V"><?php echo"H"; ?></option>
        </select>
        </fieldset>
        </div>
		 <div class="box">
        <h3><?php echo "Effects"; ?></h3>
        <fieldset id="effects">
		 <fieldset><?php echo "Greyscale"; ?>
        <input type="checkbox" name="greyscale" id="greyscale">
        <?php echo "Edge Detect"; ?>
        <input type="checkbox" name="edgedetect" id="edgedetect">
		<?php echo "Mean Removal"; ?>
        <input type="checkbox" name="meanremoval" id="meanrm">
         <?php echo "Sepia"; ?>
        <input type="checkbox" name="sepia" id="sepia">
        </fieldset>
        <fieldset>
        <?php echo "Emboss"; ?>
        <input type="checkbox" name="emboss" id="emboss">
        <?php echo "Blur"; ?>
        <input type="checkbox" name="blur" id="blur">
		<?php echo "Invert"; ?>
        <input type="checkbox" name="negate" id="negate">  
        <?php echo "Drop Shadow"; ?>
        <input type="checkbox" name="drop" id="drop">  
		</fieldset>
		</fieldset>
		</div> 
<div class="box">	 
       	<h3><?php echo "Colorize"; ?></h3>
		<div id="area">
	<div id="knob"></div>
</div>
<p id="upd">XX</p>
		<div id="area2">
	<div id="knob2"></div>
	</div>

<p id="upd2">XX</p>
		<div id="area3">
	<div id="knob3"></div>
</div>
<p id="upd3">XX</p>
<span id="setColor"></span>
		</div>
<div class="box">
	   <strong>Brightness</strong>
	   		<div id="area4">
	<div id="knob4"></div>
</div>
<p id="upd4">XX</p>
<strong>Contrast</strong>
		<div id="area5">
	<div id="knob5"></div>
</div>
<p id="upd5">XX</p>
<strong>Smooth</strong>
<div id="area6">
	<div id="knob6"></div>
	</div>
	<p id="upd6">XX</p>
</div></div></div></div>
		<input type="hidden" size ="3" name="r"  id="r" value="0"/>
		<input type="hidden" size ="3" name="g"  id="g" value="0"/>
		<input type="hidden" size ="3" name="b"  id="b" value="0"/>
		<input type="hidden" size ="3" name="x"  id="x" value="0"/>
		<input type="hidden" size ="3" name="y"  id="y" value="0"/>
		<input type="hidden" size ="3" name="w"  id="w" value="0"/>
		<input type="hidden" size ="3" name="h"  id="h" value="0"/>
		<input type="hidden" size ="3" name="bright"  id="bright" value="0"/>
		<input type="hidden" size ="3" name="contrast"  id="contrast" value="0"/>
		<input type="hidden" size ="3" name="opacity"  id="opacity" value="0"/>
      		</td>  
		    	       			      
 <div id="wrapper1">
	
	<h3 class="tab1" title="options"><div class="tab1txt"><a href="#">Options</a></div></h3>
	<h3 class="tab1" title="Save"><div class="tab1txt"><a href="#">Save</a></div></h3>
	<div class="boxholder">
		<div class="box1">
<fieldset id="apply">
		<button type="submit"><?php echo JText::_( 'Apply' ); ?></button> </form>
		<form>
<input type="button" id="btn" 
onclick="nakul()" 
value="Crop On">
</form>
<form name="undo" action="index.php?option=com_media&amp;task=studio.undo&amp;folder=<?php echo $this->state->folder; ?>&amp;im[]=<?php echo $this->state->path;?>"  method="POST">
<button type="submit"><?php echo JText::_( 'Undo' ); ?></button> </form>
	</div>
		<div class="box1">
		<form name="save" action="index.php?option=com_media&amp;task=studio.save&amp;folder=<?php echo $this->state->folder; ?>&amp;im[]=<?php echo $this->state->path;?>"  method="POST">
		 <?php echo "Save As"; ?>
		<input id="savename" name="savename" type="text" size="40" />
<strong>FOLDER: /images/</strong><input id="savefolder" name="savefolder" type="text" size="40" />
<input type="hidden" size ="3" name="quality"  id="quality" value="80"/>
<strong>Quality(jpeg)</strong>
<div id="area7">
	<div id="knob7"></div>
	</div>
	<p id="upd7">XX</p>
		<button type="submit"><?php echo JText::_( 'Save' ); ?></button> </form>
		</div> </div> </div>  
		 	<div class="image">
							  <img src="<?php echo $this->state->url;?>" id="imge" style="float:center" width="<?php // echo $this->state->width_600; ?>" height="<?php // echo $this->state->height_600; ?>" >   
               </div>    
