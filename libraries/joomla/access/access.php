<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	Access
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.access.rules');
jimport('joomla.database.query');

/**
 * Class that handles all access authorization
 *
 * @package 	Joomla.Framework
 * @subpackage	User
 * @since		1.6
 */
class JAccess extends JObject
{
	protected static $viewLevels = array();

	/**
	 * Method to check authorization for a user / action / asset combination.
	 *
	 * @access	public
	 * @param	integer	User id.
	 * @param	string	Action name.
	 * @param	string	Asset name.
	 * @return	boolean	True if authorized.
	 * @since	1.0
	 */
	public static function check($userId, $action, $asset = null)
	{
		// Sanitize inputs.
		$userId = (int) $userId;
		$action = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));
		$asset  = strtolower(preg_replace('#[\s\-]+#', '.', trim($asset)));

		// Default to the root asset node.
		if (empty($asset)) {
			$asset = 'root.1';
		}


		$db		= JFactory::getDbo();
		$query	= new JQuery;

		$query->select('b.rules');
		$query->from('#__assets AS a');
		$query->where('a.name = '.$db->quote($asset));
		$query->leftJoin('#__assets AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
		$query->order('b.lft');

		$db->setQuery($query);
		$result	= $db->loadResultArray();
		$rules	= new JRules;
		$rules->mergeCollection($result);

		// Get all groups that the user is mapped to
		$userGroupIds = self::getGroupsByUser($userId);

		return $rules->allow($action, array_merge(array($userId*-1), $userGroupIds));
	}

	/**
	 * Returns an array of the Group Ids that a user is mapped to
	 *
	 * @param	int $userId			The User Id
	 * @param	boolean $recursive	Recursively include all child groups (optional)
	 *
	 * @return	array
	 */
	public static function getGroupsByUser($userId, $recursive = true)
	{
		// Get a database object.
		$db	= &JFactory::getDbo();

		// First find the usergroups that this user is in
		$query = new JQuery;
		$query->select($recursive ? 'ug2.id' : 'ug1.id');
		$query->from('#__user_usergroup_map AS uugm');
		$query->where('uugm.user_id = '.(int) $userId);
		$query->join('LEFT', '#__usergroups AS ug1 ON ug1.id = uugm.group_id');
		if ($recursive) {
			$query->join('LEFT', '#__usergroups AS ug2 ON ug2.lft <= ug1.lft AND ug2.rgt >= ug1.rgt');
		}
		$db->setQuery((string) $query);

		$result = $db->loadResultArray();

		// Clean up any NULL values, just in case
		JArrayHelper::toInteger($result);
		array_unshift($result, '1');

		return $result;
	}

	/**
	 * Method to get the authorized access levels for a user.
	 *
	 * @access	public
	 * @param	integer	User id.
	 * @param	string	Action name.
	 * @return	array	Array of access level ids.
	 * @since	1.0
	 */
	public static function getAuthorisedViewLevels($userId)
	{
		// Get the user group ids for the user.
		$userGroupIds = self::getGroupsByUser($userId);

		// Only load the view levels once.
		if (empty(self::$viewLevels))
		{
			// Get a database object.
			$db	= JFactory::getDBO();

			// Build the base query.
			$query	= new JQuery;
			$query->select('id, rules');
			$query->from('`#__viewlevels`');

			// Set the query for execution.
			$db->setQuery((string) $query);

			// Build the view levels array.
			foreach ($db->loadAssocList() as $level)
			{
				self::$viewLevels[$level['id']] = (array) json_decode($level['rules']);
			}
		}

		// Initialize the authorised array.
		$authorised = array(1);

		// Find the authorized levels.
		foreach (self::$viewLevels as $level => $rule)
		{
			foreach ($rule as $id)
			{
				if (($id < 0) && (($id * -1) == $userId))
				{
					$authorised[] = $level;
					break;
				}
				elseif (($id >= 0) && in_array($id, $userGroupIds))
				{
					$authorised[] = $level;
					break;
				}
			}
		}

		return $authorised;
	}

	/**
	 * Method to get the available permissions of a given type for a section.
	 *
	 * @access	public
	 * @param	string	Access section name.
	 * @param	integer	Permission type.
	 * @return	array	List of available permissions.
	 * @since	1.0
	 */
	public static function getActions($component, $section = 'component')
	{
		$actions = array();

		if (is_file(JPATH_ADMINISTRATOR.'/components/'.$component.'/access.xml'))
		{
			$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/'.$component.'/access.xml');

			foreach ($xml->children() as $child)
			{
				if ($section == (string) $child['name'])
				{
					foreach ($child->children() as $action)
					{
						$actions[] = (object) array('name' => (string) $action['name'], 'title' => (string) $action['title'], 'description' => (string) $action['description']);
					}

					break;
				}
			}
		}

		return $actions;
	}

	public static function getAssetRules($assetId, $recursive = false)
	{
		jimport('joomla.access.rules');

		$db		= &JFactory::getDbo();
		$query	= new JQuery;

		$query->select($recursive ? 'b.rules' : 'a.rules');
		$query->from('#__assets AS a');
		$query->where('a.id = '.(int) $assetId);
		if ($recursive)
		{
			$query->leftJoin('#__assets AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
			$query->order('b.lft');
		}

		$db->setQuery($query);
		$result	= $db->loadResultArray();
		$rules	= new JRules;
		$rules->mergeCollection($result);

		return $rules;
	}
}
