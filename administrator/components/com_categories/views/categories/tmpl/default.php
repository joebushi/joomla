<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	//Ordering allowed ?
	$ordering = ($this->filter->order == 'c.ordering');

	JHTML::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_categories&amp;section=' . $this->filter->section); ?>" method="post" name="adminForm">

<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->filter->search;?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php
			if ( $this->filter->section == 'com_content') {
				echo JHTML::_('list.section',  'sectionid', $filter->sectionid, 'onchange="document.adminForm.submit();"' );
			}
			?>
			<?php
			echo JHTML::_('grid.state', $this->filter->state );
			?>
		</td>
	</tr>
</table>

<table class="adminlist">
<thead>
	<tr>
		<th width="10" align="left">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" />
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort',   'Title', 'c.title', @$this->filter->order_Dir, @$this->filter->order ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort',   'Published', 'c.published', @$this->filter->order_Dir, @$this->filter->order ); ?>
		</th>
		<th width="10%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort',   'Order by', 'c.ordering', @$this->filter->order_Dir, @$this->filter->order ); ?>
			<?php echo JHTML::_('grid.order',  $this->rows ); ?>
		</th>
		<th width="7%">
			<?php echo JHTML::_('grid.sort',   'Access', 'groupname', @$this->filter->order_Dir, @$this->filter->order ); ?>
		</th>
		<?php
		if ( $this->filter->section == 'com_content') {
			?>
			<th width="20%"  class="title">
				<?php echo JHTML::_('grid.sort',   'Section', 'section_name', @$this->filter->order_Dir, @$this->filter->order ); ?>
			</th>
			<?php
		}
		?>
		<?php
		if ( $this->type == 'content') {
			?>
			<th width="5%">
				<?php echo JText::_( 'Num Active' ); ?>
			</th>
			<th width="5%">
				<?php echo JText::_( 'Num Trash' ); ?>
			</th>
			<?php
		}
		?>
		<th width="1%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort',   'ID', 'c.id', @$this->filter->order_Dir, @$this->filter->order ); ?>
		</th>
	</tr>
</thead>
<tfoot>
<tr>
	<td colspan="13">
		<?php echo $this->pagination->getListFooter(); ?>
	</td>
</tr>
</tfoot>
<tbody>
<?php
$k = 0;
if( count( $this->rows ) ) {
$this->renderCats($this->rows, 0, 0);
} else {
	if( $this->type == 'content') {
		?>
		<tr><td colspan="10"><?php echo JText::_('There are no Categories'); ?></td></tr>
		<?php
	} else {
		?>
		<tr><td colspan="8"><?php echo JText::_('There are no Categories'); ?></td></tr>
		<?php
	}
}
?>
</tbody>
</table>

<input type="hidden" name="option" value="com_categories" />
<input type="hidden" name="section" value="<?php echo $this->filter->section;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="chosen" value="" />
<input type="hidden" name="act" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="filter_order" value="<?php echo $this->filter->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter->order_Dir; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
