<?php
/**
* @version $Id: update.buildcache.php,v 1.1 2005/08/28 15:45:01 pasamio Exp $
* @package Mambo Update
* @copyright (C) 2005 Samuel Moffatt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

include($mosConfig_absolute_path . "/includes/patTemplate/patError.php");
$_MAMBOTS->registerFunction( 'onAfterInstall', 'botRebuildCache');
$_MAMBOTS->registerFunction( 'onAfterUninstall', 'botRebuildCache');


/**
 * Approves or denies an installed based on dependency check
 *
 * This function checks the installed elements table to check that the dependencies have bee resolved
 */
function botRebuildCache() {
	mosFS::load('#mambo.update');
	update_client_common::buildCache(1);
} 