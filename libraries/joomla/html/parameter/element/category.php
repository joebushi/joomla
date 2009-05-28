<?php
/**
 * @version		$Id: category.php 11845 2009-05-27 23:28:59Z robs $
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Renders a category element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementCategory extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $_name = 'Category';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		jimport('joomla.database.query');

		$db = &JFactory::getDbo();

		$section	= $node->attributes('section');
		$class		= $node->attributes('class');
		if (!$class) {
			$class = "inputbox";
		}

		// Build the query.
		$query = new JQuery;
		$query->select('c.title AS text, c.category_id AS value');
		$query->select('COUNT(DISTINCT c3.category_id) AS level');
		$query->from('#__categories AS c');
		$query->join('LEFT OUTER', '`#__categories` AS c3 ON c3.left_id < c.left_id AND c3.right_id > c.right_id');
		$query->group('c.category_id');
		//$query->where('c.published = 1');
		$query->order('c.left_id ASC');

		$db = &JFactory::getDBO();
		$db->setQuery($query->toString());

		$options = $db->loadObjectList();

		for ($i = 0, $n = count($options); $i < $n; $i++){
			$options[$i]->text = str_repeat('&nbsp;&nbsp;',$options[$i]->level).$options[$i]->text;
		}

		array_unshift($options, JHtml::_('select.option', '', '- None -'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}