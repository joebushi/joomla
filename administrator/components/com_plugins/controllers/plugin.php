<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Plugins controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @version		1.6
 */
class PluginsControllerPlugins extends JController
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('apply', 'save');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('edit', 'display');
		$this->registerTask('add', 'display');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');
	}