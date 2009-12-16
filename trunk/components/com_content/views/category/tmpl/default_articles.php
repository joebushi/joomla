<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php if (empty($this->articles)) : ?>
	<!--  no articles -->
<?php else : ?>
	<h5>Article Links</h5>
	<?php if ($this->params->get('filter_field') != 'hide') :?>
		<p><?php echo JText::_('Content_'.$this->params->get('filter_field').'_Filter_Label'); ?></p>
	<?php endif; ?>
	<?php if ($this->params->get('show_pagination_limit')) : ?>
		<p><?php echo 'Display # will go here'; ?></p>
	<?php endif; ?>
	<?php if ($this->params->get('show_headings')) :?>
		<p><?php echo 'headings go here'?>
		<?php if ($this->params->get('show_date') != 'hide') : ?>
			<?php echo JText::_('Content_'.$this->params->get('show_date').'_Date')?></p>
		<?php endif; ?>
		<ul>
	<?php endif; ?>
		<?php foreach ($this->articles as &$article) : ?>
			<li>
				<a href="<?php echo JRoute::_(ContentRoute::article($article->slug, $article->catslug)); ?>">
					<?php echo $article->title; ?></a>
				<?php if ($this->params->get('show_date') != 'hide') : ?>
					<?php echo JHTML::_('date', $article->displayDate, $this->escape(
						$this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->params->get('show_pagination')) : ?>
		<p><?php echo 'Pagination will go here'; ?></p>
	<?php endif; ?>	

<?php endif; ?>
