<?php
/**
 * @version
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
/*
 * Get the request group fields and print them out..
 */
//jimport('joomla.html.pane');
$pane = &JPane::getInstance('sliders');
		echo $pane->startPanel(JText::_('Menus_Item_Required_Parameters'), 'required');
	?>
	<fieldset class="panelform">
		<?php 	foreach ($this->paramsform->getFields('request') as $field) :

		?>
		
			<?php echo $field->label; ?>
			<?php echo $field->input; ?>
		
		<?php	endforeach;
		if (empty($field)){ 
				echo JText::_('Menus_Item_No_Required_Params');
		} 	?>
		</fieldset>
	<?php echo $pane->endPanel(); ?> 