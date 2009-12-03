<?php
/**
 * @version		$Id: blog.php 12296 2009-06-22 11:14:59Z pentacle $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'); // think this is missing, copied from default.php, might be covered from there

$cparams =& JComponentHelper::getParams('com_media');

?>

<div class="multi-article<?php echo $this->params->get('pageclass_sfx');?>">

<?php if ($this->params->get('show_page_title', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_title')); ?>
</h1>
<?php endif; ?>

<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
<div class="description">
	<?php if ($this->params->get('show_description_image') && $this->category->image) : ?>
		<img src="<?php echo $this->baseurl . '/' . $cparams->get('image_path') . '/'. $this->category->image;?>"   alt="" />
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo $this->category->description; ?>
	<?php endif; ?>
</div>
<?php endif; ?>

<!--  SubCategory Listing -->
	<ul>
	<?php
	foreach($this->children as $child)
	{
		//echo '<li><a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)).'">'.$child->title.'</a> ('.$child->numitems.')</li>';

	}
	?>
	</ul>

<!-- Leading Articles -->
<?php if ($this->params->get('num_leading_articles')) : ?>
		<div class="leading-row">
			<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
				<?php if ($i >= $this->total) : break; endif; ?>
					<div class="leading">
						<?php
							$this->item = &$this->getItem($i, $this->params);
							echo $this->loadTemplate('item');
						?>
					</div>
					<!-- <span class="leading-separator">&nbsp;</span> -->
			<?php endfor; ?>
		</div><!-- end leading-row -->
<?php else : $i = $this->pagination->limitstart; endif; ?>

<!-- Intro'd Articles -->
<?php
$numIntroArticles = $this->params->def('num_intro_articles', 4);
	if ($numIntroArticles) :
		$colCount = $this->params->def('num_columns', 2);
		if ($colCount == 0) :
			$colCount = 1;
		endif;
		$rowCount = (int) $numIntroArticles / $colCount;
		$ii = 0;
		for ($y = 0; $y < $rowCount && $i < $this->total; $y++) : ?>
			<div class="article-row">
				<?php for ($z = 0; $z < $colCount && $ii < $numIntroArticles && $i < $this->total; $z++, $i++, $ii++) : ?>
					<div class="article-column column<?php echo $z + 1; ?> cols<?php echo $colCount; ?>" >
						<?php
							$this->item = &$this->getItem($i, $this->params);
							echo $this->loadTemplate('item');
						?>
					</div>
					<!--  <span class="article-separator">&nbsp;</span> -->
				<?php endfor; ?>
				<!-- <span class="row-separator">&nbsp;</span> -->
			</div>
		<?php endfor;
	endif; ?>

<!-- Links Articles -->
<?php if ($this->params->get('num_links') && ($i < $this->total)) : ?>
		<div class="blog_more">
			<?php
				$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
				echo $this->loadTemplate('links');
			?>
		</div>
<?php endif; ?>

<!--  Pagination -->
<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">
		<?php if ($this->pagination->get('pages.total') > 1) : ?>
		<p class="results">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
		<?php endif; ?>

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php endif; ?>
	<?php endif; ?>


</div><!--  end multi-article -->