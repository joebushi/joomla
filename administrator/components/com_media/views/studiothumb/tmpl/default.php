<?php defined('_JEXEC') or die('Restricted access'); ?>
 
<h1><?php echo "Advanced Options"; ?></h1>
<fieldset>
       <div class="thumb">
               <h3>Create Thumbnail</h3>


<strong>Save As </strong>
<form name="thumbnail" action="index.php?option=com_media&amp;task=studio.thumbnail&amp;folder=<?php echo $this->state->folder; ?>&amp;im[]=<?php echo $this->state->path;?>&amp;img=<?php echo $this->state->image;?>"  method="POST">
<input id="savename" name="savename" type="text" size="60" />
		<button type="submit" ><?php echo JText::_( 'Thumbnail' ); ?></button> </form>
		<strong>Default name is th_FILENAME</strong>
		</div>
</fieldset>
<fieldset>
<div class="watermark">
<h3>WaterMark </h3> 
<strong>Save As </strong>
<form name="input" action="index.php?option=com_media&amp;task=studio.watermark&amp;folder=<?php echo $this->state->folder; ?>&amp;im[]=<?php echo $this->state->path;?>&amp;img=<?php echo $this->state->image;?>"  method="POST"> 
<input id="savename1" name="savename1" type="text" size="60" />
<select id="position" name="position">
          <option value="0"><?php echo"Position"; ?></option>
          <option value="11">Random</option>
		  <option value="1">Top-Right</option>
          <option value="2">Top-Left</option>
          <option value="3">Bottom-Right</option>
          <option value="4">Bottom-Left</option>
          <option value="5">Center</option>
          <option value="6">Top</option>
          <option value="7">Bottom</option>
          <option value="8">Left</option>
          <option value="9">Right</option>
          
</select>
<button type="submit" ><?php echo JText::_( 'Watermark' ); ?></button> </form>
<h4><?php echo $this->state->watermark; ?></h4>
<strong>Default name is wm_FILENAME</strong>

		


</div>
<div class ="rename">
</fieldset>
<fieldset>
<h3>Rename</h3>
<form name="renameform" action="index.php?option=com_media&amp;task=studio.rename&amp;folder=<?php echo $this->state->folder; ?>&amp;im[]=<?php echo $this->state->path;?>&amp;img=<?php echo $this->state->image;?>"  method="POST">
<input id="rename" name="rename" type="text" size="40" />
<button type="submit" ><?php echo JText::_( 'Rename' ); ?></button> </form>
  </fieldset> 
  </div>

