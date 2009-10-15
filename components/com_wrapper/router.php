<?php
/**
 * @version		$Id: router.php 12644 2009-08-13 22:29:39Z pentacle $
 * @package		Joomla.Site
 * @subpackage	com_wrapper
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * @param	array
 * @return	array
 */
function WrapperBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}

	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function WrapperParseRoute($segments)
{
	$vars = array();

	$vars['view'] = 'wrapper';

	return $vars;
}