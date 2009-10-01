<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.database.query');

/**
 * Extended Utility class for all HTML drawing classes.
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.6
 */
abstract class JHtmlRules
{
	/**
	 * Displays a list of the available access sections
	 *
	 * @param	string	The form field name.
	 * @param	string	The name of the selected section.
	 * @param	string	Additional attributes to add to the select field.
	 * @param	boolean	True to add "All Sections" option.
	 *
	 * @return	string	The required HTML for the SELECT tag.
	 */
	public static function assetFormWidget($component, $section, $assetId = null, $control = 'jform[rules2]')
	{
		// Load the behavior.
		self::_loadBehavior();

		// Load the behavior.
		$images = self::_getImagesArray();

		// Get the actions for the asset.
		$access = JFactory::getACL();
		$actions = $access->getAvailablePermissions($component, $section);

		// Get the user groups.
		$groups = self::_getUserGroups();

		// Get the incoming inherited rules as well as the asset specific rules.
		$inheriting = JAccess::getAssetRules(self::_getParentAssetId($assetId), true);
		$inherited = JAccess::getAssetRules($assetId, true);
		$rules = JAccess::getAssetRules($assetId);


		$html = array();

		$html[] = '<div id="acl-options">';
		$html[] = '	<p>Customize the rules for this Article. The Inherit column displays the parent setting and the Current column displays the computed rule based on the value of the dropdown menu next to each group.</p>';

		$html[] = '	<dl class="tabs">';

		$html[] = '		<dt>'.JText::_('Summary').'</dt>';
		$html[] = '		<dd>';
		$html[] = '			<p>Below is an overview of the access setting for this article. Click the tabs above to customize these settings by action.</p>';
		$html[] = '			<table id="aclsummary-table" summary="Below is an overview of the access setting for this article. Click the tabs above to customize these settings by action.">';
		$html[] = ' 			<caption>ACL Summary Table</caption>';
		$html[] = ' 			<tr>';
		$html[] = ' 				<th class="col1"></th>';
		foreach ($actions as $i => $action)
		{
			$html[] = ' 				<th class="col'.($i+2).'">'.JText::_($action->title).'</th>';
		}
		$html[] = ' 			</tr>';

		foreach ($groups as $i => $group)
		{
			$html[] = ' 			<tr class="row'.($i%2).'">';
			$html[] = ' 				<td class="col1">'.$group->text.'</td>';
			foreach ($actions as $i => $action)
			{
				$html[] = ' 				<td class="col'.($i+2).'">'.($inherited->allow($action->name, $group->value) ? $images['allow-i'] : $images['deny-i']).'</td>';
			}
			$html[] = ' 			</tr>';
		}

		$html[] = ' 		</table>';
		$html[] = ' 	</dd>';

		foreach ($actions as $action)
		{
			$html[] = '		<dt>'.JText::_($action->title).'</dt>';
			$html[] = '		<dd style="display:none;">';
			$html[] = '			<p>'.JText::_($action->description).'</p>';
			$html[] = '			<table id="aclmodify-table" summary="'.JText::_($action->description).'">';
			$html[] = ' 			<caption>ACL '.JText::_($action->title).' Table</caption>';
			$html[] = ' 			<tr>';
			$html[] = ' 				<th class="col1"></th>';
			$html[] = ' 				<th class="col2">'.JText::_('Inherit').'</th>';
			$html[] = ' 				<th class="col3"></th>';
			$html[] = ' 				<th class="col4">'.JText::_('Current').'</th>';
			$html[] = ' 			</tr>';

			foreach ($groups as $i => $group)
			{
				$html[] = ' 			<tr class="row'.$i.'">';
				$html[] = ' 				<td class="col1">'.$group->text.'</td>';
				$html[] = ' 				<td class="col2">'.($inheriting->allow($action->name, $group->value) ? $images['allow-i'] : $images['deny-i']).'</td>';
				$html[] = ' 				<td class="col3">';
				$html[] = ' 					<select id="'.$action->name.'_'.$group->value.'" class="inputbox" size="1" name="'.$control.'['.$action->name.']['.$group->value.']">';
				$html[] = ' 						<option value=""'.($rules->allow($action->name, $group->value) === null ? ' selected="selected"' : '').'>'.JText::_('Inherit').'</option>';
				$html[] = ' 						<option value="1"'.($rules->allow($action->name, $group->value) === true ? ' selected="selected"' : '').'>'.JText::_('Allow').'</option>';
				$html[] = ' 						<option value="0"'.($rules->allow($action->name, $group->value) === false ? ' selected="selected"' : '').'>'.JText::_('Deny').'</option>';
				$html[] = ' 					</select>';
				$html[] = ' 				</td>';
				$html[] = ' 				<td class="col4">'.($inherited->allow($action->name, $group->value) ? $images['allow'] : $images['deny']).'</td>';
				$html[] = ' 			</tr>';
			}

			$html[] = ' 		</table>';
			$html[] = ' 	</dd>';
		}

		$html[] = ' </dl>';

		// Build the footer with legend and special purpose buttons.
		$html[] = '	<div class="clr"></div>';
		$html[] = '	<div id="acllegend">';
		$html[] = '		'.$images['allow'];
		$html[] = '		<span>Allowed</span>';
		$html[] = '		'.$images['deny'];
		$html[] = '		<span>Denied</span>';
		$html[] = '	</div>';
		$html[] = '	<div id="acleditgroups">';
		$html[] = '		<p><a href="#">Edit Groups</a></p>';
		$html[] = '	</div>';
		$html[] = '	<div id="aclresetbtn">';
		$html[] = '		<p><a href="#">Reset to Inherit</a></p>';
		$html[] = '	</div>';

		$html[] = '</div>';

		return implode("\n", $html);
	}

	protected static function _getParentAssetId($assetId)
	{
		// Get a database object.
		$db = JFactory::getDBO();

		// Get the user groups from the database.
		$db->setQuery(
			'SELECT parent_id' .
			' FROM #__assets' .
			' WHERE id = '.(int) $assetId
		);
		return (int) $db->loadResult();
	}

	protected static function _getUserGroups()
	{
		// Get a database object.
		$db = JFactory::getDBO();

		// Get the user groups from the database.
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();

		// Pad the option text with spaces using depth level as a multiplier.
//		foreach ($options as $option) {
//			$option->text = str_repeat('&nbsp;&nbsp;',$option->level).$option->text;
//		}

		return $options;
	}

	protected static function _loadBehavior()
	{
		JFactory::getDocument()->addScriptDeclaration('window.addEvent(\'domready\', function(){$$(\'dl.tabs\').each(function(tabs){new JTabs(tabs);});});');
		JHtml::script('tabs.js');
	}

	protected static function _getImagesArray()
	{
		$base = JURI::root(true);
		$images['allow'] = '<img alt="Allow" src="'.$base.'/administrator/templates/bluestork/images/admin/icon-16-allow.png" />';
		$images['deny'] = '<img alt="Deny" src="'.$base.'/administrator/templates/bluestork/images/admin/icon-16-deny.png" />';
		$images['allow-i'] = '<img alt="Allow (Inherited)" src="'.$base.'/administrator/templates/bluestork/images/admin/icon-16-allowinactive.png" />';
		$images['deny-i'] = '<img alt="Deny (Inherited)" src="'.$base.'/administrator/templates/bluestork/images/admin/icon-16-denyinactive.png">';

		return $images;
	}
}