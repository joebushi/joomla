<?php
/**
* @version $Id: mod_logoutbutton.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $my, $acl;
global $_LANG;

$link_user 		= '#';
$link_logout	= '#';
$title_user		= $_LANG->_( 'Logged in User' );
$title_logout	= '';

if ( !$mainframe->get('disableMenu', false) ) {
	if ( $acl->acl_check( 'com_users', 'manage', 'users', $my->usertype ) ) {
		$link_user 	= 'index2.php?option=com_users&amp;task=editA&amp;id='. $my->id;
		$title_user	= $_LANG->_( 'Edit User Information' );		
	}
	$link_logout	= 'index2.php?option=logout';
	$title_logout	= $_LANG->_( 'Logout' );
}	
?>
<span style="padding-left: 15px;">
<?php echo $_LANG->_( 'User' ); ?>:
</span>
<strong>
<a href="<?php echo $link_user; ?>" title="<?php echo $title_user; ?>" style="text-decoration: none;">
<?php echo $my->username;?></a>
</strong>
<a href="<?php echo $link_logout; ?>" class="logoutButton" title="<?php echo $title_logout; ?>">
<?php echo $_LANG->_( 'Logout' ); ?></a>