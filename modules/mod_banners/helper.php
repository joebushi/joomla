<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	mod_banners
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modBannersHelper
{
	function getList(&$params)
	{
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_ROOT.'/components/com_banners/models');
		$model = &JModel::getInstance('Banners','BannersModel',array('ignore_request'=>true));
		$model->setState('filter.client_id', (int) $params->get('cid'));
		$model->setState('filter.category_id', (int) $params->get('catid'));
		$model->setState('list.limit', (int) $params->get('count', 1));
		$model->setState('list.start', 0);
		$model->setState('filter.ordering', $params->get('ordering'));
		$model->setState('filter.tag_search', $params->get('tag_search'));
		$document = &JFactory::getDocument();
		$keywords = explode(',', $document->getMetaData('keywords'));
		$model->setState('filter.keywords', $keywords);
		
		$banners = $model->getItems();
		$model->impress();
		return $banners;
	}
	function renderBanner($params, &$item)
	{
		require_once JPATH_ROOT . '/components/com_banners/helpers/check.php';
		$link = JRoute::_('index.php?option=com_banners&task=click&id='. $item->id);
		$baseurl = JURI::base();

		$html = '';		
		$parameters = new JRegistry;
		$parameters->loadJSON($item->params);
		$parameters = $parameters->toObject();
		// Custom code
		if ($item->type==1)
		{
			// template replacements
			$html = str_replace('{CLICKURL}', $link, $parameters->custom->bannercode);
			$html = str_replace('{NAME}', $item->name, $html);
		}
		// Image or flash
		else
		{
			$imageurl = $parameters->image->url;
			// Image
			if (BannersHelperCheck::isImage($imageurl))
			{
				$alt = $parameters->alt->alt;
				$alt = $alt ? $alt : $item->name ;
				$alt = $alt ? $alt : JText::_('mod_banners_Banner') ;
				
				$image 	= '<img src="'.$baseurl.'images/banners/'.$imageurl.'" alt="'.$alt.'" />';
				if ($item->clickurl)
				{
					switch ($params->get('target', 1))
					{
						// cases are slightly different
						case 1:
							// open in a new window
							$a = '<a href="'. $link .'" target="_blank">';
							break;

						case 2:
							// open in a popup window
							$a = "<a href=\"javascript:void window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\">";
							break;

						default:	// formerly case 2
							// open in parent window
							$a = '<a href="'. $link .'">';
							break;
						}

					$html = $a . $image . '</a>';
				}
				else
				{
					$html = $image;
				}
			}
			// Flash
			else if (BannersHelperCheck::isFlash($imageurl))
			{
				$width = $parameters->flash->width;
				$height = $parameters->flash->height;

				$imageurl = $baseurl."images/banners/".$imageurl;
				$html =	"<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" border=\"0\" width=\"$width\" height=\"$height\">
							<param name=\"movie\" value=\"$imageurl\"><embed src=\"$imageurl\" loop=\"false\" pluginspage=\"http://www.macromedia.com/go/get/flashplayer\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\"></embed>
						</object>";
			}
		}
		return $html;
	}
}
