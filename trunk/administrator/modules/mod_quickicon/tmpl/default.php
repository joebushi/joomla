<?php defined('_JEXEC') or die; ?>
<div id="cpanel">
<?php
$lang = &JFactory::getLanguage();
$lang->load('com_quickicons');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_quickicons'.DS.'helpers'.DS.'html');
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
?>
</div>
