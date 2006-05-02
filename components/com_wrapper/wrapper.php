<?php
/**
* @version $Id: wrapper.php,v 1.1 2005/08/25 14:18:16 johanjanssens Exp $
* @package Mambo
* @subpackage Wrapper
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

showWrap( $option );

function showWrap( $option ) {
	global $database, $Itemid, $mainframe;

	$menu =& new mosMenu( $database );
	$menu->load( $Itemid );
	$params =& new mosParameters( $menu->params );
	$params->def( 'back_button', 	$mainframe->getCfg( 'back_button' ) );
	$params->def( 'scrolling', 		'auto' );
	$params->def( 'page_title', 	1 );
	$params->def( 'pageclass_sfx', 	'' );
	$params->def( 'header', 		$menu->name );
	$params->def( 'height', 		500 );
	$params->def( 'height_auto', 	1 );
	$params->def( 'width', 			'100%' );
	$params->def( 'add', 			1 );
	$params->def( 'xhtml', 			0 );
	$params->def( 'target', 		'wrapper' );
	$params->def( 'meta_key', 		'' );
	$params->def( 'meta_descrip', 	'' );
	$params->def( 'seo_title', 		$menu->name );
	
	$url = $params->def( 'url', '' );

	if ( $params->get( 'add' ) ) {
		// adds 'http://' if none is set	
		 if ( substr( $url, 0, 1 ) == '/' ) {
			// relative url in component. use server http_host.
			$row->url = 'http://'. $_SERVER['HTTP_HOST'] . $url;
		} elseif ( !strstr( $url, 'http' ) && !strstr( $url, 'https' ) ) {
			$row->url = 'http://'. $url;
		} else {
			$row->url = $url;
		}
	} else {
		$row->url = $url;
	}

	$row->loadx = '';
	$row->load 	= '';
	// auto height control
	if ( $params->def( 'height_auto' ) ) {
		if ( $params->def( 'xhtml' ) ) {
			$row->loadx = 'window.onload = iFrameHeight;';
		} else {
			$row->load = 'onload="iFrameHeight()"';
		}
	}

	// SEO Meta Tags
	$mainframe->setPageMeta( $params->get( 'seo_title' ), $params->get( 'meta_key' ), $params->get( 'meta_descrip' ) );

	mosFS::load( '@front_html' );

	wrapperScreens_front::view( $row, $params );
}
?>