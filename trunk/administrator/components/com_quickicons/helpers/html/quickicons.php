<?php
/**
 * @version		$Id: content.php 12175 2009-06-19 23:52:21Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_quickicons
 */
abstract class JHtmlQuickIcons
{
	/**
	 * @param	string $image image filename
	 * @param	string $link icon link
	 * @param	string $text icon text
	 * @param	string $title icon title
	 * @param	string $key icon alt key
	 * @param	string $access access authorization
	 * @param	string $component component
	 * @param	string $template_path template path
	 * @param	string $default_path default path
	 * @access	public
	 * @return string html code
	 */
	function button($image,$link,$text,$title,$key,$access,$component, $template_path='',$default_path='')
	{
		if (!empty($access))
		{
			if (!JFactory::getUser()->authorize($access)) {
				return '';
			}
		}
		if (!JComponentHelper::getComponent($component,true)->enabled) {
			return '';
		}
	
		$float		= JFactory::getLanguage()->isRTL() ? 'right' : 'left';
		$template	= JFactory::getApplication()->getTemplate();
		if (!empty($template_path)&&
			file_exists(JPATH_ADMINISTRATOR.DS.'templates'.DS.$template.DS.implode(DS,explode('/',$template_path)).DS.$file))
		{
			$path = 'templates/'.$template.'/'.$template_path.'/'.$file;
		}
		else
		{
			$path = $default_path;
		}
		
		$html ='<div style="float:'.$float.'">';
		$html.='<div class="icon">';
		$html.='<a href="'.JRoute::_($link).'" title="' . htmlentities($title) . '"';
		if($key) {
			$html.=' accesskey="' . $key . '"';
		}
		$html.='>';
		$html.=JHtml::_('image.administrator', $image, $path, NULL, NULL, $text);
		$html.='<span>' . $text . '</span></a>';
		$html.='</div>';
		$html.='</div>';
		return $html;
	}
	/**
	 * @param	string $skey section key
	 * @access	public
	 * @return string html code
	 */
	function quickicons($skey)
	{
		$lang = &JFactory::getLanguage();
		$lang->load('com_quickicons');

		$db = &JFactory::getDBO();
		$query = new JQuery();
		$query->select('*');
		$query->from('`#__quickicons`');
		$query->where('`skey` = '. $db->Quote($skey));
		$query->where('`published` = 1');
		$query->order('`ordering` ASC');
		$db->setQuery($query->toString());
		$icons = $db->loadObjectList();
		$html='';
		foreach($icons as $icon) {
			$html.=JHtml::_('quickicons.button',$icon->image,$icon->link,JText::_($icon->text),JText::_($icon->title),JText::_($icon->key),$icon->access,$icon->component,$icon->template_path,$icon->default_path);
		}
		return $html;
	}
}
