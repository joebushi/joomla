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

if (!defined('JPERMISSION_VIEW')) {
	define('JPERMISSION_VIEW', 3);
}
if (!defined('JPERMISSION_ASSET')) {
	define('JPERMISSION_ASSET', 2);
}
if (!defined('JPERMISSION_ACTION')) {
	define('JPERMISSION_ACTION', 1);
}

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

	var $_quiet = true;

	function quiet($value)
	{
		$old = $this->_quiet;
		$this->_quiet = (boolean) $value;
		return $old;
	}

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
	public function check($userId, $actionName, $assetName = null)
	{
		// Sanitize inputs.
		$userId = (int) $userId;
		$actionName = strtolower(preg_replace('#[\s\-]+#', '.', trim($actionName)));
		$assetName  = strtolower(preg_replace('#[\s\-]+#', '.', trim($assetName)));


		$db		= JFactory::getDbo();
		$query	= new JQuery;

		$query->select('b.rules');
		$query->from('#__access_assets AS a');
		$query->where('a.name = '.$db->quote($assetName));
		$query->leftJoin('#__access_assets AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
		$query->order('b.lft');

		$db->setQuery($query);
		$result	= $db->loadResultArray();
		$rules	= new JRules;
		$rules->mergeCollection($result);

		// Get all groups that the user is mapped to
		$userGroupIds = $this->getUserGroupMap($userId, true);

		return $rules->allow($actionName, array_merge(array($userId*-1), $userGroupIds));
	}

	/**
	 * Returns an array of the Group Ids that a user is mapped to
	 *
	 * @param	int $userId			The User Id
	 * @param	boolean $recursive	Recursively include all child groups (optional)
	 *
	 * @return	array
	 */
	public function getUserGroupMap($userId, $recursive = false)
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

		$this->_quiet or $this->_log($db->getQuery());

		$result = $db->loadResultArray();

		// Clean up any NULL values, just in case
		JArrayHelper::toInteger($result);
		array_unshift($result, '1');

		$this->_quiet or $this->_log("User $userId in groups: ".print_r($result, 1));

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
	public function getAuthorisedAccessLevels($userId)
	{
		// Get the user group ids for the user.
		$userGroupIds = $this->getUserGroupMap($userId, true);

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

			// Debugging option.
			$this->_quiet or $this->_log($db->getQuery());

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
	public function getAvailablePermissions($component, $section = 'component')
	{
		$permissions = array();

		if (is_file(JPATH_ADMINISTRATOR.'/components/'.$component.'/access.xml'))
		{
			$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/'.$component.'/access.xml');

			foreach ($xml->children() as $child)
			{
				if ($section == (string) $child['name'])
				{
					foreach ($child->children() as $action)
					{
						$permissions[] = (object) array('name' => (string) $action['name'], 'title' => (string) $action['title'], 'description' => (string) $action['description']);
					}

					break;
				}
			}
		}

		return $permissions;
	}

	/**
	 * Returns an array of the User Group ID's that can perform a given action
	 *
	 * @value	string $action	The name of the action
	 *
	 * @return	array
	 */
	function getAuthorisedUsergroups($action, $recursive = false)
	{
		// Get a database object.
		$db	= &JFactory::getDbo();

		// Build the base query.
		$query	= new JQuery;
		$query->select('DISTINCT ug2.id');
		$query->from('`#__access_actions` AS a');
		// Map actions to rules
		$query->join('INNER',	'`#__access_action_usergroup_map` AS agm ON agm.action_id = a.id');

		if ($recursive) {
			$query->join('INNER', '#__usergroups AS ug1 ON ug1.id = agm.usergroup_id');
			$query->join('LEFT', '#__usergroups AS ug2 ON ug2.lft >= ug1.lft AND ug2.rgt <= ug1.rgt');
		}
		else {
			$query->join('INNER', '#__usergroups AS ug2 ON ug2.id = ugrm.group_id');
		}

		// Handle an array of actions or just a single action.
		if (is_array($action))
		{
			// Quote the actions.
			foreach ($action as $k => $v) {
				$action[$k] = $db->Quote($v);
			}
			$query->where('(a.name = '.implode(' OR a.name = ', $action).')');
		}
		else {
			$query->where('a.name = '.$db->Quote($action));
		}

		$db->setQuery((string) $query);

		$this->_quiet or $this->_log($db->getQuery());

		$ids = $db->loadResultArray();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
			return false;
		}

		$this->_quiet or $this->_log(print_r($ids, 1));

		return $ids;
	}

	public static function getAssetRules($assetId, $recursive = false)
	{
		jimport('joomla.access.rules');

		$db		= &JFactory::getDbo();
		$query	= new JQuery;

		$query->select($recursive ? 'b.rules' : 'a.rules');
		$query->from('#__access_assets AS a');
		$query->where('a.id = '.(int) $assetId);
		if ($recursive)
		{
			$query->leftJoin('#__access_assets AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
			$query->order('b.lft');
		}

		$db->setQuery($query);
		$result	= $db->loadResultArray();
		$rules	= new JRules;
		$rules->mergeCollection($result);

		return $rules;
	}

	function _log($text)
	{
		echo nl2br($text).'<hr />';
	}
}
