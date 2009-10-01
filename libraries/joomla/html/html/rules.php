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
		$actions = JAccess::getActions($component, $section);

		// Get the user groups.
		$groups = self::_getUserGroups();

		// Get the incoming inherited rules as well as the asset specific rules.
		$inheriting = JAccess::getAssetRules(self::_getParentAssetId($assetId), true);
		$inherited = JAccess::getAssetRules($assetId, true);
		$rules = JAccess::getAssetRules($assetId);


		$html = array();

		$html[] = '<div class="acl-options">';

		$html[] = '	<dl class="tabs">';

		$html[] = '		<dt>'.JText::_('CONTENT_ACCESS_SUMMARY').'</dt>';
		$html[] = '		<dd>';
		$html[] = '			<p>'.JText::_('CONTENT_ACCESS_SUMMARY_DESC').'</p>';
		$html[] = '			<table class="aclsummary-table" summary="'.JText::_('CONTENT_ACCESS_SUMMARY_DESC').'">';
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
				$html[] = ' 				<td class="col'.($i+2).'">'.($inherited->allow($action->name, $group->value) ? $images['allow'] : $images['deny']).'</td>';
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
			$html[] = '			<table class="aclmodify-table" summary="'.JText::_($action->description).'">';
			$html[] = ' 			<caption>ACL '.JText::_($action->title).' Table</caption>';
			$html[] = ' 			<tr>';
			$html[] = ' 				<th class="col1"></th>';
			$html[] = ' 				<th class="col2">'.JText::_('Inherit').'</th>';
			$html[] = ' 				<th class="col3"></th>';
			$html[] = ' 				<th class="col4">'.JText::_('Current').'</th>';
			$html[] = ' 			</tr>';

			foreach ($groups as $i => $group)
			{
				$html[] = ' 			<tr class="row'.($i%2).'">';
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
		$html[] = '	<ul class="acllegend">';
		$html[] = '		<li class="acl-allowed">'.JText::_('Allowed').'</li>';
		$html[] = '		<li class="acl-denied">'.JText::_('Denied').'</li>';
		$html[] = '		<li class="acl-editgroups"><a href="#">'.JText::_('Edit Groups').'</a></li>';
		$html[] = '		<li class="acl-resetbtn"><a href="#">'.JText::_('Reset to Inherit').'</a></li>';
		$html[] = '	</ul>';
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
		$images['allow'] = '<span class="icon-16-allow" title="'.JText::_('Allow').'"> </span>';
		$images['deny'] = '<span class="icon-16-deny" title="'.JText::_('Deny').'"> </span>';
		$images['allow-i'] = '<span class="icon-16-allowinactive" title="'.JText::_('Allow (Inherited)').'"> </span>';
		$images['deny-i'] = '<span class="icon-16-denyinactive" title="'.JText::_('Deny (Inherited)').'"> </span>';

		return $images;
	}
}