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

// get date parameter
$dateHeading = '';
$showDate = 0;
$dateFormat = JText::_('DATE_FORMAT_LC4');
switch ($this->params->get('show_date')) 
{
	case 'created':
		$dateHeading = 'CONTENT_CREATED_DATE';
		break;
	case 'modified': 
		$dateHeading = 'CONTENT_MODIFIED_DATE';
		break;
	case 'published': 
		$dateHeading = 'CONTENT_PUBLISHED_DATE'; 
		break;
}
if ($this->params->get('date_format')) {
	$dateFormat = $this->escape($this->params->get('date_format'));
}

// get filter parameter
$filterText = '';
switch ($this->params->get('filter_field'))
{
	case 'hits':
		$filterText = 'CONTENT_HITS_FILTER_LABEL';
		break;
	case 'title': 
		$filterText = 'CONTENT_TITLE_FILTER_LABEL';
		break;
	case 'author': 
		$filterText = 'CONTENT_AUTHOR_FILTER_LABEL';
		break;
}
?>
<?php if (empty($this->articles)) : ?>
	<!--  no articles -->
<?php else : ?>
	<h5>Article Links</h5>
	<?php if ($filterText) :?>
		<p><?php echo JText::_($filterText); ?></p>
	<?php endif; ?>
	<?php if ($this->params->get('show_headings')) :?>
		<p><?php echo 'headings go here'?>
		<?php if ($dateHeading) : ?>
			<?php echo JText::_($dateHeading)?></p>
		<?php endif; ?>
		<ul>
	<?php endif; ?>
		<?php foreach ($this->articles as &$article) : ?>
			<li>
				<a href="<?php echo JRoute::_(ContentRoute::article($article->slug, $article->catslug)); ?>">
					<?php echo $article->title; ?></a>
				<?php if ($this->params->get('show_date') != 'hide') : ?>
					<?php echo JHTML::_('date', $article->displayDate, $dateFormat); ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>
