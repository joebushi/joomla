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

/**
 * Callback Registry.
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
class JXMLTransformerCallbackRegistry extends JObject {

	/**
	 * @var    array
	 * @access public
	 */
	var $overloadedNamespaces = array ();

	/**
	 * @var    boolean
	 * @access private
	 */
	var $_locked = FALSE;

	/**
	 * If TRUE, the transformation will continue recursively
	 * until the XML contains no more overloaded elements.
	 * Can be overrided on a per-element basis.
	 *
	 * @var    boolean
	 * @access private
	 */
	var $_recursiveOperation = TRUE;

	/**
	 * Constructor.
	 *
	 * @param  boolean
	 * @access public
	 */
	function __construct($recursiveOperation) {
		$this->_recursiveOperation = $recursiveOperation;
	}

	/**
	 * Overloads an XML Adapter.
	 *
	 * @param  string
	 * @param  object
	 * @param  boolean
	 * @return mixed
	 * @access public
	 */
	function overloadNamespace($NamespacePrefix, & $object, $recursiveOperation = '') {
		if (!is_object($object)) {
			return sprintf('Cannot overload Namespace "%s", '.'second parameter is not an object.', $NamespacePrefix);
		}

		if (!is_subclass_of($object, 'JXMLTransformerAdapter')) {
			return sprintf('Cannot overload Namespace "%s", '.'provided object was not instantiated from '.'a class that inherits JXMLTransformerAdapter.', $NamespacePrefix);
		}

		if (!method_exists($object, 'startElement') || !method_exists($object, 'endElement')) {
			return sprintf('Cannot overload Namespace "%s", '.'method(s) "startElement" and/or "endElement" '.'are missing on given object.', $NamespacePrefix);
		}

		$this->overloadedNamespaces[$NamespacePrefix]['active'] = true;
		$this->overloadedNamespaces[$NamespacePrefix]['object'] = & $object;
		$this->overloadedNamespaces[$NamespacePrefix]['recursiveOperation'] = is_bool($recursiveOperation) ? $recursiveOperation : $this->_recursiveOperation;

		return true;
	}

	/**
	 * Reverts overloading of a given XML Adapter.
	 *
	 * @param  string
	 * @access public
	 */
	function unOverloadNamespace($NamespacePrefix) {
		if (isset ($this->overloadedNamespaces[$NamespacePrefix])) {
			unset ($this->overloadedNamespaces[$NamespacePrefix]);
		}
	}

	/**
	 * Returns TRUE if a given Adapter is overloaded,
	 * FALSE otherwise.
	 *
	 * @param  string
	 * @return boolean
	 * @access public
	 */
	function isOverloadedNamespace($NamespacePrefix) {
		return isset ($this->overloadedAdapters[$NamespacePrefix]);
	}

	/**
	 * Enables or disables the recursive operation.
	 *
	 * @param  boolean
	 * @access public
	 */
	function setRecursiveOperation($recursiveOperation) {
		if (is_bool($recursiveOperation)) {
			$this->_recursiveOperation = $recursiveOperation;
		}
	}

	/**
	 * Lock all Adapter handlers except a given one.
	 *
	 * @string Adapter
	 * @return boolean
	 * @access public
	 * @see    releaseLock()
	 */
	function getLock($Namespace) {
		if (!$this->_locked) {
			$NamespacePrefixes = array_keys($this->overloadedNamespaces);

			foreach ($NamespacePrefixes as $NamespacePrefix) {
				if ($NamespacePrefix != $Namespace) {
					unset ($this->overloadedNamespaces[$NamespacePrefix]['active']);
				}
			}

			$this->_locked = TRUE;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Releases a lock.
	 *
	 * @access public
	 * @see    getLock()
	 */
	function releaseLock() {
		$NamespacePrefixes = array_keys($this->overloadedNamespaces);

		foreach ($NamespacePrefixes as $NamespacePrefix) {
			$this->overloadedNamespaces[$NamespacePrefix]['active'] = TRUE;
		}

		$this->_locked = FALSE;
	}
}
?>