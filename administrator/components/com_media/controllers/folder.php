<?php
/**
 * @version		$Id: weblink.php 7873 2007-07-05 22:44:21Z friesengeist $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Weblinks Weblink Controller
 *
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.5
 */
class MediaControllerFolder extends MediaController
{
	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The image directory to delete a file from
	 * @since 1.5
	 */
	function delete($current)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		$msg	= null;
		$paths	= JRequest::getVar( 'rm', array(), '', 'array' );
		$ret	= false;
		if (count($paths)) {
			foreach ($paths as $path)
			{
				if ($path !== JFilterInput::clean($path, 'cmd')) {
					$msg .= '<font color="red">'.JText::_('Unable to delete:').htmlspecialchars($path).' '.JText::_('WARNFILENAME').'</font><br />';
					continue;
				}

				$fullPath = JPath::clean(COM_MEDIA_BASE.DS.$current.DS.$path);
				if (is_file($fullPath)) {
					$ret |= !JFile::delete($fullPath);
				} else if (is_dir($fullPath)) {
					$files = JFolder::files($fullPath, '.', true);
					$canDelete = true;
					foreach ($files as $file) {
						if ($file != 'index.html') {
							$canDelete = false;
						}
					}
					if ($canDelete) {
						$ret |= !JFolder::delete($fullPath);
					} else {
						$msg .= '<font color="red">'.JText::_('Unable to delete:').$fullPath.' '.JText::_('Not Empty!').'</font><br />';
					}
				}
			}
		}
		return !$ret;
	}

	/**
	 * Create a folder
	 *
	 * @param string $path Path of the folder to create
	 * @since 1.5
	 */
	function createFolder()
	{
		global $mainframe;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		$folder			= JRequest::getCmd( 'foldername', '');
		$folderCheck	= JRequest::getVar( 'foldername', null, '', 'string', JREQUEST_ALLOWRAW);
		$parent			= JRequest::getVar( 'dirpath', '', '', 'path' );

		JRequest::setVar('folder', $parent);

		if (($folderCheck !== null) && ($folder !== $folderCheck)) {
			$mainframe->redirect('index.php?option=com_media&folder='.$parent, JText::_('WARNDIRNAME'));
		}

		if (strlen($folder) > 0) {
			$path = JPath::clean(COM_MEDIA_BASE.DS.$parent.DS.$folder);
			if (!is_dir($path) && !is_file($path))
			{
				jimport('joomla.filesystem.*');
				JFolder::create($path);
				JFile::write($path.DS."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
			}
			JRequest::setVar('folder', ($parent) ? $parent.'/'.$folder : $folder);
		}
	}
}
