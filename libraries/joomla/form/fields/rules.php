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

		// Get the available user groups.
		$groups = $this->_getUserGroups();

		// Build the form control.
		$html = array();
		foreach ($actions as $action)
		{
			$html[] = '<fieldset>';
			$html[] = '	<legend>'.$action->title.'</legend>';
			$html[] = '	<p>'.$action->description.'</p>';

			foreach ($groups as $group)
			{
				// TODO: Fix this horrid inline style crap... just trying to get by :)
				$html[] = '<label style="float:none;clear:none" for="'.$this->inputId.'_'.$action->name.'_'.$group->value.'">Inherit</label>';
				$html[] = '<input style="display:inline;float:none" type="radio" name="'.$this->inputName.'['.$action->name.']['.$group->value.']" id="'.$this->inputId.'_'.$action->name.'_'.$group->value.'" value="" />';
				$html[] = '<label style="float:none;clear:none" for="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_0">Deny</label>';
				$html[] = '<input style="display:inline;float:none" type="radio" name="'.$this->inputName.'['.$action->name.']['.$group->value.']" id="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_0" value="0" />';
				$html[] = '<label style="float:none;clear:none" for="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_1">Allow</label>';
				$html[] = '<input style="display:inline;float:none" type="radio" name="'.$this->inputName.'['.$action->name.']['.$group->value.']" id="'.$this->inputId.'_'.$action->name.'_'.$group->value.'_1" value="1" />';
				$html[] = '<br />';
			}

			$html[] = '</fieldset>';
		}

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
			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		return $options;
	}
}