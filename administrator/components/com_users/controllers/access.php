<?php
/**
 * @version		$Id: group.php 11952 2009-06-01 03:21:19Z robs $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * The Users Group Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerAccess extends JController
{
	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function saveAccess()
	{
		$component = JRequest::getVar('component');
		$db =& JFactory::getDBO();
		$query = 'SELECT id FROM #__access_rules WHERE section = '.$db->Quote($component).' AND access_type = 1';
		$db->setQuery($query);
		$rules = $db->loadResultArray();
		$query = 'DELETE FROM #__access_rules WHERE id IN ('.implode(',', $rules).');';
		$query = 'DELETE FROM #__access_action_rule_map WHERE rule_id IN ('.implode(',', $rules).');';
		$query = 'DELETE FROM #__usergroup_rule_map WHERE rule_id IN ('.implode(',', $rules).');';
		$db->setQuery($query);
		$db->Query();
		$rules = JRequest::getVar('accessrules', array());
		$component = JRequest::getVar('component');
		jimport('joomla.access.permission.simplerule');
		foreach($rules as $group => $actions)
		{
			foreach($actions as $action => $value)
			{
				if($value == 1)
				{
					$rule = JSimpleRule::getInstance();
					$rule->setAction($component.'.'.$action);
					$rule->setUserGroups(array($group));
					$rule->setSection($component);
					$rule->store();
				}
			}
		}

		return true;	
	}
}
?>