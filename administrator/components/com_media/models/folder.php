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
class MediaModelFolder extends JModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_media.folder';

	/**
	 * Method to auto-populate the model state.
	 */
	protected function _populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$folder = $app->getUserStateFromRequest($this->_context.'.folder', 'folder');
		$this->setState('media.folder', JPath::clean($folder));

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_media');
		$this->setState('params', $params);
	}

	/**
	 */
	function getFolders()
	{
		static $folders;

		// Only process the list once per request
		if (is_array($folders)) {
			return $folders;
		}

		// Get current path.
		$current = rtrim($this->getState('media.folder'), '/');

		// If undefined, set to empty
		if (empty($current) || $current[0] != '/') {
			$current = '/';
		}

		// Initialise variables.
		$basePath	= COM_MEDIA_BASE.$current;
		$folders 	= array();

		// Get the sub-folders.
		$folderList = JFolder::folders($basePath);

		// Iterate over the folders if they exist.
		if ($folderList !== false) {
			foreach ($folderList as $folder) {
				$tmp = new JObject;
				$tmp->name = basename($folder);
				$tmp->path = JPath::clean($basePath.'/'.$folder, '/');
				$tmp->path_relative = str_replace(COM_MEDIA_BASE, '', $tmp->path);
				$count = MediaHelper::countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		return $folders;
	}

	/**
	 */
	function getFiles()
	{
		static $files;

		// Only process the list once per request
		if (is_array($files)) {
			return $files;
		}

		// Include dependancies.
		jimport('joomla.filesystem.file');

		// Get current path.
		$current = rtrim($this->getState('media.folder'), '/');

		// If undefined, set to empty
		if (empty($current) || $current[0] != '/') {
			$current = '/';
		}

		// Initialise variables.
		$basePath	= COM_MEDIA_BASE.$current;
		$files 		= array();

		// Get the list of files.
		$fileList 	= JFolder::files($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file) {
				if ($this->_isFile($basePath, $file)) {
					$tmp = new JObject;
					$tmp->name = $file;
					$tmp->path = JPath::clean($basePath.'/'.$file, '/');
					$tmp->path_relative = str_replace(COM_MEDIA_BASE, '', $tmp->path);
					$tmp->size = filesize($tmp->path);

					$ext = strtolower(JFile::getExt($file));
					switch ($ext)
					{
						// Image
						case 'jpg':
						case 'png':
						case 'gif':
						case 'xcf':
						case 'odg':
						case 'bmp':
						case 'jpeg':
							$tmp->type		= 'image';

							$info = @getimagesize($tmp->path);
							$tmp->width		= @$info[0];
							$tmp->height	= @$info[1];
							$tmp->type		= @$info[2];
							$tmp->mime		= @$info['mime'];

							$filesize		= MediaHelper::parseSize($tmp->size);

							if (($info[0] > 60) || ($info[1] > 60)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 60);
								$tmp->width_60 = $dimensions[0];
								$tmp->height_60 = $dimensions[1];
							} else {
								$tmp->width_60 = $tmp->width;
								$tmp->height_60 = $tmp->height;
							}

							if (($info[0] > 16) || ($info[1] > 16)) {
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 16);
								$tmp->width_16 = $dimensions[0];
								$tmp->height_16 = $dimensions[1];
							} else {
								$tmp->width_16 = $tmp->width;
								$tmp->height_16 = $tmp->height;
							}

						// Non-image document
						default:
							$tmp->type		= 'other';

							// $iconfile_32 = JPATH_ADMINISTRATOR.DS."components".DS."com_media".DS."images".DS."mime-icon-32".DS.$ext.".png";
							$iconfile_32 = '../media/media/images/mime-icon-32/'.$ext.'.png';
							if (file_exists($iconfile_32)) {
								$tmp->icon_32 = $iconfile_32;
							} else {
								$tmp->icon_32 = '../media/media/images/con_info.png';
							}
							// $iconfile_16 = JPATH_ADMINISTRATOR.DS.'components/com_media/images/mime-icon-16'.DS.$ext.'.png';
							$iconfile_16 = '../media/media/images/mime-icon-16/'.$ext.'.png';
							if (file_exists($iconfile_16)) {
								$tmp->icon_16 = $iconfile_16;
							} else {
								$tmp->icon_16 = '../media/media/images/con_info.png';
							}
							$files[] = $tmp;
							break;
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Method to determine whether file is valid or should be ignored.
	 *
	 * @param	string	The file name.
	 *
	 * @return	boolean
	 */
	protected function _isFile($filePath, $fileName)
	{
		if (!is_file($filePath.'/'.$fileName)) {
			return false;
		}
		if (substr($fileName, 0, 1) == '.') {
			return false;
		}
		if (strtolower($fileName) == 'index.html') {
			return false;
		}

		return true;
	}
}