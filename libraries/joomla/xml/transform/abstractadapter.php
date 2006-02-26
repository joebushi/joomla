<?php
/**
 * @version $Id: $
 * @package Joomla
 * @subpackage Libraries
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

jimport('.joomla.jxml.utilities');

/**
 * Convenience Base Class for JXML Transformation Adapters.
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
class JXMLTransformerAdapter extends JObject {

	/**
	 * @var    string
	 * @access public
	 */
	var $defaultNamespacePrefix = '';

	/**
	 * @var    boolean
	 * @access public
	 */
	var $secondPassRequired = FALSE;

	/**
	 * @var    array
	 * @access private
	 */
	var $_prefix = array ();

	/**
	 * @var    string
	 * @access private
	 */
	var $_transformer = '';

	/**
	 * Called by JXMLTransformer at initialization time.
	 * We use this to remember our Adapter prefixes
	 * (there can be multiple) and a pointer to the
	 * Transformer object.
	 *
	 * @param  string
	 * @param  object
	 * @access public
	 */
	function initObserver($prefix, & $object) {
		$this->_prefix[] = $prefix;
		$this->_transformer = $object;
	}

	/**
	 * Wrapper for startElement handler.
	 *
	 * @param  string
	 * @param  array
	 * @return string
	 * @access public
	 */
	function startElement($element, $attributes) {
		$do = 'start_'.$element;

		if (method_exists($this, $do)) {
			return $this-> $do ($attributes);
		}

		return sprintf("<%s%s>", $element, JXMLUtilities :: attributesToString($attributes));
	}

	/**
	 * Wrapper for endElement handler.
	 *
	 * @param  string
	 * @param  string
	 * @return array
	 * @access public
	 */
	function endElement($element, $cdata) {
		$do = 'end_'.$element;

		if (method_exists($this, $do)) {
			return $this-> $do ($cdata);
		}

		return array (sprintf('%s</%s>', $cdata, $element), FALSE);
	}

	/**
	 * Lock all other Namespace handlers.
	 *
	 * @return boolean
	 * @access public
	 * @see    releaseLock()
	 */
	function getLock() {
		return $this->_transformer->_callbackRegistry->getLock($this->_prefix[0]);
	}

	/**
	 * Releases a lock.
	 *
	 * @access public
	 * @see    getLock()
	 */
	function releaseLock() {
		$this->_transformer->_callbackRegistry->releaseLock();
	}
}
?>