<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Menus Menu Item View.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.6
 */
class MediaViewBrowser extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		jimport('joomla.client.helper');

		$state		= $this->get('State');
		$folders	= $this->get('folders');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->document->addScriptDeclaration("var basepath = '".COM_MEDIA_BASE."';");

		$this->assignRef('state',		$state);
		$this->assignRef('folders',		$folders);
		$this->assign('folders_id', ' id="media-tree"');

		// Display form for FTP credentials?
		// Don't set them here, as there are other functions called before this one if there is any file write operation
		$this->assign('require_ftp',	!JClientHelper::hasCredentials('ftp'));

		parent::display($tpl);
		$this->_setToolBar();
	}

	function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children'])) {
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}

	/**
	 * Build the default toolbar.
	 */
	protected function _setToolBar()
	{
		// Get the toolbar object instance
		$bar = &JToolBar::getInstance('toolbar');

		// Set the titlebar text
		JToolBarHelper::title(JText::_('Media Manager'), 'mediamanager.png');

		// Add a delete button
		$title = JText::_('Delete');
		$dhtml = "<a href=\"#\" onclick=\"MediaManager.submit('folder.delete')\" class=\"toolbar\">
					<span class=\"icon-32-delete\" title=\"$title\"></span>
					$title</a>";
		$bar->appendButton('Custom', $dhtml, 'delete');
		JToolBarHelper::preferences('com_media');
		JToolBarHelper::help('screen.mediamanager');
	}
}
