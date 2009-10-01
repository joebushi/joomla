<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.database.query');

/**
 * Extended Utility class for all HTML drawing classes.
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.6
 */
abstract class JHtmlRules
{
	/**
	 * Displays a list of the available access sections
	 *
	 * @param	string	The form field name.
	 * @param	string	The name of the selected section.
	 * @param	string	Additional attributes to add to the select field.
	 * @param	boolean	True to add "All Sections" option.
	 *
	 * @return	string	The required HTML for the SELECT tag.
	 */
	public static function assetFormWidget($name, $attribs = '', $allowAll = true)
	{
		JFactory::getDocument()->addScriptDeclaration('window.addEvent(\'domready\', function(){$$(\'dl.tabs\').each(function(tabs){new JTabs(tabs);});});');
		JHtml::script('tabs.js');

		$base = JURI::base();
		$images['allow'] = '<img alt="Allow" src="'.$base.'images/admin/icon-16-allow.png" />';
		$images['deny'] = '<img alt="Deny" src="'.$base.'images/admin/icon-16-deny.png" />';
		$images['allow-i'] = '<img alt="Allow (Inherited)" src="'.$base.'images/admin/icon-16-allowinactive.png" />';
		$images['deny-i'] = '<img alt="Deny (Inherited)" src="'.$base.'images/admin/icon-16-denyinactive.png">';


		$html = array();

		$html[] = '<div id="acl-options">';
		$html[] = '	<p>Customize the rules for this Article. The Inherit column displays the parent setting and the Current column displays the computed rule based on the value of the dropdown menu next to each group.</p>';

		$html[] = '	<dl class="tabs">';

		$html[] = '		<dt>'.JText::_('Summary').'</dt>';
		$html[] = '		<dd>';
		$html[] = '			<p>Below is an overview of the access setting for this article. Click the tabs above to customize these settings by action.</p>';
		$html[] = '			<table id="aclsummary-table" summary="Below is an overview of the access setting for this article. Click the tabs above to customize these settings by action.">';
		$html[] = ' 			<caption>ACL Summary Table</caption>';
		$html[] = ' 			<tr>';
		$html[] = ' 				<th class="col1"></th>';
		$html[] = ' 				<th class="col2">'.JText::_('Create').'</th>';
		$html[] = ' 				<th class="col3">'.JText::_('Edit').'</th>';
		$html[] = ' 				<th class="col4">'.JText::_('Edit<br />State').'</th>';
		$html[] = ' 				<th class="col5">'.JText::_('Delete').'</th>';
		$html[] = ' 			</tr>';

		$html[] = ' 			<tr class="row0">';
		$html[] = ' 				<td class="col1">'.'Public'.'</td>';
		$html[] = ' 				<td class="col2">'.$images['allow'].'</td>';
		$html[] = ' 				<td class="col3">'.$images['deny'].'</td>';
		$html[] = ' 				<td class="col4">'.$images['allow-i'].'</td>';
		$html[] = ' 				<td class="col5">'.$images['deny-i'].'</td>';
		$html[] = ' 			</tr>';

		$html[] = ' 		</table>';
		$html[] = ' 	</dd>';

		$html[] = '		<dt>'.JText::_('Create').'</dt>';
		$html[] = '		<dd>';
		$html[] = '			<p>Shown below is the inherited state for <strong>create actions</strong> on this article and the calculated state based on the menu selection.</p>';
		$html[] = '			<table id="aclmodify-table" summary="Shown below is the inherited state for create actions on this article and the calculated state based on the menu selection.">';
		$html[] = ' 			<caption>ACL Create Table</caption>';
		$html[] = ' 			<tr>';
		$html[] = ' 				<th class="col1"></th>';
		$html[] = ' 				<th class="col2">'.JText::_('Inherit').'</th>';
		$html[] = ' 				<th class="col3"></th>';
		$html[] = ' 				<th class="col4">'.JText::_('Current').'</th>';
		$html[] = ' 			</tr>';

		$html[] = ' 			<tr>';
		$html[] = ' 				<td class="col1">'.'Public'.'</td>';
		$html[] = ' 				<td class="col2"><img alt="Allow (Inherited)" src="images/admin/icon-16-allowinactive.png"></td>';
		$html[] = ' 				<td class="col3">';
		$html[] = ' 					<select id="" class="inputbox" size="1" name="">';
		$html[] = ' 						<option value="">'.JText::_('Inherit').'</option>';
		$html[] = ' 						<option value="1">'.JText::_('Allow').'</option>';
		$html[] = ' 						<option value="0">'.JText::_('Deny').'</option>';
		$html[] = ' 					</select>';
		$html[] = ' 				</td>';
		$html[] = ' 				<td class="col4"><img alt="Deny" src="images/admin/icon-16-deny.png"></td>';
		$html[] = ' 			</tr>';

		$html[] = ' 		</table>';
		$html[] = ' 	</dd>';

		$html[] = ' </dl>';

		// Build the footer with legend and special purpose buttons.
		$html[] = '	<div class="clr"></div>';
		$html[] = '	<div id="acllegend">';
		$html[] = '		<img alt="Allow" src="images/admin/icon-16-allow.png">';
		$html[] = '		<span>Allowed</span>';
		$html[] = '		<img alt="Deny" src="images/admin/icon-16-deny.png">';
		$html[] = '		<span>Denied</span>';
		$html[] = '	</div>';
		$html[] = '	<div id="acleditgroups">';
		$html[] = '		<p><a href="#">Edit Groups</a></p>';
		$html[] = '	</div>';
		$html[] = '	<div id="aclresetbtn">';
		$html[] = '		<p><a href="#">Reset to Inherit</a></p>';
		$html[] = '	</div>';

		$html[] = '</div>';

		return implode("\n", $html);
	}
}