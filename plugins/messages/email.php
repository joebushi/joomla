<?php
/**
* @version		$Id: email.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! E-Mail Message plugin
 *
 * @author		Hannes Papenberg <hannes.papenberg@community.joomla.org>
 * @package		Joomla
 * @subpackage	Message
 */
class  plgMessageEmail extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgMessageEmail()
	{
		require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_messages'.DS.'helper'.DS.'message.php');

		$message =& JMessageHelper::getInstance();

		$message->addMessageAdapter('Email');
	}
}

require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_messages'.DS.'helper'.DS.'messageplg.php');

class JMessageEmail extends JMessage
{
	function __construct()
	{

	}

	function getMessages($folder = null)
	{

	}

	function getMessage($id)
	{

	}

	function getFolders()
	{

	}

	function addFolder($folder)
	{

	}

	function deleteFolder($folder)
	{

	}

	function sendMessage($recipients, $topic, $body, $options)
	{

	}

	function deleteMessage($id)
	{

	}

	function moveMessage($id, $folder)
	{

	}

	function setMessageState($id, $read)
	{

	}
}