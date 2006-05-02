<?php
/**
* @version $Id: mosimage.php,v 1.5 2005/08/31 19:28:05 facedancer Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosImage' );

/**
*/
function botMosImage( $published, &$row, &$params, $page=0 ) {
	global $database;

	// check whether mosimage has been disabled for page
	if (!$published || !$params->get( 'image' )) {
	    $row->text = str_replace( '{mosimage}', '', $row->text );
	    
	    $regex = "/<img[^>]*>/i";
	    preg_match_all( $regex, $row->text, $regs );
	    $count = count($regs[0]);
	    for($i = 0; $i < $count; $i++) {
	    	$row->text = str_replace( $regs[0][$i], '', $row->text );
	    }
	    
	    return true;
	}

	
	// load mambot params info
	$query = "SELECT id"
	. "\n FROM #__mambots"
	. "\n WHERE element = 'mosimage'"
	. "\n AND folder = 'content'"
	;
	$database->setQuery( $query );
 	$id 	= $database->loadResult();
 	$mambot = new mosMambot( $database );
  	$mambot->load( $id );
 	$mparams =& new mosParameters( $mambot->params );	


	// Process <img>
	if($mparams->get('process_img')) {
		// expression to search for
		$regex = "/<img[^>]+>/i";
		
		// find all instances of <img> and put in $matches
		preg_match_all( $regex, $row->text, $matches );
		
		$count = count( $matches[0] );
		
		
		// mambot only processes if there are any instances of the <img> in the text
		if( $count ) {
			$images = processImg($matches[0], $mparams);
	
			$start = 0;
			// needed to stopping loading of images for the introtext, when it is set to hidden	
			if ( !$params->get( 'introtext' ) ) {
				// find all instances of <img> in intro text and put in $matches
				preg_match_all( $regex, $row->introtext, $matches_intro );
			 	// Number of <img's>
				$start 		= count( $matches_intro[0] );
			}		
			
			// store some vars in globals to access from the replacer
			$GLOBALS['botMosImageCount'] 	= $start;
			$GLOBALS['botMosImageParams'] 	=& $params;
			$GLOBALS['botMosImageArray'] 	=& $images;
		
			// perform the replacement
			$row->text = preg_replace_callback( $regex, 'botMosImage_replacer', $row->text );
			// clean up globals
			unset( $GLOBALS['botMosImageCount'] );
			unset( $GLOBALS['botMosImageMask'] );
			unset( $GLOBALS['botMosImageArray'] );		
		}
	}

	
	
	
	
	// Process {mosimage}		
		
	// expression to search for
	$regex = '/{mosimage\s*.*?}/i';		
		
	// find all instances of mambot and put in $matches
	preg_match_all( $regex, $row->text, $matches );
	
 	// Number of mambots
	$count = count( $matches[0] );

 	// mambot only processes if there are any instances of the mambot in the text
 	if ( $count ) {
	 	
	 	$mparams->def( 'padding' );
	 	$mparams->def( 'margin' );
	 	$mparams->def( 'link', 0 );

 		$images 	= processMOSImage( $row, $mparams );
		
		$start = 0;
		// needed to stopping loading of images for the introtext, when it is set to hidden		
		if ( !$params->get( 'introtext' ) ) {
			// find all instances of mambot in intro text and put in $matches
			preg_match_all( $regex, $row->introtext, $matches_intro );
		 	// Number of mambots
			$start 		= count( $matches_intro[0] );
		}
		
		// store some vars in globals to access from the replacer
		$GLOBALS['botMosImageCount'] 	= $start;
		$GLOBALS['botMosImageParams'] 	=& $params;
		$GLOBALS['botMosImageArray'] 	=& $images;
	
		// perform the replacement
		$row->text = preg_replace_callback( $regex, 'botMosImage_replacer', $row->text );
		// clean up globals
		unset( $GLOBALS['botMosImageCount'] );
		unset( $GLOBALS['botMosImageMask'] );
		unset( $GLOBALS['botMosImageArray'] );
			
		
	} 
	return true;
}


/**
* Process <img>
* TODOS:
* Do smthn with local/not local domains
*/
function processImg($images, &$mparams)
{	
	global $mosConfig_absolute_path, $mosConfig_live_site;

	$enable_thumbnailer_frontend = $mparams->get( 'enable_thumbnailer_frontend' );
	$quality = $mparams->get( 'jpg_quality_frontend' );
	$maxw = $mparams->get( 'max_image_width' );
	$maxh = $mparams->get( 'max_image_height' );
	$enforce_default_size = $mparams->get( 'enforce_default_size' );
	if($enforce_default_size == 1) {
		$targetw = $mparams->get( 'default_image_width' );
		$targeth = $mparams->get( 'default_image_height' );
	}	
	$process_img_this_domain = $mparams->get( 'process_img_this_domain');
	$process_img_other_domains = $mparams->get( 'process_img_other_domains');
	
	if(($process_img_other_domains != 1 && $process_img_this_domain != 1) || $enable_thumbnailer_frontend != 1)	
		return $images;

	$output_all_as_jpg = $mparams->get( 'output_all_as_jpg');	
	
	$regex = "/([a-z]+)=\"([^\"]+)\"/";
	
	for($i = 0; $i < count($images); $i++) {
		
		preg_match_all($regex, $images[$i], $attributes);
		
		unset($attribArray);
		
		for($j = 0; $j < count($attributes[0]); $j++) {
				$attribArray[strtolower($attributes[1][$j])] = $attributes[2][$j];
		}
		
		// check domain to know if mosimage should process img tag
		if(!($process_img_this_domain == 1 && $process_img_other_domains == 1)) {
			if($process_img_other_domains == 0) {
				if((substr($attribArray['src'], 0, 7) == "http://") && (substr($attribArray['src'], 0,  strlen($mosConfig_live_site)) != $mosConfig_live_site)) {
					continue;
				}
			} else {
				if(substr($attribArray['src'], 0, 7) != "http://" ||  (substr($attribArray['src'], 0,  strlen($mosConfig_live_site)) == $mosConfig_live_site)) {
					continue;
				}
			}
		}
		
		// check if file is valid & getimagesize exists
		if(isset($attribArray['src']) && @fopen($attribArray['src'], 'r') && function_exists( 'getimagesize' )) {
			
			$size = @getimagesize( $attribArray['src'] );
			if (is_array( $size )) {
				if($enforce_default_size == 1) { // if enforce_default_size is set both $targetw & $targeth would be set
					imageResize3($size[0], $size[1], $targetw, $targeth);			
				} else {
					// get img height & width from <img> tag if possible
					if(isset($attribArray['width'])) {
						$targetw = $attribArray['width'];
					} else {
						$targetw = $size[0];
					}
					
					if(isset($attribArray['height'])) {
						$targeth = $attribArray['height'];
					} else {
						$targeth = $size[1];
					}
					
					// resize image if it exceeds max width/height
					if(($targetw > $maxw || $targeth > $maxh)) {
						if( $targetw > $maxw ) {
							$targetw = $maxw;
						} else {
							$targetw = $size[0];
						}
						
						if( $targeth > $maxh ) {
							$targeth = $maxh;
						} else {
							$targeth = $size[1];	
						}
					}
					imageResize3($size[0], $size[1], $targetw, $targeth);
				}
				
				$attribArray['width'] = $size[0];
				$attribArray['height'] = $size[1];					
				
				$url = $mosConfig_live_site . '/includes/phpThumb/phpThumb.php?q='.$quality.'&aoe=1&w='.$size[0].'&h='.$size[1].'&src=';
				if(substr($attribArray['src'], 0, 7) == "http://")
					$url .= $attribArray['src'];
				else 
					$url .= $mosConfig_live_site.'/'.$attribArray['src'];
					
				if($output_all_as_jpg == 1) {
					$url .= '&f=jpeg';
				} else {										
					switch(substr(strrchr($attribArray['src'], '.'), 1))
					{
						case 'jpg' : 	$url .= '&f=jpeg';
										break;
						case 'jpeg' : 	$url .= '&f=jpeg';
										break;
						case 'png' : 	$url .= '&f=png';
										break;
						case 'gif' : 	$url .= '&f=gif';
										break;
						default: 		$url .= '&f=jpeg';
										break;																			
					}
				}
				$attribArray['src'] = $url;
				
				// change <img> attributes
				
				$img = '<img';
				foreach ($attribArray as $attrib => $value) {
					if($attrib == "style") {
						if(!($start = strpos($value, "width") === false)) {
							$end = strpos($value, ";", $start+1);
							$value = substr($value, 0, $start) . substr($value, $end);
						}
						
						if(!($start = strpos($value, "height") === false)) {
							$end = strpos($value, ";", $start+1);
							$value = substr($value, 0, $start) . substr($value, $end);
						}						
					}	
				    $img .= ' ' . $attrib . '="' . $value . '"';
				}
				$img .= ' / >';
				
				$images[$i] = $img;
			}				
		}				
	}	
		
	return $images;		
}

/**
* Process {mosimage}
* TODOS:
* change the code after 'if($output_all_as_jpg == 1)'
*/
function processMOSImage ( &$row, &$mparams ) {
	global $mosConfig_absolute_path, $mosConfig_live_site;
	
	$enable_thumbnailer_frontend = $mparams->get( 'enable_thumbnailer_frontend' );
	$quality = $mparams->get( 'jpg_quality_frontend' );
	$maxw = $mparams->get( 'max_image_width' );
	$maxh = $mparams->get( 'max_image_height' );
	$enforce_default_size = $mparams->get( 'enforce_default_size' );
	if($enforce_default_size == 1) {
		$targetw = $mparams->get( 'default_image_width' );
		$targeth = $mparams->get( 'default_image_height' );
	}

	$output_all_as_jpg = $mparams->get( 'output_all_as_jpg');
	
	$images 		= array();

	// split on \n the images fields into an array
	$row->images 	= explode( "\n", $row->images );

	$start = 0;
	$total = count( $row->images );
	
	for ( $i = $start; $i < $total; $i++ ) {
		$img = trim( $row->images[$i] );

		// split on pipe the attributes of the image
		if ( $img ) {
			
			$attrib = explode( '|', trim( $img ) );
			// $attrib[0] image name and path from /images/stories			
			
			// image size attibutes and thumbnailer enabling
			$size = '';
			$thumbnailer = '';
			if ( function_exists( 'getimagesize' ) ) {
				$size 	= @getimagesize( $mosConfig_absolute_path .'/images/stories/'. $attrib[0] );
				if (is_array( $size )) {
					
					/**
					* Process image if thumbnailer is enabled
					*/
					if($enable_thumbnailer_frontend == 1) {	
						if($enforce_default_size == 1) {
							imageResize3($size[0], $size[1], $targetw, $targeth);								
						} else if(($size[0] > $maxw || $size[1] > $maxh)) {
								
							if( $size[0] > $maxw ) {
								$targetw = $maxw;
							} else {
								$targetw = $size[0];
							}
							
							if( $size[1] > $maxh ) {
								$targeth = $maxh;
							} else {
								$targeth = $size[1];	
							}
							imageResize3($size[0], $size[1], $targetw, $targeth);
						}
					
						$thumbnailer = $mosConfig_live_site . '/includes/phpThumb/phpThumb.php?q='.$quality.'&w='.$size[0].'&h='.$size[1];
						if($output_all_as_jpg == 1) {
							$thumbnailer .= '&f=jpeg';
						} else {
							switch(substr(strrchr($attrib[0], '.'), 1))
							{
								case 'jpg' : 	$thumbnailer .= '&f=jpeg';
												break;
								case 'jpeg' : 	$thumbnailer .= '&f=jpeg';
												break;
								case 'png' : 	$thumbnailer .= '&f=png';
												break;
								case 'gif' : 	$thumbnailer .= '&f=gif';
												break;
								default: 		$thumbnailer .= '&f=jpeg';
												break;																		
							}
						}
						$thumbnailer .= '&src=';
					}
					
					$imgTagSize = ' width="'. $size[0] .'" height="'. $size[1] .'"';
				}
			}			
						
			// $attrib[1] alignment
			if ( !isset($attrib[1]) || !$attrib[1] ) {
				$attrib[1] = 'left';
			}
			
			// $attrib[2] alt & title
			if ( !isset($attrib[2]) || !$attrib[2] ) {
				$attrib[2] = 'Image';
			} else {
				$attrib[2] = htmlspecialchars( $attrib[2] );
			}
			
			// $attrib[3] border
			if ( !isset($attrib[3]) || !$attrib[3] ) {
				$attrib[3] = '0';
			}
			
			// $attrib[4] caption
			if ( !isset($attrib[4]) || !$attrib[4] ) {
				$attrib[4]	= '';
				$border 	= $attrib[3];
			} else {
				$border 	= 0;
			}
			
			// $attrib[5] caption position
			if ( !isset($attrib[5]) || !$attrib[5] ) {
				$attrib[5] = '';
			}
			
			// $attrib[6] caption alignment
			if ( !isset($attrib[6]) || !$attrib[6] ) {
				$attrib[6] = '';
			}
			
			// $attrib[7] width
			if ( !isset($attrib[7]) || !$attrib[7] ) {
				$attrib[7] 	= '';
				$width 		= '';
			} else {
				
				if(isset($size[0]) && ($size[0] > $attrib[7])) 
					$attrib[7] = $size[0] + 5;
					
				$width 		= ' width: '. $attrib[7] .'px';
			}
			
			// $attrib[8] link
			if ( !isset($attrib[8]) || !$attrib[8] ) {
				$attrib[8] 	= '';
				$link 		= '';
			} else {
				$link 		= $attrib[8];
				// adds 'http://' if none is set	
				if ( !strstr( $link, 'http' ) && !strstr( $link, 'https' ) ) {
					$link = 'http://'. $link;
				}
			}
			
			// $attrib[9] link target
			if ( !isset($attrib[9]) || !$attrib[9] ) {
				$attrib[9] 	= '';
				$target 	= '_blank';
			} else {
				$target 	= $attrib[9];
			}
			
			// assemble the <image> tag
			$image = '';
			if ( $link ) {
				// link
				$image .= '<a href="'. $link .'" target="'. $target .'" style="display: block;">';	
			}
				
			$image .= '<img src="' . $thumbnailer . $mosConfig_live_site .'/images/stories/'. $attrib[0] .'" '. $imgTagSize;
			// no aligment variable - if caption detected
			if ( !$attrib[4] ) {
				$image .= $attrib[1] ? ' align="'. $attrib[1] .'"' : '';
			}
			$image .=' hspace="6" alt="'. $attrib[2] .'" title="'. $attrib[2] .'" border="'. $border .'" />';
			if ( $link ) {
				// link
				$image .= '</a>';	
			}
						
			// assemble caption - if caption detected
			if ( $attrib[4] ) {
				$caption = '<div class="mosimage_caption" style="width: '. $width .'; text-align: '. $attrib[6] .';" align="'. $attrib[6] .'">';
				if ( $link ) {
					// link
					$caption .= '<a href="'. $link .'" target="'. $target .'" style="display: block;">';	
				}			
				$caption .= $attrib[4];
				if ( $link ) {
					// link
					$caption .= '</a>';	
				}
				$caption .='</div>';
			}		
			
			// final output
			$img = '';
			if ( $attrib[4] ) {
				// surrounding div
				$img .= '<div class="mosimage" style="border-width: '. $attrib[3] .'px; float: '. $attrib[1] .'; margin: '. $mparams->def( 'margin' ) .'px; padding: '. $mparams->def( 'padding' ) .'px;'. $width .'" align="center">';
				
				// display caption in top position
				if ( $attrib[5] == 'top' ) {
					$img .= $caption;
				}
				
				$img .= $image;				
				
				// display caption in bottom position
				if ( $attrib[5] == 'bottom' ) {
					$img .= $caption;
				}
				$img .='</div>';
								
			} else {
				$img = $image;
			}

			
			$images[] = $img;
		}
	}
	
	return $images;
}

/**
* Replaces the matched tags an image
* @param array An array of matches (see preg_match_all)
* @return string
*/
function botMosImage_replacer( &$matches ) {
	$i = $GLOBALS['botMosImageCount']++;
	
	return @$GLOBALS['botMosImageArray'][$i];
}

/*
	Two functions below should be placed in mambo lib maybe, I use 'em in com_media and mosimage
*/

/**
* takes the larger size of the width and height and applies the
* formula accordingly...this is so this script will work
* dynamically with any size image
* @param width
* @param height
* @param square side that image should fit into
* @return image width and height in url query-like format
*/
function imageResize( $width, $height, $target ) {
	if ( $width > $target || $height > $target ) {
		if ( $width > $height ) {
			$percentage = ( $target / $width );
		} else {
			$percentage = ( $target / $height );
		}

		//gets the new value and applies the percentage, then rounds the value
		$width 	= round( $width * $percentage );
		$height = round( $height * $percentage );
	}

	return 'width="'. $width .'" height="'. $height .'"';
}

/**
* takes the larger size of the width and height and applies the
* formula accordingly...this is so this script will work
* dynamically with any size image
* Modifies given parameters by reference
* @param width
* @param height
* @param square side that image should fit into
*/
function imageResize2( &$width, &$height, $target ) {
	if ( $width > $target || $height > $target ) {
		if ( $width > $height ) {
			$percentage = ( $target / $width );
		} else {
			$percentage = ( $target / $height );
		}

		//gets the new value and applies the percentage, then rounds the value
		$width 	= round( $width * $percentage );
		$height = round( $height * $percentage );
	}
}

/**
* This function is for best fit image into defined rectangle
*
* Modifies given parameters by reference
* @param width
* @param height
* @param target width
* @param target height
*/
function imageResize3( &$width, &$height, $targetw, $targeth ) {	
	if($width != $targetw && $height != $targeth) {
		$percentage_x = $targetw / $width;
		$percentage_y = $targeth / $height;
		
		if($percentage_x > $percentage_y) {
			$percentage = $percentage_y;
		} else {
			$percentage = $percentage_x;
		}
	
		$width	= round($width * $percentage);
		$height = round($height * $percentage);		
	}
}


?>