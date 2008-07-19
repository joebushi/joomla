<?php
/**
* @version		$Id: $
* @package		Joomla
* @subpackage	Categories
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Categories component
 *
 * @static
 * @package		Joomla
 * @subpackage	Categories
 * @since 1.0
 */
class CategoriesViewCategories extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		// get parameters from the URL or submitted form
		$db					=& JFactory::getDBO();
		$user				=& JFactory::getUser();
	
		// Get data from the model
		$rows		= & $this->get( 'Data');
		$pagination = & $this->get( 'Pagination' );
		$type		= & $this->get( 'Type');
		$section_name	= & $this->get( 'SectionName');
		$filter		= & $this->get( 'Filter');

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Category Manager' ) .': <small><small>[ '. JText::_(JString::substr($filter->section, 4)).' ]</small></small>', 'categories.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
	
		if ( $filter->section == 'com_content' || ( $filter->section > 0 ) ) {
			JToolBarHelper::customX( 'moveselect', 'move.png', 'move_f2.png', 'Move', true );
			JToolBarHelper::customX( 'copyselect', 'copy.png', 'copy_f2.png', 'Copy', true );
		}
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::help( 'screen.categories' );

		$this->assignRef('user',		$user);
		$this->assignRef('type',		$type);
		$this->assignRef('section_name',$section_name);
		$this->assignRef('rows',		$rows);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('filter',		$filter);

		parent::display($tpl);
	}

function renderCats($rows, $level = 0, $parent = 0)
{
foreach($rows as $row) {
if($row->parent_id == $parent) {
	$row->sect_link = JRoute::_( 'index.php?option=com_sections&task=edit&cid[]='. $row->section );

	$link = 'index.php?option=com_categories&section='. $this->filter->section .'&task=edit&cid[]='. $row->id .'&type='.$this->type.(JRequest::getVar('mode') == 'tree' ? '&mode=tree':'');

	$access 	= JHTML::_('grid.access',   $row, $i );
	$checked 	= JHTML::_('grid.checkedout',   $row, $i );
	$published 	= JHTML::_('grid.published', $row, $i );
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td>
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td>
			<?php echo $checked; ?>
		</td>
		<td>
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'Title' );?>::<?php echo $row->title; ?>">
			<?php
			if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out )  ) {
				echo str_repeat('|--',$level).' '.$row->title;
			} else {
				echo str_repeat('|--',$level).' '; ?>
				<a href="<?php echo JRoute::_( $link ); ?>">
					<?php echo $row->title; ?></a>
				<?php
			}
			?></span>
		</td>
		<td align="center">
			<?php echo $published;?>
		</td>
		<td class="order">
			<span><?php echo $this->pagination->orderUpIcon( $i, ($row->section == @$this->rows[$i-1]->section), 'orderup', 'Move Up', $ordering ); ?></span>
			<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($row->section == @$this->rows[$i+1]->section), 'orderdown', 'Move Down', $ordering ); ?></span>
			<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
		</td>
		<td align="center">
			<?php echo $access;?>
		</td>
		<?php
		if ( $this->filter->section == 'com_content' ) {
			?>
			<td>
				<a href="<?php echo $row->sect_link; ?>" title="<?php echo JText::_( 'Edit Section' ); ?>">
					<?php echo $row->section_name; ?></a>
			</td>
			<?php
		}
		?>
		<?php
		if ( $this->type == 'content') {
			?>
			<td align="center">
				<?php echo $row->active; ?>
			</td>
			<td align="center">
				<?php echo $row->trash; ?>
			</td>
			<?php
		}
		$k = 1 - $k;
		?>
		<td align="center">
			<?php echo $row->id; ?>
		</td>
	</tr>
	<?php
	if(JRequest::getVar('mode', 'flat') == 'tree')
	{
		$this->renderCats($rows, $level + 1, $row->id);
	}
}
}
}
}
