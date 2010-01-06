<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
require_once dirname(__FILE__).DS.'list.php';

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldTemplateStyle extends JFormFieldGroupedList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'TemplateStyle';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function _getGroups()
	{
		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT id, title, template' .
			' FROM #__template_styles'.
			' WHERE client_id = 0 '.
			' ORDER BY template, title'
		);
		$styles = $db->loadObjectList();

		// Pre-process into groups.
		$last		= null;
		$groups	= array();
		foreach ($styles as $style) {
			if ($style->template != $last) {
				$last = $style->template;
				$groups[$last] = array();
			}
			$groups[$last] = JHtml::_('select.option', $style->id, $style->title);
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::_getOptions(), $groups);

		return $groups;
	}
}
