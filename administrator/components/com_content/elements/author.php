<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Articles
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.database.query');

/**
 * Renders a author element
 *
 * @package 	Joomla
 * @subpackage	Articles
 * @since		1.5
 */
class JElementAuthor extends JElement
{
	/**
	 * The name of the element.
	 *
	 * @var		string
	 */
	var	$_name = 'Author';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$access	= JFactory::getACL();

		// Include user in groups that have access to edit their articles, other articles, or manage content.
		$action = array('com_content.article.edit_own', 'com_content.article.edit_article', 'com_content.manage');
		$groups	= $access->getAuthorisedUsergroups($action, true);

		// Check the results of the access check.
		if (!$groups) {
			return false;
		}

		// Clean up and serialize.
		JArrayHelper::toInteger($groups);
		$groups = implode(',', $groups);

		// Build the query to get the users.
		$query = new JQuery();
		$query->select('u.id AS value');
		$query->select('u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__user_usergroup_map AS m ON m.user_id = u.id');
		$query->where('u.block = 0');
		$query->where('m.group_id IN ('.$groups.')');

		// Get the users.
		$db = JFactory::getDbo();
		$db->setQuery((string) $query);
		$users = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
			return false;
		}

		return JHtml::_('select.genericlist', $users, $name, 'class="inputbox" size="1"', 'value', 'text', $value);
	}
}