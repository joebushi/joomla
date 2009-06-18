<?php
/**
 * @version		$Id: assetgroups.php 12099 2009-06-16 11:14:29Z hackwar $
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

require_once dirname(__FILE__).DS.'list.php';

/**
 * Renders a select list of Asset Groups
 *
 * @package 	Joomla.Framework
 * @subpackage	Parameter
 * @since		1.6
 */
class JElementAsset extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $_name = 'Asset';

	/**
	 * Get the options for the element
	 *
	 * @param	object $node
	 * @return	array
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		return JHTML::_('access.asset', $node->attributes('component'), $node->attributes('assettype'));
	}
}
