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
	protected $items;

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
		JResponse::setHeader('Content-disposition', 'inline; filename="'.$app->getCfg('sitename').'-tracks-'.JFactory::getDate().'.csv"', true);

		echo '"'.
			JText::_('Banners_Heading_Name').'","'.
			JText::_('Banners_Heading_Client').'","'.
			JText::_('JGrid_Heading_Category').'","'.
			JText::_('Banners_Heading_Type').'","'.
			JText::_('Banners_Heading_Count').'","'.
			JText::_('Banners_Heading_Date').'"'."\n";
		foreach($items as $i=>$item)
		{
			echo '"'.
				$item->name.'","'.
				$item->client_name.'","'.
				$item->category_title.'","'.
				($item->track_type==1 ? JText::_('Banners_Impression'): JText::_('Banners_Click')).'","'.
				$item->count.'","'.
				$item->track_date.'"'."\n";
		}
	}
}
