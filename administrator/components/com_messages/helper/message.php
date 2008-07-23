<?php
/**
 * @version		$Id: message.php 10123 2008-03-10 12:24:14Z willebil $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JMessageHelper
{
	/**
	 * Message Plugins available
	 *
	 * @access	private
	 * @var	array
	 */
	var $_messageAdapters	= array();

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param database A database connector object
	 */
	function __construct(& $db)
	{

	}

	function getInstance()
	{
		static $instance;

		if(!isset($instance))
		{
			$instance = new JMessageHelper();
		}
		return $instance;
	}

	function getMessageAdapters()
	{
		return $this->_messageAdapters;
	}

	function addMessageAdapter($adapter)
	{
		$this->_messageAdapters[] = $adapter;
	}
}

class JMessageMessage
{
	var $id = null;

	var $from = null;

	var $to = null;

	var $folder = null;

	var $datetime = null;

	var $state = null;

	var $priority = null;

	var $subject = null;

	var $message = null;
	
	var $adapter = null;
}