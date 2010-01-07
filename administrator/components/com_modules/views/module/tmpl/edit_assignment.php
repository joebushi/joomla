<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Initiasile related data.
require_once JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php';
$menuTypes = MenusHelper::getMenuLinks();

jimport('joomla.html.pane');
$pane = &JPane::getInstance('sliders');
/* * /
echo '<pre>';
print_r($menuTypes);
echo '</pre>';
/* */
?>
		<script>
			window.addEvent('domready', function() {
				$('add').addEvent('click', function() {
					$('unassigned').getSelected().each(function(el) {
						uog = el.getParent().getProperty('label');
						aog = $('assigned').getChildren('optgroup[label='+uog+']');
						if(aog.length < 1){
							aog = new Element('optgroup', {label: uog});
							aog.inject($('assigned'));
						}else
							aog = aog[0];
							el.inject(aog);
					});
				});
				$('remove').addEvent('click', function() {
					$('assigned').getSelected().each(function(el) {
						uog = el.getParent().getProperty('label');
						aog = $('unassigned').getChildren('optgroup[label='+uog+']');
						if(aog.length < 1){
							aog = new Element('optgroup', {label: uog});
							aog.inject($('unassigned'));
						}else
							aog = aog[0];
							el.inject(aog);
					});
				});
			});
		</script>

		<style type="text/css">
			.holder	{ width:200px; float:left; }
			.holder #add,#remove	{ display:block; width:100px; border:1px solid #ccc; background:#eee; padding:10px; }
			.holder select	{ margin:0 0 10px 0; width:150px; font:12px tahoma; padding:5px; height:300px; }
			.holder option	{ padding:10px; }
		</style>


		<fieldset class="adminform">
			<legend><?php echo JText::_('Modules_Menu_Assignment'); ?></legend>
				<label id="jform_menus-lbl" class="hasTip" for="jform_menus"><?php echo JText::_('Modules_Module_Assign'); ?>:</label>

				<fieldset id="jform_menus" class="radio">
					<select name="jform[assignment]">
						<?php echo JHtml::_('select.options', ModulesHelper::getAssignmentOptions($this->item->client_id), 'value', 'text', $this->item->assignment, true);?>
					</select>

				</fieldset>

				<div class="clr"></div>

				<?php echo $pane->startPane('assignment-pane'); ?>
				<?php echo $pane->startPanel(JText::_('Menu Selection'), 'menu-selection-details'); ?>
					<div class="holder">
						<?php echo $this->lists['unassigned']; ?>
						<a id="add" href="javascript:;">add >></a>
					</div>

					<div class="holder">
						<select id="assigned" name="assigned[]" multiple="multiple">
						</select>
						<a id="remove" href="javascript:;"><< remove</a>
					</div>

					<div class="clr"></div>
				<?php echo $pane->endPanel(); ?>

				<?php echo $pane->startPanel(JText::_('Article Selection'), 'article-selection-details'); ?>
					<div class="holder">
						<?php echo $this->lists['articles_unassigned']; ?>
						<a id="add" href="javascript:;">add >></a>
					</div>

					<div class="holder">
						<select id="articles-assigned" name="articles-assigned[]" multiple="multiple">
						</select>
						<a id="remove" href="javascript:;"><< remove</a>
					</div>


					<div class="clr"></div>
				<?php echo $pane->endPanel(); ?>

				<?php echo $pane->startPanel(JText::_('Category Selection'), 'cat-selection-details'); ?>
					<div class="holder">
						<?php echo $this->lists['cat-unassigned']; ?>
						<a id="add" href="javascript:;">add >></a>
					</div>

					<div class="holder">
						<select id="cat-assigned" name="cat-assigned[]" multiple="multiple">
						</select>
						<a id="remove" href="javascript:;"><< remove</a>
					</div>


					<div class="clr"></div>
				<?php echo $pane->endPanel(); ?>
				<?php echo $pane->endPane(); ?>

		</fieldset>