<?php
/**
* @version $Id: banners.html.php,v 1.1 2005/08/25 14:18:09 johanjanssens Exp $
* @package Mambo
* @subpackage Banners
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Banner
 */
class bannerScreens_front {
	/**
	 * @param string The main template file to include for output
	 * @param array An array of other standard files to include
	 * @return patTemplate A template object
	 */
	function &createTemplate( $bodyHtml='', $files=null ) {
		$tmpl =& mosFactory::getPatTemplate( $files );

		$directory = mosComponentDirectory( $bodyHtml, dirname( __FILE__ ) );
		$tmpl->setRoot( $directory );

		if ( $bodyHtml ) {
			$tmpl->setAttribute( 'body', 'src', $bodyHtml );
		}

		return $tmpl;
	}

	function item( &$banner ) {
		global $mosConfig_live_site;
		global $_LANG;

		$tmpl =& bannerScreens_front::createTemplate( 'item.html' );

		$imageurl 	= $mosConfig_live_site .'/images/banners/'. $banner->imageurl;
		$href 		= sefRelToAbs( 'index.php?option=com_banners&amp;task=click&amp;bid='. $banner->bid );
		if ( !$banner->editor ) {
			$alt = $_LANG->_( 'Advertisement' );
		} else {
			$alt = $banner->editor;
		}

		if ( trim( $banner->custombannercode ) ) {
			$tmpl->addVar( 'mod_banner', 'banner', $banner->custombannercode );

			$tmpl->addVar( 'mod_banner', 'type', 	1 );
		} else {
			$tmpl->addVar( 'mod_banner', 'href', 	$href );
			$tmpl->addVar( 'mod_banner', 'image', $imageurl );
			$tmpl->addVar( 'mod_banner', 'alt', $alt );

			if ( eregi( "(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$", $banner->imageurl ) ) {
				$tmpl->addVar( 'mod_banner', 'type', 	2 );
			} else if ( eregi( "\.swf$", $banner->imageurl ) ) {
				$tmpl->addVar( 'mod_banner', 'type', 	3 );
			}
		}

		$tmpl->displayParsedTemplate( 'mod_banner' );
	}
}
?>