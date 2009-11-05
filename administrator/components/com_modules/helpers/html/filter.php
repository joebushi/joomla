<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Utility class for com_modules
 *
 * @static
 * @package 	Joomla
 * @subpackage	com_modules
 * @since		1.6
 */

abstract class JHtmlFilter
{
	public static function assigned($client, $selected = null)
	{
		jimport('joomla.database.query');
		$db		= &JFactory::getDbo();
		$query 	= new JQuery;
		$query->select('CONCAT(a.template," -", a.title) AS text, a.id AS value')
			->from('#__template_styles AS a')
			->where('a.client_id = '.(int) $client->id);
		$db->setQuery($query);

		$assigned[]	= JHtml::_('select.option',  '0', '- '.JText::_('Select Template').' -');
		$assigned 	= array_merge($assigned, $db->loadObjectList());

		return JHtml::_(
			'select.genericlist',
			$assigned,
			'filter_assigned',
			array(
				'list.attr' => 'class="inputbox" size="1" onchange="this.form.submit()"',
				'list.select' => $selected
			)
		);
	}

	public static function position($client, $selected = null)
	{
		jimport('joomla.database.query');
		$db		= &JFactory::getDbo();
		$query 	= new JQuery;
		$query->select('a.position AS text, a.position AS value')
			->from('#__modules AS a')
			->where('a.client_id = '.(int) $client->id)
			->group('a.position')
			->order('a.position');
		$db->setQuery($query);

		$positions[] 	= JHtml::_('select.option',  '0', '- '. JText::_('Select Position') .' -');
		$positions 		= array_merge($positions, $db->loadObjectList());

		return JHtml::_(
			'select.genericlist',
			$positions,
			'filter_position',
			array(
				'list.attr' => 'class="inputbox" size="1" onchange="this.form.submit()"',
				'list.select' => $selected
			)
		);
	}

	public static function type($client, $selected = null)
	{
		jimport('joomla.database.query');
		$db		= &JFactory::getDbo();
		$query 	= new JQuery;
		$query->select('a.module AS text, a.module AS value')
			->from('#__modules AS a')
			->where('a.client_id = '.(int) $client->id)
			->group('a.module')
			->order('a.module');
		$db->setQuery($query);

		$types[] 	= JHtml::_('select.option',  '0', '- '. JText::_('Select Type') .' -');
		$types 		= array_merge($types, $db->loadObjectList());

		return JHtml::_(
			'select.genericlist',
			$types,
			'filter_type',
			array(
				'list.attr' => 'class="inputbox" size="1" onchange="this.form.submit()"',
				'list.select' => $selected
			)
		);
	}
}
