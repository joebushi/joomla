<?php
/**
Author : Nakul Ganesh S
Joomla GSoC 2008
Mentor : Deborah Susan Clarkson
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Media Component List Model
 *
 * @package		Joomla
 * @subpackage	Media
 * @since 1.5
 */
class MediaModelStudio extends JModel
{
	
		function getState($property = null)
	{
		static $set;

		if (!$set) {
		$folder = JRequest::getVar( 'folder', '', '', 'path' );
		$this->setState('folder', $folder);
		$paths = JRequest::getVar('im', 'array()', '', 'array');
		$this->setState('paths', $paths);
		//Creating a temporary folder called tmp under the images folder
			$tmpfolpath = JPath::clean(JPATH_SITE . DS .'images'. DS . "tmp");
			if (!is_dir($path) && !is_file($path))
			{
				jimport('joomla.filesystem.*');
				JFolder::create($tmpfolpath);
				JFile::write($tmpfolpath.DS."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
			}
		if (count($paths))
        {
            foreach ($paths as $path)
            {
                $image = $path;
            }
        }
		$this->setState('image', $image);  
		//"image" returns the name of the selected image
		
		$image_path =JURI::root().(  'images/'. $folder. '/' . $image);  
		$this->setState('url', $image_path);
		//"url" gives the full url of the selected image           

    if(count($paths))
        {
            foreach ($paths as $path)
            {
                $image_path =JPATH::clean( JPATH_SITE . DS .'images'. DS . $folder. DS . $path);
            }
        }
		$this->setState('path', $image_path);     
		//"path" gives the full path of the selected image
		if($image_path)
		{		
							{
							$info = @getimagesize($image_path);
							$width		= @$info[0];
							$height	    = @$info[1];
							$type		= @$info[2];
							$mime		= @$info['mime'];
							}
							
							if (($info[0] > 600) || ($info[1] > 600)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 600);
								$width_600 = $dimensions[0];
								$height_600 = $dimensions[1];
							} else {
								$width_600 = $width;
								$height_600 = $height;
							}
		}
		$this->setState('width_600', $width_600);
		$this->setState('height_600', $height_600);       				
		$watermark=JPATH::clean( JPATH_SITE . DS .'images'.DS.'watermark.png'); 
		if(is_file ($watermark)){
			$this->setState('watermark', "Watermark image found !");
		}    
		else
		{
			$this->setState('watermark',"Watermark image not found!");
		}
		$set = true;
		return parent::getState($property);
	}
}
}
