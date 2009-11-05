<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.database.query');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldPositions extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'Positions';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getOptions()
	{
		$clientId = 0;
		$client	= JApplicationHelper::getClientInfo($clientId);
		$db		= &JFactory::getDbo();
		$query	= new JQuery;

		// template assignment filter
		$query->select('DISTINCT a.template')
			->from('#__template_styles AS a')
			->where('a.client_id = '.$clientId);

		$db->setQuery($query);
		$templates = $db->loadResultArray();

		// Get a list of all module positions as set in the database
		$query->select('DISTINCT a.position')
			->from('#____modules AS a')
			->where('a.client_id = '.$clientId);

		$db->setQuery($query);
		$positions = $db->loadResultArray();

		// Get a list of all template xml files for a given application
		$xml = &JFactory::getXMLParser('Simple');
		foreach ($templates as $template) {
			$path = $client->path.DS.'templates'.DS.$template.DS.'templateDetails.xml';
			if ($xml->loadFile($path))
			{
				$p = &$xml->document->getElementByPath('positions');
				if ($p INSTANCEOF JSimpleXMLElement && count($p->children()))
				{
					foreach ($p->children() as $child)
					{
						if (!in_array($child->data(), $positions)) {
							$positions[] = $child->data();
						}
					}
				}
			}
		}

		$positions = array_unique($positions);
		sort($positions);

		// Merge any additional options in the XML definition.
		$positions = array_merge(parent::_getOptions(), $positions);

		return $positions;
	}
}