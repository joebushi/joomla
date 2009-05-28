<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.fields.list');

/**
 * Supports an HTML select list of categories
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldCategoryOrdering extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @access	public
	 * @var		string
	 * @since	1.1
	 */
	var	$type = 'CategoryOrdering';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @access	protected
	 * @return	array		An array of JHtml options.
	 * @since	1.1
	 */
	function _getOptions()
	{
		// Get the database connection object.
		$db = &JFactory::getDBO();

		// Get the current parent id.
		$parent_id = $this->_form->getValue('parent_id', 1);

		// Get the category options.
		$db->setQuery(
			'SELECT node.ordering AS value, node.title AS text' .
			' FROM #__categories AS node' .
			' WHERE node.parent_id = '.(int) $parent_id .
			' ORDER BY node.left_id'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// If first is allowed, add it to the front of the list.
		if ($this->_element->attributes('allow_first') == 1) {
			array_unshift($options, JHTML::_('select.option', -1, '- '.JText::_('First').' -'));
		}

		// If last is allowed, add it to the end of the list.
		if ($this->_element->attributes('allow_last') == 1) {
			array_push($options, JHTML::_('select.option', -2, '- '.JText::_('Last').' -'));
		}

		return $options;
	}
}