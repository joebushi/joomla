<?php
/**
* @version $Id: mod_random_image.php,v 1.1 2005/08/25 14:23:45 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class modRandomImageData {

	function &getRows( &$params ){
		global $mosConfig_absolute_path, $mosConfig_live_site, $_LANG;

		$type 	= $params->get( 'type', 'jpg' );
		$folder = $params->get( 'folder' );
		$link 	= $params->get( 'link' );
		$width 	= $params->get( 'width' );
		$height = $params->get( 'height' );

		$abspath_folder = $mosConfig_absolute_path .'/'. $folder;
		$the_array = array();
		$the_image = array();

		if ( is_dir( $abspath_folder ) ) {
			if ( $handle = opendir( $abspath_folder ) ) {
				while (false !== ( $file = readdir($handle ) ) ) {
					if ( $file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html' ) {
						$the_array[] = $file;
					}
				}
			}
			closedir( $handle );

			foreach ( $the_array as $img ) {
				if ( !is_dir( $abspath_folder .'/'. $img ) ) {
					if ( eregi( $type, $img ) ) {
						$the_image[] = $img;
					}
				}
			}

			if ( !$the_image ) {
				echo $_LANG->_( 'No images' );
				exit;
			} else {
			  	$i 			= count( $the_image );
			  	$random 	= mt_rand(0, $i - 1);
			  	$image_name = $the_image[$random];

			  	$i 			= $abspath_folder . '/'. $image_name;
			  	$size 		= getimagesize ($i);

			  	// Accepting empty width; adjusting emtpy height when we have a width
			  	if( $width == '' && $height != '' ) {
			  		$coeff 	= $size[0]/$size[1];
			  		$width = (int) ($height*$coeff);
			  	} else if ( $height == '' && $width != '' ) {
			  		$coeff 	= $size[0]/$size[1];
			  		$height = (int) ($width/$coeff);
			  	}

			  	$image 		= $mosConfig_live_site .'/'. $folder .'/'. $image_name;
			}

		  	$row->link  		= ( $link ? 1 : 0 );
		  	$row->url  			= $link;
		  	$row->image 		= $image;
		  	$row->width 		= $width != '' ? 'width="' .$width. '"' : '';
		  	$row->height 		= $height != '' ? 'height="' .$height. '"' : '';;
		  	$row->image_name 	= $image_name;
		  	$row->target	 	= $params->get( 'target', '_SELF' );
		}

		return $row;
	}
}


class modRandomImage {

	function show ( &$params ) {
		$cache  = mosFactory::getCache( "mod_randomimage" );

		$cache->setCaching($params->get('cache', 1));
		$cache->setLifeTime($params->get('cache_time', 900));
		$cache->setCacheValidation(true);

		$cache->callId( "modRandomImage::_display", array( $params ), "mod_randomimage");
	}

	function _display( &$params ) {

		$row = modRandomImageData::getRows( $params );

		$tmpl =& moduleScreens::createTemplate( 'mod_random_image.html' );

		$tmpl->addVar( 'mod_random_image', 'class', $params->get( 'moduleclass_sfx' ) );

		$tmpl->addObject( 'mod_random_image', $row, 'row_' );

		$tmpl->displayParsedTemplate( 'mod_random_image' );
	}
}

modRandomImage::show( $params );
?>