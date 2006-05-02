<?php
/**
 * @version $Id: mambo.feed.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
 * @package Mambo
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

mosFS::load( 'includes/feedcreator.class.php' );

/**
 * Cleans text of all formating and scripting code
 * @param string
 * @return string
 */
function rssCleanText ( $text ) {
	$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
	$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
	$text = preg_replace( '/<!--.+?-->/', '', $text );
	$text = preg_replace( '/{.+?}/', '', $text );
	$text = preg_replace( '/&nbsp;/', ' ', $text );
	$text = preg_replace( '/&amp;/', ' ', $text );
	$text = preg_replace( '/&quot;/', ' ', $text );
	$text = strip_tags( $text );
	$text = htmlspecialchars( $text );
	return $text;
}

/**
 * Needed to fix a known limitation in Feedcreator which hardcodes the encoding
 * @package Mambo
 */
class MamboFeedCreator extends UniversalFeedCreator {
	/**
	 * @param string RSS output format
	 * @param string Cache file name
	 * @param boolean
	 * @param string Character encoding for the contents of the feed
	 */
	function saveFeed( $format='RSS0.91', $filename='', $displayContents=true, $encoding='iso-8859-15' ) {
		// feed format
		$this->_setFormat( $format );
		// feed encoding
		$this->_feed->encoding = $encoding;

		$this->_feed->saveFeed( $filename, $displayContents );
	}
}
?>