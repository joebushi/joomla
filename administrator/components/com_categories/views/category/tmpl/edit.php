<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_categories/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();

// Get the form fields.
$fields	= $this->form->getFields();
?>

<script type="text/javascript">
function submitbutton(task)
{
	if (task == 'category.cancel' || document.formvalidator.isValid(document.adminForm)) {
		<?php echo $fields['description']->editor->save('JForm[description]'); ?>
		submitform(task);
	}
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_categories&view=category&layout=edit');?>" method="post" name="adminForm">
	<fieldset>
		<table class="adminform">
			<tbody>
				<tr>
					<td>
						<?php echo $fields['title']->label; ?><br />
						<?php echo $fields['title']->input; ?>
					</td>
					<td>
						<?php echo $fields['alias']->label; ?><br />
						<?php echo $fields['alias']->input; ?>
					</td>
					<td>
						<?php echo $fields['hits']->label; ?><br />
						<?php echo $fields['hits']->input; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $fields['parent_id']->label; ?><br />
						<?php echo $fields['parent_id']->input; ?>
					</td>
					<td>
						<?php echo $fields['path']->label; ?><br />
						<?php echo $fields['path']->input; ?>
					</td>
					<td>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

		<table width="100%">
			<tbody>
				<tr valign="top">
					<td width="70%">
						<label><?php echo $fields['description']->label; ?></label>
						<?php echo $fields['description']->input; ?>
					</td>
					<td width="30%">
						<fieldset>
							<legend><?php echo JText::_('Category_Fieldset_Settings');?></legend>
							<ol>
								<li>
									<?php echo $fields['published']->label; ?><br />
									<?php echo $fields['published']->input; ?>
								</li>
								<li>
									<?php echo $fields['ordering']->label; ?><br />
									<?php echo $fields['ordering']->input; ?>
								</li>
							</ol>
						</fieldset>
						<fieldset>
							<legend><?php echo JText::_('Category_Fieldset_Permissions');?></legend>
							<ol>
								<li>
									<?php echo $fields['access']->label; ?><br />
									<?php echo $fields['access']->input; ?>
								</li>
							</ol>
						</fieldset>

					</td>
				</tr>
			</tbody>
		</table>

	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<div class="clr"></div>