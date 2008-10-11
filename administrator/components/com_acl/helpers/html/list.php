<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * HTML Grid Helper
 *
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 */
class AclList
{
	function enabled( $value, $i )
	{
		$images	= array( 0 => 'images/publish_x.png', 1 => 'images/tick.png' );
		$alts	= array( 0 => 'Disabled', 1 => 'Enabled' );
		$img 	= JArrayHelper::getValue( $images, $value, $images[0] );
		$task 	= $value == 1 ? 'acl.disable' : 'acl.enable';
		$alt 	= JArrayHelper::getValue( $alts, $value, $images[0] );
		$action = JText::_( 'Click to toggle setting' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		<img src="'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}

	function allowed( $value, $i )
	{
		$images	= array( 0 => 'images/publish_x.png', 1 => 'images/tick.png' );
		$alts	= array( 0 => 'Denied', 1 => 'Allowed' );
		$img 	= JArrayHelper::getValue( $images, $value, $images[0] );
		$task 	= $value == 1 ? 'acl.deny' : 'acl.allow';
		$alt 	= JArrayHelper::getValue( $alts, $value, $images[0] );
		$action = JText::_( 'Click to toggle setting' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		<img src="'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}

}