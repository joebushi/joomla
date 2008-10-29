<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class ContentTypeHelper extends JObject
{
	public static function getTypes()
	{
		jimport('joomla.database.query');
		$db =& JFactory::getDBO();
		$query = new JQuery;

		$query->select('t.*');
		$query->select('CONCAT('.$db->Quote($db->replacePrefix('#__content_type_')).', t.table_name) AS tablename');
		$query->from('#__content_types AS t');
		$query->order('t.ordering, t.name');

		$db->setQuery($query);
		try {
			return $db->loadObjectList();
		} catch (JException $e) {
			return false;
		}
	}
}