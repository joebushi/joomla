<?php
/**
 * @version		$Id: object.php 11064 2008-10-13 01:20:10Z ircmaxell $
 * @package		Joomla.Framework
 * @subpackage	Base
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * Object class, allowing __construct in PHP4.
 *
 * @package		Joomla.Framework
 * @subpackage	Base
 * @since		1.5
 */
class JStdClass EXTENDS JObject
{

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @access	public
	 * @since	1.5
	 */
	public function __construct() {}

}
