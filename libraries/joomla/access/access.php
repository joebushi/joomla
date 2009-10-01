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
class JAccess
{
	protected static $viewLevels = array();

	/**
	 * Method to check if a user is authorised to perform an action, optionally on an asset.
	 *
	 * @param	integer	Id of the user for which to check authorisation.
	 * @param	string	The name of the action to authorise.
	 * @param	mixed	Integer asset id or the name of the asset as a string.  Defaults to the global asset node.
	 * @return	boolean	True if authorised.
	 * @since	1.6
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

		// Get the rules for the asset, recursively to root.
		$rules = self::getAssetRules($asset, true);

		// Get all groups that the user is mapped to recursively.
		$groups = self::getGroupsByUser($userId);

		return $rules->allow($action, array_merge(array($userId*-1), $groups));
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
		// Get all groups that the user is mapped to recursively.
		$groups = self::getGroupsByUser($userId);

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
				elseif (($id >= 0) && in_array($id, $groups))
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

	/**
	 * Method to return the JRules object for an asset.  The returned object can optionally hold
	 * only the rules explicitly set for the asset or the summation of all inherited rules from
	 * parent assets and explicit rules.
	 *
	 * @param	mixed	Integer asset id or the name of the asset as a string.
	 * @param	boolean	True to return the rules object with inherited rules.
	 * @return	object	JRules object for the asset.
	 * @since	1.6
	 */
	public static function getAssetRules($asset, $recursive = false)
	{
		// Get the database connection object.
		$db = JFactory::getDbo();

		// Build the database query to get the rules for the asset.
		$query	= new JQuery;
		$query->select($recursive ? 'b.rules' : 'a.rules');
		$query->from('#__assets AS a');

		// If the asset identifier is numeric assume it is a primary key, else lookup by name.
		if (is_numeric($asset)) {
			$query->where('a.id = '.(int) $asset);
		}
		else {
			$query->where('a.name = '.$db->quote($asset));
		}

		// If we want the rules cascading up to the global asset node we need a self-join.
		if ($recursive)
		{
			$query->leftJoin('#__assets AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
			$query->order('b.lft');
		}

		// Execute the query and load the rules from the result.
		$db->setQuery($query);
		$result	= $db->loadResultArray();

		// Instantiate and return the JRules object for the asset rules.
		$rules	= new JRules;
		$rules->mergeCollection($result);

		return $rules;
	}
}
