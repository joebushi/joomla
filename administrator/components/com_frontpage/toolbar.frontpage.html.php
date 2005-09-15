<?php
/**
* @version $Id: toolbar.frontpage.html.php 55 2005-09-09 22:01:38Z eddieajau $
* @package Joomla
* @subpackage Content
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
* @subpackage Content
*/
class TOOLBAR_FrontPage {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::archiveList();
		mosMenuBar::spacer();
		mosMenuBar::custom('remove','delete.png','delete_f2.png','Remove', true);
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.frontpage' );
		mosMenuBar::endTable();
	}
}
?>