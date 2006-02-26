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

jimport('joomla.jxml.transform.transform');

/**
 * JXML Transformation driver that uses PHP's output buffering mechanism 
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
class JXMLTransformer_OutputBuffer extends JXMLTransformer {

	/**
	 * @var    boolean
	 * @access private
	 */
	var $_started = FALSE;

	/**
	 * Constructor.
	 *
	 * @param  array
	 * @access public
	 */
	function __construct($parameters = array ()) {
		parent::__construct($parameters);

		if (!empty ($this->_callbackRegistry->overloadedNamespaces)) {
			$this->start();
		}
	}

	/**
	 * Starts the output-buffering,
	 * and thus the transformation.
	 *
	 * @access public
	 */
	function start() {
		if (!$this->_started) {
			ob_start(array ($this, 'transform'));

			$this->_started = TRUE;

			if ($this->_checkDebug()) {
				$this->sendMessage('start: '.serialize($this));
			}
		}
	}
}
?>