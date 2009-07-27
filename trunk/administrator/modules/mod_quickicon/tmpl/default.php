<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	mod_quickicon
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>

<div id="cpanel">
<?php
$lang = &JFactory::getLanguage();
$lang->load('com_quickicons');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_quickicons'.DS.'helpers'.DS.'html');

if (count($sections)==1):
	echo JHtml::_('quickicons.quickicons',$sections[0]->key);
elseif (!empty($sections)):
	jimport('joomla.html.pane');
	
	$pane = &JPane::getInstance('sliders',array('name'=>'quickicons'));

	echo $pane->startPane('quickicons-pane');

		foreach($sections as $section):
			$html = JHtml::_('quickicons.quickicons',$section->key);
			if (!empty($html)):
				echo $pane->startPanel(JText::_($section->name), 'quickicons-panel-'.$section->key);
					echo $html;
				echo $pane->endPanel();
			endif;
		endforeach;

	echo $pane->endPane();
endif;
?>
</div>
