<?php
/**
 * @version		$Id: menu.php 12605 2009-08-04 23:20:49Z pentacle $
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Renders a menu element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementMenu extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $_name = 'Menu';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'menus.php';
		$menuTypes 	= MenusHelper::getMenuTypes();

		foreach ($menuTypes as $menutype) {
			$options[] = JHtml::_('select.option', $menutype, $menutype);
		}
		array_unshift($options, JHtml::_('select.option', '', '- '.JText::_('Select Menu').' -'));

		return JHtml::_('select.genericlist',  $options, $control_name.'['.$name.']',
			array(
				'id' => $control_name.$name,
				'list.attr' => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}
}
