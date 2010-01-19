<?php
/**
 * @version		$Id: tree.php 6961 2007-03-15 16:06:53Z tcp $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Tree Node Class.
 *
 * @package 	Joomla.Framework
 * @subpackage	Base
 * @since		1.5
 */
class JNode extends JObject
{
	/**
	 * Parent node
	 */
	protected $_parent = null;

	/**
	 * Array of Children
	 */
	protected $_children = array();

	function __construct()
	{
		return true;
	}

	function addChild(&$node)
	{
		$node->setParent($this);
		$this->_children[] = & $node;
	}

	function &getParent()
	{
		return $this->_parent;
	}

	function setParent(&$node)
	{
		$this->_parent = & $node;
	}

	function hasChildren()
	{
		return count($this->_children);
	}

	function &getChildren()
	{
		return $this->_children;
	}
}
