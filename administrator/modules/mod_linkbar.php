<?php
/**
* @version $Id: mod_linkbar.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Base class for Linkbar
 * @package Mambo
 */
class mosLinkBar extends mosAbstractTasker {
	/** @var array An array of data for the links */
	var $_links=null;

	/**
	 * Adds a link
	 * @param string The text to display
	 * @param string The href for the link
	 * @param string The title attribute for the link
	 */
 	function addLink( $text, $href='', $title='' ) {
  		$this->_links[] = array(
  			'text' => $text,
 			'href' => $href,
 			'title' => $title
  			);
	}

	/**
	 * Displays the Linkbar
	 */
	function display() {
 		if (is_array( $this->_links ) && count( $this->_links ) > 0) {
 			?>
 			<ul id="linkbar">
 			<?php
  			foreach ($this->_links as $i => $link) {
 				// title attrib for a tag uses text param value unless a title param value exists
 				$title = $link['text'];
 				if ( $link['title'] ) {
 					$title = $link['title'];
 				}
 				?>
 				<li>
 				<a id="a<?php echo $i; ?>" href="<?php echo  $link['href']; ?>" title="<?php echo $title; ?>">
 				<?php echo $link['text']; ?></a>
 				</li>
 				<?php
  			}
 			?>
 			</ul>
 			<?php
  		}
  	}
}

// include the linkbar file if available
if ( $path = $mainframe->getPath( 'linkbar' ) ) {
	include_once( $path );
}
?>