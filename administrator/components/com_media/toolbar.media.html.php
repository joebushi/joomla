<?php
/**
* @version $Id: toolbar.media.html.php 180 2005-09-13 13:07:46Z stingrey $
* @package Joomla
* @subpackage Massmail
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Massmail
*/
class TOOLBAR_media {
	/**
	* Draws the menu for a New Media
	*/

	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::custom('upload','upload.png','upload_f2.png','Upload',false);
		mosMenuBar::spacer();
		mosMenuBar::custom('newdir','new.png','new_f2.png','Create' ,false);
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.mediamanager' );
		mosMenuBar::endTable();
	}
}
?>