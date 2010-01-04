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
class MediaViewFolder extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Do not allow cache.
		JResponse::allowCache(false);

		$state		= $this->get('State');
		$folders	= $this->get('Folders');
		$files		= $this->get('Files');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',	$state);
		$this->assignRef('folders',	$folders);
		$this->assignRef('files',	$files);

		parent::display($tpl);
	}
}
