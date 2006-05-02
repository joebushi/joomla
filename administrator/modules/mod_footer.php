<?php
/**
* @version $Id: mod_footer.php,v 1.1 2005/08/25 14:17:46 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $tstart;

mosFS::load( 'includes/footer.php' );

?>
<div class="small" align="center">
<?php
// display page generation time
if ( !empty( $tstart ) ) {
	$tend 		= mosProfiler::getmicrotime();
	$totaltime 	= ($tend - @$tstart);
	printf ( $_LANG->_( 'Page was generated in' ) ." %f ". $_LANG->_( 'seconds' ), $totaltime );
}
?>
</div>