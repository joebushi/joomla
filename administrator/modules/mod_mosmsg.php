<?php
/**
* @version $Id: mod_mosmsg.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$mosmsg = trim( strip_tags( mosGetParam( $_REQUEST, 'mosmsg', '' ) ) );

if ( $mosmsg ) {
	if ( !get_magic_quotes_gpc() ) {
		$mosmsg = addslashes( $mosmsg );
	} else {
		$mosmsg = stripslashes( $mosmsg );
	}
	
	?>
	<div class="message">
	<?php echo $mosmsg; ?>
	</div>
	<?php
}
?>