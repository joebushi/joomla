<?php
/**
* @version $Id: mod_unread.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$query = "SELECT COUNT(*)"
. "\n FROM #__messages"
. "\n WHERE state = '0'"
. "\n AND user_id_to = '$my->id'"
;
$database->setQuery( $query );
$unread = $database->loadResult();

$txt 	= $_LANG->_( 'Administration Messages' );
$link	= 'index2.php?option=com_messages';

if ( $unread ) {
	$style = 'color: red; text-decoration: none;  font-weight: bold';
	$image = 'images/mail.png';
} else {
	$style = 'color: black; text-decoration: none;';
	$image = 'images/nomail.png';
}

if ( $mainframe->get('disableMenu', false) ) {
	$link = '#';
}
?>
<a href="<?php echo $link; ?>" style="<?php echo $style; ?>">
<?php echo $unread; ?>
<img src="<?php echo $image; ?>" align="middle" border="0" alt="<?php echo $txt; ?>" title="<?php echo $txt; ?>" />
</a>