<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class BannersViewTracks extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$items		= $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$document = &JFactory::getDocument();
		$app = &JFactory::getApplication();
		$document->setType('raw');
		$document->setMimeEncoding('text/csv');
		JResponse::setHeader('Content-disposition', 'inline; filename="'.JText::sprintf('Banners_FileName',$app->getCfg('sitename'),JFactory::getDate()).'.csv"', true);

		echo '"'.
			str_replace('"','""',JText::_('Banners_Heading_Name')).'","'.
			str_replace('"','""',JText::_('Banners_Heading_Client')).'","'.
			str_replace('"','""',JText::_('JGrid_Heading_Category')).'","'.
			str_replace('"','""',JText::_('Banners_Heading_Type')).'","'.
			str_replace('"','""',JText::_('Banners_Heading_Count')).'","'.
			str_replace('"','""',JText::_('Banners_Heading_Date')).'"'."\n";
		foreach($items as $i=>$item)
		{
			echo '"'.
				str_replace('"','""',$item->name).'","'.
				str_replace('"','""',$item->client_name).'","'.
				str_replace('"','""',$item->category_title).'","'.
				str_replace('"','""',($item->track_type==1 ? JText::_('Banners_Impression'): JText::_('Banners_Click'))).'","'.
				str_replace('"','""',$item->count).'","'.
				str_replace('"','""',$item->track_date).'"'."\n";
		}
	}
}

