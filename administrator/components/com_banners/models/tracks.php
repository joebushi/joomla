<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.database.query');

/**
 * Methods supporting a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class BannersModelTracks extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_banners.tracks';
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JQuery
	 */
	protected function _getListQuery()
	{
		// Get the application object
		$app = &JFactory::getApplication();
		
		require_once JPATH_COMPONENT . '/helpers/banners.php';

		// Create a new query object.
		$query = new JQuery;

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'*'
			)
		);
		$query->from('`#__banner_tracks` AS a');
		return $query;
	}
}
