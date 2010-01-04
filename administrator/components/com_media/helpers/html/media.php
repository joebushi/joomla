<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 */
class JHtmlMedia
{
	/**
	 * Display the flash uploader.
	 */
	public static function flash($fileTypes)
	{
		//$fileTypes = $config->get('image_extensions', 'bmp,gif,jpg,png,jpeg');
		$types = explode(',', $fileTypes);
		$displayTypes = '';		// this is what the user sees
		$filterTypes = '';		// this is what controls the logic
		$firstType = true;
		foreach ($types AS $type) {
			if (!$firstType) {
				$displayTypes .= ', ';
				$filterTypes .= '; ';
			} else {
				$firstType = false;
			}
			$displayTypes .= '*.'.$type;
			$filterTypes .= '*.'.$type;
		}
		$typeString = '{ \'Images ('.$displayTypes.')\': \''.$filterTypes.'\' }';

		JHtml::_('behavior.uploader', 'upload-flash',
			array(
				'onComplete' => 'function(){ MediaManager.refreshFrame(); }',
				'targetURL' => '\\$(\'uploadForm\').action',
				'typeFilter' => $typeString
			)
		);
	}
}