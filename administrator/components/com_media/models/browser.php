<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Media Component Manager Model
 *
 * @package		Joomla.Administrator
 * @subpackage	Media
 * @since		1.6
 */
class MediaModelBrowser extends JModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_media.browser';

	/**
	 * Method to auto-populate the model state.
	 */
	protected function _populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$folder = $app->getUserStateFromRequest($this->_context.'.folder', 'folder', '/');
		$this->setState('media.folder', JPath::clean($folder));

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_media');
		$this->setState('params', $params);
	}

	/**
	 * Get the folder tree for the media manager browsing view.
	 *
	 * @param	string	The base path to browse from.
	 */
	function getFolders($base = null)
	{
		// Initialise variables.
		if (empty($base)) {
			$base = COM_MEDIA_BASE;
		}
		$mediaBase = str_replace(DS, '/', COM_MEDIA_BASE);

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$tree = array();
		foreach ($folders as $folder) {
			$folder		= str_replace(DS, '/', $folder);
			$name		= substr($folder, strrpos($folder, '/') + 1);
			$relative	= str_replace($mediaBase, '', $folder);
			$absolute	= $folder;
			$path		= explode('/', $relative);
			$node		= (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);

			$tmp = &$tree;

			for ($i=0,$n=count($path); $i<$n; $i++) {
				if (!isset($tmp['children'])) {
					$tmp['children'] = array();
				}
				if ($i == $n-1) {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}
				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
		$tree['data'] = (object) array('name' => JText::_('Media'), 'relative' => '/', 'absolute' => $base);

		return $tree;
	}
}