<?php
/**
 * @version		$Id: categories.php 11845 2009-05-27 23:28:59Z robs $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
require_once dirname(__FILE__).DS.'list.php';

/**
 * Supports an HTML select list of categories
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldCategories extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Categories';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
	{
		// TODO: Support all the required attributes

		$db			= &JFactory::getDbo();
		$extension	= $this->_element->attributes('extension');
		$published	= $this->_element->attributes('published');
		$allowNone	= $this->_element->attributes('allow_none');

		if ($published === '') {
			$published = null;
		}

		// Get the database connection object.
		$db = &JFactory::getDBO();

		// Get the category options.
		$db->setQuery(
			'SELECT node.id AS value, node.title AS text, (COUNT(node.parent_id) - 1) AS level' .
			' FROM #__categories AS node' .
			' INNER JOIN #__categories AS parent ON node.left_id BETWEEN parent.left_id AND parent.right_id' .
			' WHERE node.id > 1' .
			' GROUP BY node.id' .
			' ORDER BY node.left_id'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Pad out the text strings based on the item level.
		foreach ($options as $i => $option) {
			$options[$i]->text = str_pad($option->text, strlen($option->text) + 2*($option->level - 1), '- ', STR_PAD_LEFT);
		}

		// If none is allowed, add it to the front of the list.
		if ($this->_element->attributes('allow_none') == 1) {
			array_unshift($options, JHTML::_('select.option', 0, '- None -'));
		}

		array_unshift($options, JHtml::_('select.option', 1, 'ROOT'));

		return $options;
	}
}