<?php
/**
* @version		$Id: jmessage.php 10381 2008-06-01 03:35:53Z pasamio $
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
 * Joomla! JMessage Message plugin
 *
 * @author		Hannes Papenberg <hannes.papenberg@community.joomla.org>
 * @package		Joomla
 * @subpackage	Message
 */
class  plgMessageJmessage extends JPlugin
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
	function plgMessageJmessage()
	{
		require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_messages'.DS.'helper'.DS.'message.php');

		$message =& JMessageHelper::getInstance();

		$message->addMessageAdapter('Jmessage');
	}
}

require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_messages'.DS.'helper'.DS.'messageplg.php');

class JMessageJmessage extends JMessage
{
	var $_data = array();

	var $_id = null;

	var $_loaded = false;

	function __construct($user, $loadData = false)
	{
		if($this->_loaded)
		{
			$this->_loadData($user);
		}
	}

	function getMessages($folder = null)
	{
		if($this->_loaded)
		{
			$this->_loadData();
		}
		if($folder == null)
		{
			return $this->_data;		
		} else {
			foreach($this->_data as $data)
			{
				if($data->folder == $folder)
				{
					$result[] = $data;
				}
			}
			return $result;
		}
	}

	function getMessage($id)
	{
		if(!isset($this->_data))
		{
			$this->_loadData();
		}
		return $this->_data[$id];
	}

	function getFolders()
	{

	}

	function addFolder($folder)
	{

	}

	function setFolder($id)
	{

	}

	function deleteFolder($folder)
	{

	}

	function sendMessage($message)
	{
		if(is_a($message, 'JMessageMessage'))
		{
			$db =& JFactory::getDBO();
			$query = 'INSERT INTO #__messages'.
					' (`user_id_from` ,`user_id_to` ,`folder_id`'.
					' ,`date_time` ,`state` ,`priority` ,`subject` ,`message`)'.
					' VALUES ('.$message->from.','.$message->to.','.$message->folder.
					', '.$message->datetime.','.$message->state.','.$message->priority.
					','.$db->Quote($message->subject).','.$db->Quote($message->message).');';
			$db->setQuery($query);
			$db->Query();
		}
	}

	function deleteMessage($id)
	{

	}

	function moveMessage($id, $folder)
	{

	}

	function _loadData($user = null)
	{
		if($user == null)
		{
			$user = JFactory::getUser();
		}
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__messages WHERE user_id_from = '.$user->get('id');
		$db->setQuery($query);
		$results = $db->loadObjectList();
		foreach($results as $result)
		{
			$message = new JMessageMessage();
			$message->id = $result->message_id;
			$message->from = $result->user_id_from;
			$message->to = $result->user_id_to;
			$message->folder = $result->folder_id;
			$message->datetime = $result->date_time;
			$message->state = $result->state;
			$message->priority = $result->priority;
			$message->subject = $result->subject;
			$message->body = $result->message;
			$message->adapter = 'Jmessage';
			$this->_data[$message->id] = $message;
		}
		$this->_loaded = true;
	}
}