<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldRules extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Rules';


	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function _getInput()
	{
		// Get relevant attributes from the field definition.
		$section = $this->_element->attributes('section') !== null ? $this->_element->attributes('section') : '';
		$assetField = $this->_element->attributes('asset_field') !== null ? $this->_element->attributes('asset_field') : 'asset_id';

		// Get the actions for the asset.
		$access = JFactory::getACL();
		$actions = $access->getAvailablePermissions($section, JPERMISSION_ASSET);

		// Get the rules for this asset.
		$rules = JAccess::getAssetRules($this->_form->getValue($assetField));

		// Get the available user groups.
		$groups = $this->_getUserGroups();

		// Build the form control.
		$html = array();

		// Open the table.
		$html[] = '<table>';

		// The table heading.
		$html[] = '	<thead>';
		$html[] = '	<tr>';
		$html[] = '		<th>';
		$html[] = '			<span>'.JText::_('User Group').'</span>';
		$html[] = '		</th>';
		foreach ($actions as $action)
		{
			$html[] = '		<th>';
			$html[] = '			<span title="'.JText::_($action->description).'">'.JText::_($action->title).'</span>';
			$html[] = '		</th>';
		}
		$html[] = '	</tr>';
		$html[] = '	</thead>';

		// The table body.
		$html[] = '	<tbody>';
		foreach ($groups as $group)
		{
			$html[] = '	<tr>';
			foreach ($actions as $action)
			{
				$html[] = '		<th style="border-bottom:1px solid #ccc">';
				$html[] = '			'.$group->text;
				$html[] = '		</th>';
				$html[] = '		<td style="border-bottom:1px solid #ccc">';
				// TODO: Fix this inline style stuff...
				//$html[] = '			<fieldset class="access_rule">';
				$html[] = '				<input style="display:inline;float:none" type="radio" name="'.$this->inputName.'['.$action->name.']['.$group->value.']" id="'.$this->inputId.'_'.$action->name.'_'.$group->value.'" value=""'.($rules->allow($action->name, $group->value) === null ? ' checked="checked"' : '').' />';
				$html[] = '				<label style="float:none;clear:none" for="'.$this->inputId.'_'.$action->name.'_'.$group->value.'">'.JText::_('Inherit').'</label>';
				$html[] = '				<input style="display:inline;float:none" type="radio" name="'.$this->inputName.'['.$action->name.']['.$group->value.']" id="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_0" value="0"'.($rules->allow($action->name, $group->value) === false ? ' checked="checked"' : '').' />';
				$html[] = '				<label style="float:none;clear:none" for="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_0">'.JText::_('Deny').'</label>';
				$html[] = '				<input style="display:inline;float:none" type="radio" name="'.$this->inputName.'['.$action->name.']['.$group->value.']" id="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_1" value="1"'.($rules->allow($action->name, $group->value) === true ? ' checked="checked"' : '').' />';
				$html[] = '				<label style="float:none;clear:none" for="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_1">'.JText::_('Allow').'</label>';
				//$html[] = '			</fieldset>';
				$html[] = '		</td>';
			}
			$html[] = '	</tr>';
		}
		$html[] = '	</tbody>';

		// Close the table.
		$html[] = '</table>';

		return implode("\n", $html);
	}

	protected function _getUserGroups()
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
		for ($i=0,$n=count($options); $i < $n; $i++) {
			$options[$i]->text = str_repeat('&nbsp;',$options[$i]->level).$options[$i]->text;
		}

		return $options;
	}
}