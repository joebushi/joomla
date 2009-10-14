<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
require_once dirname(__FILE__).DS.'list.php';

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldTimezones extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Timezones';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
	{
		if (strlen($this->value) == 0) {
			$conf = &JFactory::getConfig();
			$value = $conf->getValue('config.offset');
		}

		// LOCALE SETTINGS
		$options = array (
			JHtml::_('select.option', -12, JText::_('JFIELD_TIMEZONE_UTC-12:00')),
			JHtml::_('select.option', -11, JText::_('JFIELD_TIMEZONE_UTC-11:00')),
			JHtml::_('select.option', -10, JText::_('JFIELD_TIMEZONE_UTC-10:00')),
			JHtml::_('select.option', -9.5, JText::_('JFIELD_TIMEZONE_UTC-09:30')),
			JHtml::_('select.option', -9, JText::_('JFIELD_TIMEZONE_UTC-09:00')),
			JHtml::_('select.option', -8, JText::_('JFIELD_TIMEZONE_UTC-08:00')),
			JHtml::_('select.option', -7, JText::_('JFIELD_TIMEZONE_UTC-07:00')),
			JHtml::_('select.option', -6, JText::_('JFIELD_TIMEZONE_UTC-06:00')),
			JHtml::_('select.option', -5, JText::_('JFIELD_TIMEZONE_UTC-05:00')),
			JHtml::_('select.option', -4, JText::_('JFIELD_TIMEZONE_UTC-04:00')),
			JHtml::_('select.option', -4.5, JText::_('JFIELD_TIMEZONE_UTC-04:30')),
			JHtml::_('select.option', -3.5, JText::_('JFIELD_TIMEZONE_UTC-03:30')),
			JHtml::_('select.option', -3, JText::_('JFIELD_TIMEZONE_UTC-03:00')),
			JHtml::_('select.option', -2, JText::_('JFIELD_TIMEZONE_UTC-02:00')),
			JHtml::_('select.option', -1, JText::_('JFIELD_TIMEZONE_UTC-01:00')),
			JHtml::_('select.option', 0, JText::_('JFIELD_TIMEZONE_UTC00:00')),
			JHtml::_('select.option', 1, JText::_('JFIELD_TIMEZONE_UTC+01:00')),
			JHtml::_('select.option', 2, JText::_('JFIELD_TIMEZONE_UTC+02:00')),
			JHtml::_('select.option', 3, JText::_('JFIELD_TIMEZONE_UTC+03:00')),
			JHtml::_('select.option', 3.5, JText::_('JFIELD_TIMEZONE_UTC+03:30')),
			JHtml::_('select.option', 4, JText::_('JFIELD_TIMEZONE_UTC+04:00')),
			JHtml::_('select.option', 4.5, JText::_('JFIELD_TIMEZONE_UTC+04:30')),
			JHtml::_('select.option', 5, JText::_('JFIELD_TIMEZONE_UTC+05:00')),
			JHtml::_('select.option', 5.5, JText::_('JFIELD_TIMEZONE_UTC+05:30')),
			JHtml::_('select.option', 5.75, JText::_('JFIELD_TIMEZONE_UTC+05:45')),
			JHtml::_('select.option', 6, JText::_('JFIELD_TIMEZONE_UTC+06:00')),
			JHtml::_('select.option', 6.30, JText::_('JFIELD_TIMEZONE_UTC+06:30')),
			JHtml::_('select.option', 7, JText::_('JFIELD_TIMEZONE_UTC+07:00')),
			JHtml::_('select.option', 8, JText::_('JFIELD_TIMEZONE_UTC+08:00')),
			JHtml::_('select.option', 8.75, JText::_('JFIELD_TIMEZONE_UTC+08:00')),
			JHtml::_('select.option', 9, JText::_('JFIELD_TIMEZONE_UTC+09:00')),
			JHtml::_('select.option', 9.5, JText::_('JFIELD_TIMEZONE_UTC+09:30')),
			JHtml::_('select.option', 10, JText::_('JFIELD_TIMEZONE_UTC+10:00')),
			JHtml::_('select.option', 10.5, JText::_('JFIELD_TIMEZONE_UTC+10:30')),
			JHtml::_('select.option', 11, JText::_('JFIELD_TIMEZONE_UTC+11:00')),
			JHtml::_('select.option', 11.30, JText::_('JFIELD_TIMEZONE_UTC+11:30')),
			JHtml::_('select.option', 12, JText::_('JFIELD_TIMEZONE_UTC+12:00')),
			JHtml::_('select.option', 12.75, JText::_('JFIELD_TIMEZONE_UTC+12:45')),
			JHtml::_('select.option', 13, JText::_('JFIELD_TIMEZONE_UTC+13:00')),
			JHtml::_('select.option', 14, JText::_('JFIELD_TIMEZONE_UTC+14:00')),
		);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::_getOptions(), $options);

		return $options;
	}
}