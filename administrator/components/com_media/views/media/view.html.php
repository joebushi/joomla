<?php
/**
* @version		$Id: view.html.php 8582 2007-08-27 14:37:02Z jinx $
* @package		Joomla
* @subpackage	Media
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Media component
 *
 * @static
 * @package		Joomla
 * @subpackage	Media
 * @since 1.0
 */
class MediaViewMedia extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		$style = $mainframe->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

		$listStyle = "
			<ul id=\"submenu\">
				<li><a id=\"thumbs\" onclick=\"document.mediamanager.setViewType('thumbs')\">".JText::_('Thumbnail View')."</a></li>
				<li><a id=\"details\" onclick=\"document.mediamanager.setViewType('details')\">".JText::_('Detail View')."</a></li>
			</ul>
		";

		$document =& JFactory::getDocument();
		$document->setBuffer($listStyle, 'module', 'submenu');

		JHTML::_('behavior.mootools');
		$document->addScript('components/com_media/assets/mediamanager.js');
		$document->addStyleSheet('components/com_media/assets/mediamanager.css');

		JHTML::_('behavior.modal');
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			document.preview = SqueezeBox;
		});");

		JHTML::_('behavior.uploader', 'file-upload', array('onAllComplete' => 'function(){ document.mediamanager.refreshFrame(); }'));

		$base = str_replace("\\","/",JPATH_ROOT);
		$js = "
			var basepath = '".$base.'/images'."';
			var viewstyle = '".$style."';
		" ;
		$document->addScriptDeclaration($js);

		$this->assignRef('session', JFactory::getSession());
		$this->assignRef('config', JComponentHelper::getParams('com_media'));
		$this->assignRef('state', $this->get('state'));
		$this->assign('require_ftp', false);
		$this->assign('current', '');
		$this->assign('folders', $this->get('folderTree'));

		parent::display($tpl);
		echo JHTML::_('behavior.keepalive');
	}

	function getFolderLevel($folder)
	{
		$txt = null;
		if (isset($folder['children']) && count($folder['children'])) {
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
}
?>
