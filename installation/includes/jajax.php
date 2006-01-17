<?php
/**
 * @version $Id: $
 * @package Joomla
 * @subpackage Installation
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

define( '_JEXEC', 1 );

define( 'JPATH_BASE', dirname( __FILE__ ) );

//Global definitions
define( 'DS', DIRECTORY_SEPARATOR );

//Joomla framework path definitions
$parts = explode( DS, JPATH_BASE );
array_pop( $parts );
array_pop( $parts );

define( 'JPATH_ROOT',			implode( DS, $parts ) );
define( 'JPATH_SITE',			JPATH_ROOT );
define( 'JPATH_CONFIGURATION',	JPATH_ROOT );
define( 'JPATH_LIBRARIES',		JPATH_ROOT . DS . 'libraries' );

// Require the library loader
require_once( JPATH_LIBRARIES . DS .'loader.php' );
// Require the xajax library
require_once ('xajax'.DS.'xajax.inc.php');
$xajax = new xajax();
$xajax->errorHandlerOn();

$xajax->registerFunction(array('getCollations', 'JAJAXHandler', 'dbcollate'));
$xajax->registerFunction(array('getFtpRoot', 'JAJAXHandler', 'ftproot'));

jimport( 'joomla.common.base.object' );
jimport( 'joomla.i18n.string' );
jimport( 'joomla.filesystem.*' );

/**
 * AJAX Task handler class
 * 
 * @static
 * @package Joomla
 * @subpackage Installer
 * @since 1.1
 */
class JAJAXHandler {
	
	/**
	 * Method to get the database collations
	 */
	function dbcollate($args) {

		jimport( 'joomla.error' );
		jimport( 'joomla.application.application' );
		jimport( 'joomla.database.database' );

		$objResponse = new xajaxResponse();
		$args = $args['vars'];

		/*
		 * Get a database connection instance
		 */		
		$database = & JDatabase :: getInstance($args['DBtype'], $args['DBhostname'], $args['DBuserName'], $args['DBpassword'] );

		if ($err = $database->getErrorNum()) {
			if ($err != 3) {
				$objResponse->addAlert('Database Connection Failed');
				return $objResponse;
			}
		}
		/*
		 * This needs to be rewritten for output to a javascript method... 
		 */
		$collations = array();

		// determine db version, utf support and available collations
		$vars['DBversion'] = $database->getVersion();
		$verParts = explode( '.', $vars['DBversion'] );
		$vars['DButfSupport'] = ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int) $verParts[2] >= 2));
		if ($vars['DButfSupport']) {
			$query = "SHOW COLLATION LIKE 'utf8%'";
			$database->setQuery( $query );
			$collations = $database->loadAssocList();
			// Tell javascript we have UTF support
			$objResponse->addAssign('utfsupport', 'value', '1');
		} else {
			// backward compatibility - utf-8 data in non-utf database
			// collation does not really have effect so default charset and collation is set
			$collations[0]['Collation'] = 'latin1';
			// Tell javascript we do not have UTF support
			$objResponse->addAssign('utfsupport', 'value', '0');
		}
		$txt = '<select id="vars_dbcollation" name="vars[DBcollation]" class="inputbox" size="1">';
		
		foreach ($collations as $collation) {
			$txt .= '<option value="'.$collation["Collation"].'">'.$collation["Collation"].'</option>';
		}
		$txt .=	'</select>';
		
		$objResponse->addAssign("theCollation","innerHTML",$txt);
		return $objResponse;
	}

	/**
	 * Method to get the path from the FTP root to the Joomla root directory
	 */
	function ftproot($args) {

		jimport( 'joomla.error' );
		jimport( 'joomla.application.application' );

		$objResponse = new xajaxResponse();
		$args = $args['vars'];
		require_once(JPATH_BASE.DS."classes.php");
		$root =  JInstallationHelper::findFtpRoot($args['ftpUser'], $args['ftpPassword'], $args['ftpHost']);
		$objResponse->addAssign('ftproot', 'value', $root);
		$objResponse->addAssign('rootPath', 'style.display', '');
		return $objResponse;
	}

}

/*
 * Process the AJAX requests
 */
$xajax->processRequests();
?>