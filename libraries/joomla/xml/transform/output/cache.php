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

jimport('cache.Lite');
jimport('joomla.jxml.transform.transform');

/**
 * JXML Transformation driver for caching the transformed XML. 
 *
 *
 * This class draws heavily on work by Sebastian Bergmann <sb@sebastian-bergmann.de> 
 * and Kristian Köhntopp <kris@koehntopp.de> and is based on the PEAR XML_Transformer 
 * package
 *
 * @category XML Transformation
 * @package  Joomla
 * @subpackage JFramework
 * @since  1.1
 */
class JXMLTransformer_Cache extends JXMLTransformer {

	/**
	 * @var    object
	 * @access private
	 */
	var $_cache = FALSE;

	/**
	 * Constructor.
	 *
	 * @param  array
	 * @access public
	 */
	function __construct($parameters = array ()) {
		parent :: __($parameters);
		$this->_cache = new Cache_Lite($parameters);
	}

	/**
	 * Cached transformation a given XML string using
	 * the registered PHP callbacks for overloaded tags.
	 *
	 * @param  string
	 * @param  string
	 * @return string
	 * @access public
	 */
	function transform($xml, $cacheID = '') {
		$cacheID = ($cacheID != '') ? $cacheID : md5($xml);

		$cachedResult = $this->_cache->get($cacheID, 'JXMLTransformer');

		if ($cachedResult !== FALSE) {
			return $cachedResult;
		}

		$result = parent :: transform($xml);
		$this->_cache->save($result, $cacheID, 'JXMLTransformer');

		return $result;
	}
}
?>