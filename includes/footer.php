<?php
/**
* @version $Id: footer.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $_VERSION;
?>
<div align="center">
	(C) <?php echo date( 'Y' ) . ' ' . $GLOBALS['mosConfig_sitename'];?>
</div>
<div align="center">
<?php echo $_VERSION->URL; ?>
</div>