<?php
/**
 * @version		$Id$
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
class JElementAssetGroups extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $_name = 'AssetGroups';

	/**
	 * Get the options for the element
	 *
	 * @param	object $node
	 * @return	array
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDbo();
		$db->setQuery(
			'SELECT id AS value, title AS text'
			.' FROM #__access_assetgroups'
			.' ORDER BY left_id'
		);
		$options = $db->loadObjectList();
		return JHTML::_('access.assetgroups', 'access', $value);
	}
}
