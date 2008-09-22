<?php
/**
 * @author Nakul Ganesh S
 * GSoC 2008
 * @mentor Deborah Susan Clarkson
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
/**
 * Weblinks Weblink Controller
 *
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.5
 */
class MediaControllerStudio extends MediaController
{
	/**
	 * Display the Joomla! Studio 
	 *
	 * @param none
	 * 
	 */
	
	function display()
	{
		jimport('joomla.filesystem.path');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        JClientHelper::setCredentialsFromRequest('ftp');		
		parent::display();	
		
	}
	
	/**
	 * Edits images and save them.
	 * Includes
	 * 
	 * 
	 */

	function image()
    {
	        global $mainframe;
	        jimport('joomla.filesystem.path');
	        // Set FTP credentials, if given
	        jimport('joomla.client.helper');
	        JClientHelper::setCredentialsFromRequest('ftp');
			
			//Absolute image path 
			$paths = JRequest::getVar('im', 'array()', '', 'array');
			//The image parent folder
	        $folder = JRequest::getVar('folder', '', '', 'path');
	        //The destination folder
			$savepaths = JRequest::getVar('img', '', '', 'name');
			//Width for image resizing
			$width = JRequest::getCmd('width','');
			//Height for image resizing
			$height = JRequest::getCmd('height','');
			//Degree of image rotation (90/180/270)
			$rotate = JRequest::getVar('rotate','','POST');
			//Flip orientation (H or V)
			$flip = JRequest::getVar('flip','','POST');
			//Grayscale T or F
			$greyscale = JRequest::getVar('greyscale','','POST');
			//Edge Detect T or F
			$edgedetect = JRequest::getVar('edgedetect','','POST');
			//Mean Removal T or F
			$meanrm = JRequest::getVar('meanrm','','POST');
			//Sepia T or F
			$sepia = JRequest::getVar('sepia','','POST');
			//Gaussian Blur T or F
			$blur = JRequest::getVar('blur','','POST');
			//Emboss T or F
			$emboss = JRequest::getVar('emboss','','POST');
			//Drop Shadow T or F
			$drop = JRequest::getVar('drop','','POST');
			//Image Colorize (rgb)
			$r = JRequest::getVar('r','','POST');
			$g = JRequest::getVar('g','','POST');
			$b = JRequest::getVar('b','','POST');
			//Brightness set
			$bright = JRequest::getVar('bright','','POST');
			//Contrast set
			$contrast = JRequest::getVar('contrast','','POST');
			//Negate T or F
			$negate = JRequest::getVar('negate','','POST');
			//Smoothness T or F
			$opacity = JRequest::getVar('opacity','','POST');
			//Image crop w=width,h=height,x&y coordinates
			$w = JRequest::getVar('w','','POST');
			$h = JRequest::getVar('h','','POST');
			$x = JRequest::getVar('x','','POST');
			$y = JRequest::getVar('y','','POST');
	
	        	if (count($paths))
	        		{	
	            		foreach ($paths as $path)
	            			{
								$image_path =$path;
	              			}	
				  	}
	        
	
		  
			//Get image extension
		 	$ext = strtolower(end(explode('.', $image_path)));
		 	//Path where the temporary image is to be stored for UNDO
		 	$unopath = JPATH::clean(JPATH_SITE . DS ."images".DS."tmp". DS . "uno." . $ext);
		 	/*
		 	* Create the image according to its type,
		 	* Quality of JPEG set to 90,
		 	* unopath saves a copy of the original file for "undo" purpose
		 	*/
		 
		    if ($ext == 'jpg' || $ext == 'jpeg')
	        	{
	            	$JSimg = imagecreatefromjpeg($image_path);
	            	imagejpeg($JSimg,$unopath,90);
				} else
	        if ($ext == 'png')
	            {
	                $JSimg = imagecreatefrompng($image_path);
	                imagepng($JSimg,$unopath);
	                # Only if your version of GD includes GIF support
	            } else
	        if ($ext == 'gif')
	           {
	               $JSimg = imagecreatefromgif($image_path);
	                imagegif($JSimg,$unopath);
	           }
			//Create a copy of the newly created image 
			$tmppath = JPATH::clean(JPATH_SITE . DS ."images".DS."tmp".DS. 'tmpimg.'.$ext);
			
			//Resizing the Image
			if($width || $height) 
			{ 
				if($width && $height) 
				{
					$newWidth = $width;
					$newHeight = $height;
			
				} elseif($width) 
				{
					$newWidth 	=  $width;
					$newHeight 	=  $newWidth/imagesx($JSimg)*imagesy($JSimg);
				} elseif($height) 
				{
					$newHeight	= $height;
					$newWidth 	= $newHeight/imagesy($JSimg)*imagesx($JSimg);
				}
		
			$newImg = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($newImg, $JSimg, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($JSimg), imagesy($JSimg));
			$JSimg = $newImg;
			imagedestroy($newImg);
			}
	/*Flip H or V */
			if($flip == H) 
			{
		
				$w = imagesx($JSimg);
	        	$h = imagesy($JSimg);
		    	$flipped = imagecreatetruecolor($w, $h);
		    	for ($yi = 0; $yi < $h; $yi++) 
				{
	        		imagecopy($flipped, $JSimg, 0, $yi, 0, $h - $yi - 1, $w, 1);
	        	}
		    	$JSimg = $flipped;
		    	imagedestroy($flipped);
	
			}
	
		    if($flip == V) 
			{
		
		    	$w = imagesx($JSimg);
	        	$h = imagesy($JSimg);
	        	$flipped = imagecreatetruecolor($w, $h);
		 		for ($xi = 0; $xi < $w; $xi++) {
	        	imagecopy($flipped, $JSimg, $xi, 0, $w - $xi - 1, 0, 1, $h);
	        	}
		    	$JSimg = $flipped;
		    	imagedestroy($flipped);
			}
	
	
	
	/* Filters */
			if($greyscale) 
			{
					imagefilter($JSimg, IMG_FILTER_GRAYSCALE);
			}
			if($edgedetect) 
			{
				imagefilter($JSimg, IMG_FILTER_EDGEDETECT);
			}
			if($meanrm) 
			{
				imagefilter($JSimg, IMG_FILTER_MEAN_REMOVAL);
			}
	
			if($sepia) 
			{
				imagefilter($JSimg, IMG_FILTER_GRAYSCALE);
				imagefilter($JSimg, IMG_FILTER_COLORIZE, 100, 50, 0);
			}
			if($emboss)
			{
				imagefilter($JSimg, IMG_FILTER_EMBOSS);
			}
			if($negate)
			{
				imagefilter($JSimg, IMG_FILTER_NEGATE);
			}
			if($blur)
			{
				imagefilter($JSimg, IMG_FILTER_GAUSSIAN_BLUR);
			}
			if($bright)
			{
				imagefilter($JSimg, IMG_FILTER_BRIGHTNESS, $bright);
			}
			if($contrast)
			{
				imagefilter($JSimg, IMG_FILTER_CONTRAST, $contrast);
			}
			if($opacity)
			{
				$opacity = -$opacity;
				imagefilter($JSimg, IMG_FILTER_SMOOTH, $opacity);
			}
			/*Colorize*/
			if($r || $g || $b)
			{
				imagefilter($JSimg, IMG_FILTER_COLORIZE, $r, $g, $b);
			}
	
			/*CROP*/
			if($x || $y || $w || $h)
			{
				$JSan = imagecreatetruecolor($w, $h);
				imagecopyresampled($JSan, $JSimg, 0, 0, $x, $y, $w, $h, $w, $h);
				$JSimg = $JSan;
				imagedestroy($JSan);
	}
	/*Drop Shadow */
		if($drop) 
		{
			/*Thanks to www.partdigital.com */
				$width = imagesx($JSimg);
			$height =  imagesy($JSimg);
				 	 
			$tl = imagecreatefromgif("components/com_media/assets/images/shadow_TL.gif");
			$t  = imagecreatefromgif("components/com_media/assets/images/shadow_T.gif"); 
			$tr = imagecreatefromgif("components/com_media/assets/images/shadow_TR.gif"); 
			$r  = imagecreatefromgif("components/com_media/assets/images/shadow_R.gif"); 
			$br = imagecreatefromgif("components/com_media/assets/images/shadow_BR.gif"); 
			$b  = imagecreatefromgif("components/com_media/assets/images/shadow_B.gif"); 
			$bl = imagecreatefromgif("components/com_media/assets/images/shadow_BL.gif");
			$l  = imagecreatefromgif("components/com_media/assets/images/shadow_L.gif");
	
			
			$w = imagesx($l); 	
			$h = imagesy($l);	
		
			$newHeight = $height + (2*$w); 
			$newWidth  = $width + (2*$w);
		
			$new = imagecreatetruecolor($newWidth, $newHeight); 
		 
			imagecopyresized($new, $t,0,0,0,0,$newWidth,$w,$h,$w);			
			imagecopyresized($new, $l,0,0,0,0,$w,$newHeight,$w,$h);
			imagecopyresized($new, $b,0,$newHeight-$w,0,0,$newWidth,$w,$h, $w); 
			imagecopyresized($new, $r,$newWidth-$w,0,0,0,$w,$newHeight,$w,$h);
			 
			 
		
			$w = imagesx($tl); 
			$h = imagesy($tl); 
			imagecopyresized($new, $tl,0,0,0,0,$w,$h,$w,$h);  
			imagecopyresized($new, $bl,0,$newHeight-$h,0,0,$w,$h,$w,$h); 
			imagecopyresized($new, $br,$newWidth-$w,$newHeight-$h,0,0,$w,$h,$w,$h);
			imagecopyresized($new, $tr,$newWidth-$w,0,0,0,$w,$h,$w, $h);  
			 
		
			$w = imagesx($l); 
			imagecopyresampled($new, $JSimg, $w,$w,0,0,  imagesx($JSimg), imagesy($JSimg), imagesx($JSimg),imagesy($JSimg));
			 $JSimg = $new;
			 imagedestroy($new);
	
		}
	/* Rotation  */
		if($rotate) 
		{
			$JSimg = imagerotate($JSimg, $rotate, imagecolorallocate($JSimg, 255, 255, 255));
		}
	
	        if ($ext == 'jpg' || $ext == 'jpeg')
	        {          
	            imagejpeg($JSimg,$tmppath,90);
			} else
	    	if ($ext == 'png')
	        {                
	                imagepng($JSimg,$tmppath);                
	        } else
	        if ($ext == 'gif')
	        {                   
	                    imagegif($JSimg,$tmppath);
	        }
	        $mainframe->redirect('index.php?option=com_media&task=studio.display&view=studio&tmpl=component&folder=tmp&im[]=tmpimg.'.$ext );
	}

function undo()
    {
        global $mainframe;
        jimport('joomla.filesystem.path');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        JClientHelper::setCredentialsFromRequest('ftp');
		
		$paths = JRequest::getVar('im', 'array()', '', 'array');
        $folder = JRequest::getVar('folder', '', '', 'path');
          if (count($paths))
        {
            foreach ($paths as $path)
            {
				$image_path =$path;
                
            }
        }
        echo $folder;
        $ext = strtolower(end(explode('.', $image_path)));
         $unopath =JPATH::clean(JPATH_SITE . DS ."images".DS."tmp". DS . "uno." . $ext);
         $tmppath = JPATH::clean(JPATH_SITE . DS ."images".DS."tmp". DS . "tmpimg." . $ext);
        
         $i = substr_count($image_path,"tmpimg");
      if($i)
      {		copy($unopath,$image_path);
      $mainframe->redirect('index.php?option=com_media&task=studio.display&view=studio&tmpl=component&folder='.$folder.'&im[]=tmpimg.'.$ext );	
	}

        

}
function save()
    {
        global $mainframe;
        jimport('joomla.filesystem.path');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        JClientHelper::setCredentialsFromRequest('ftp');
		
		$paths = JRequest::getVar('im', 'array()', '', 'array');
        $folder = JRequest::getVar('folder', '', '', 'path');
        $savefolder = JRequest::getVar('savefolder', '', '', 'path');
        $savename = JRequest::getVar('savename', '', '', 'path');
  //      $exten = JRequest::getVar('savefor', '', '', 'path');
          $quality = JRequest::getVar('qulaity','90','POST');
          if (count($paths))
        {
            foreach ($paths as $path)
            {
				$image_path =$path;
                
            }
        }
         $ext = strtolower(end(explode('.', $image_path)));
         $savepath = JPATH::clean(JPATH_SITE . DS ."images". DS . $savefolder. DS . $savename .".". $ext);
         $tmppath = JPATH::clean(JPATH_SITE . DS ."images".DS."tmp". DS . "tmpimg." . $ext);
         if ($ext == 'jpg' || $ext == 'jpeg')
        {
            $JSimg = imagecreatefromjpeg($tmppath);
            imagejpeg($JSimg,$savepath,$quality);
		} else
            if ($ext == 'png')
            {
                $JSimg = imagecreatefrompng($tmppath);
                imagepng($JSimg,$savepath);
                
            } else
                if ($JSext == 'gif')
                {
                    $JSimg = imagecreatefromgif($tmppath);
                    imagegif($JSimg,$savepath);
                }
      
      {		
     	 $mainframe->redirect('index.php?option=com_media&task=studio.display&view=studio&tmpl=component&folder='.$folder.'&im[]=tmpimg.'.$ext );	
	}

        

}
function thumbnail()
    {
        global $mainframe;
        jimport('joomla.filesystem.path');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        JClientHelper::setCredentialsFromRequest('ftp');
		
		$paths = JRequest::getVar('im', 'array()', '', 'array');
        $folder = JRequest::getVar('folder', '', '', 'path');
        $name = JRequest::getVar('img', '', '', 'path');
        $savename = JRequest::getVar('savename', '', '', 'path');
          if (count($paths))
        {
            foreach ($paths as $path)
            {
				$image_path =$path;
                
            }
        }
        
         $ext = strtolower(end(explode('.', $image_path)));
        
         if($savename){
         	$save_path = JPATH::clean(JPATH_SITE . DS ."images". DS . $folder. DS . $savename .".". $ext);
         
         }
         else{
         	$save_path = (JPATH_SITE . DS ."images". DS . $folder. DS . "tn_".$name);
			
		}
         
         
       
        $max_width = 150;
        $max_height = 150;

        
        $ext = strtolower(end(explode('.', $image_path)));
        if ($ext == 'jpg' || $ext == 'jpeg')
        {
            $JSimg = imagecreatefromjpeg($image_path);
        } else
            if ($ext == 'png')
            {
                $JSimg = imagecreatefrompng($image_path);
                # Only if your version of GD includes GIF support
            } else
                if ($ext == 'gif')
                {
                    $JSimg = imagecreatefromgif($image_path);
                }
                    # If an image was successfully loaded, test the image for size
                  if ($JSimg)
                    {

                        # Get image size and scale ratio
                        $width = imagesx($JSimg);
                        $height = imagesy($JSimg);
                        $scale = max($max_width / $width, $max_height / $height);
                        # If the image is larger than the max shrink it
                        if ($scale <= 1)
                        {
                            $new_width = floor($scale * $width);
                            $new_height = floor($scale * $height);

                            # Create a new temporary image
                            $tmp_img = imagecreatetruecolor($new_width, $new_height);

                            # Copy and resize old image into new image
                            imagecopyresized($tmp_img, $JSimg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                            
                            $JSimg = $tmp_img;
                        }
                    }
       if ($ext == 'jpg' || $ext == 'jpeg')
        {
            imagejpeg($JSimg,$save_path,90);
        } else
            if ($ext == 'png')
            {
                imagepng($JSimg,$save_path);
                # Only if your version of GD includes GIF support
            } else
                if ($ext == 'gif')
                {
                    imagegif($JSimg,$save_path);
                }
		echo "Thumbnail " .$save_path ." created .        \n";
     jexit('Please close the modal box and reload Media Manager :)');      
                
         
}
function watermark()
    {
        global $mainframe;
        jimport('joomla.filesystem.path');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        JClientHelper::setCredentialsFromRequest('ftp');
		
		$paths = JRequest::getVar('im', 'array()', '', 'array');
        $folder = JRequest::getVar('folder', '', '', 'path');
        $name = JRequest::getVar('img', '', '', 'path');
        $savename = JRequest::getVar('savename1', '', '', 'path');
        $position = JRequest::getVar('position','','POST');
		$watermarkpath = JPATH::clean(JPATH_SITE . DS ."images". DS . "watermark.png");
        
          if (count($paths))
        {
            foreach ($paths as $path)
            {
				$image_path =$path;
                
            }
        }
        if(!is_file($watermarkpath))
        {
			echo "Watermark file not found ,please upload it.               ";
			jexit('Please close the modal box and reload Media Manager ');
			JError::raiseError(500, 'Invalid Watermark image');
		}
        
         $ext = strtolower(end(explode('.', $image_path)));
        
         if($savename){
         	$save_path = JPATH::clean(JPATH_SITE . DS ."images". DS . $folder. DS . $savename .".". $ext);
         
         }
         else{
         	$save_path = JPATH::clean(JPATH_SITE . DS ."images". DS . $folder. DS . "wm_".$name);
			
		}

        $watermark = imagecreatefrompng($watermarkpath);

        if ($ext == 'jpg' || $ext == 'jpeg')
        {
            $JSimg = imagecreatefromjpeg($image_path);
        } else
            if ($ext == 'png')
            {
                $JSimg = imagecreatefrompng($image_path);
                # Only if your version of GD includes GIF support
            } else
                if ($ext == 'gif')
                {
                    $JSimg = imagecreatefrompng($image_path);
                }
        $dst_w =imagesx($JSimg);
        $dst_h =imagesy($JSimg);
        $src_w =imagesy($watermark);
        $src_h =imagesy($watermark);
                
       	imagealphablending($JSimg,true);
    	imagealphablending($watermark,true);
	if($position == 11 || $position == 0){
		$position = rand(1,9);
	}
switch ($position) {

        case 1:
            imagecopy($JSimg, $watermark, ($dst_w-$src_w), 0, 0, 0, $src_w, $src_h);
            
        break;

        case 2:
            imagecopy($JSimg, $watermark, 0, 0, 0, 0, $src_w, $src_h);
        break;

        case 3:
            imagecopy($JSimg, $watermark, ($dst_w-$src_w), ($dst_h-$src_h), 0, 0, $src_w, $src_h);
        break;

        case 4:
            imagecopy($JSimg, $watermark, 0 , ($dst_h-$src_h), 0, 0, $src_w, $src_h);
        break;
        
        case 5:
            imagecopy($JSimg, $watermark, (($dst_w/2)-($src_w/2)), (($dst_h/2)-($src_h/2)), 0, 0, $src_w, $src_h);
        break;
        
        case 6:
            imagecopy($JSimg, $watermark, (($dst_w/2)-($src_w/2)), 0, 0, 0, $src_w, $src_h);
        break;
        
        case 7:
            imagecopy($JSimg, $watermark, (($dst_w/2)-($src_w/2)), ($dst_h-$src_h), 0, 0, $src_w, $src_h);
        break;
        
        case 8:
            imagecopy($JSimg, $watermark, 0, (($dst_h/2)-($src_h/2)), 0, 0, $src_w, $src_h);
        break;
        
        case 9:
            imagecopy($JSimg, $watermark, ($dst_w-$src_w), (($dst_h/2)-($src_h/2)), 0, 0, $src_w, $src_h);
        break;
    }
     if ($ext == 'jpg' || $ext == 'jpeg')
        {
            imagejpeg($JSimg,$save_path,90);
        } else
            if ($ext == 'png')
            {
                imagepng($JSimg,$save_path);
                # Only if your version of GD includes GIF support
            } else
                if ($ext == 'gif')
                {
                    imagegif($JSimg,$save_path);
                }
                echo "Watermark " .$save_path . "created.         \n";
				jexit('Please close the modal box and reload Media Manager :)');

}
        
        function rename()
    {
        global $mainframe;
        jimport('joomla.filesystem.path');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        JClientHelper::setCredentialsFromRequest('ftp');
		
		$paths = JRequest::getVar('im', 'array()', '', 'array');
        $folder = JRequest::getVar('folder', '', '', 'path');
        $name = JRequest::getVar('img', '', '', 'path');
        $rename = JRequest::getVar('rename', '', '', 'path');
        $msg = JRequest::getVar('msg', '', '', 'path');
 
       
          if (count($paths))
        {
            foreach ($paths as $path)
            {
				$image_path =$path;
                
            }
            $ext = strtolower(end(explode('.', $image_path)));
            
            
            $dst = JPATH::clean(JPATH_SITE . DS ."images". DS . $folder.DS.$rename.".".$ext);
            if(file_exists ($image_path)){
				rename($image_path,$dst);
			}else{
				jexit('Error! File not found,please reload the Media Manager and then try again');
				//JError::raiseError('File Not found');
			}
			echo "File" . $image_path ."renamed as" .$dst."\n";	
			jexit('Please close the modal box and reload Media Manager :)');	
        }
        }

}

