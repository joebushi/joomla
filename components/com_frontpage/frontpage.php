<?php
/**
* @version $Id: frontpage.php,v 1.1 2005/08/25 14:18:10 johanjanssens Exp $
* @package Mambo
* @subpackage Content
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// load the content language file
$_LANG->load( 'com_content', 0 );

// definition that we have to run the frontpage
if( $task == '' ) {
	$task = 'frontpage';
}
// code handling has been shifted into content.php
require_once( $mosConfig_absolute_path .'/components/com_content/content.php' );
?>