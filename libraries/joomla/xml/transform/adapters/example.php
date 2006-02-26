<?php
/**
 * @version $Id: $
 * @package Joomla
 * @subpackage JFramework
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Ensure this file is included from a valid Joomla entry point
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('.joomla.jxml.transform.abstractadapter');
jimport('.joomla.jxml.utilities');

/**
 * Example adapter class to show basic functionality of the JXML Transformation library.
 *
 * @category XML Transformation
 * @package  Joomla
 * @subpackage JFramework
 * @since  1.1
 */
class JXMLTransformerAdapter_Example extends JXMLTransformerAdapter {
	
	/**
	 * This adapter handles the default (empty) namespace
	 */
	var $defaultNamespacePrefix = '&MAIN';
		
	function start_body($attributes) {
		return '<body>text';
	}

	function end_body($cdata) {
		return $cdata."</body>\n";
	}

	function start_bold($attributes) {
		return '<b>';
	}

	function end_bold($cdata) {
		return $cdata."</b>\n";
	}

	function start_boldbold($attributes) {
		return '<bold>';
	}

	function end_boldbold($cdata) {
		return $cdata."</bold>\n";
	}
}
?>