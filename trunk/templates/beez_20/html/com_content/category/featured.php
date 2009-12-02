<?php
/**
 * @version		$Id: featured.php 12450 2009-07-05 03:45:24Z eddieajau changed a.radtke  $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
// no direct access

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
$pageClass = $this->params->get('pageclass_sfx');
?>

<section class="jarticles-featured<?php echo $pageClass;?>">

<?php if ($this->params->get('show_page_title', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_title')); ?>
</h1>
<?php endif; ?>
<?php $i=0; ?>
<?php if (!empty($this->lead_items)) : ?>
<div class="leading">
        <?php foreach ($this->lead_items as &$item) : ?>
        <article class="leading-item <?php echo $item->state == 0 ? 'class="system-unpublished"' : null; ?>">
                <?php
                        $this->item = &$item;
                             $i++;
                        echo $this->loadTemplate('item');
                ?>
        </article>
        <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($this->intro_items)) : ?>


        <?php
        	$introcount = $this->params->def('num_intro_articles', 4);
        	$realintrocounter = count($this->intro_items);
        	if ($realintrocounter < $introcount) $introcount = $realintrocounter;

        	if ($introcount) :
                $colcount = $this->params->def('num_columns', 2);

                if ($colcount == 0) :
                        $colcount = 1;
                endif;

                $rowcount = (int) (($introcount / $colcount) + 0.5);

                for ($y = 0; $y < $rowcount; $y++) : ?>
                	<div class="article_row<?php echo $this->escape($this->params->get('pageclass_sfx'));  echo ' row'.$y ;?>">
                    <?php for ($z = 0; $z < $colcount ; $z++  ) : ?>
                    		<?php
                    				if (isset($this->intro_items[$i])) :?>
                    					<article class="article_column column<?php echo $z + 1; ?> cols<?php echo $colcount; ?>" >
                                     	<?php
									  		$this->item=$this->intro_items[$i];
									  		$i++;
                                      		echo $this->loadTemplate('item');
                                     	?>
                                     	</article>
                                     	<span class="article_separator">&nbsp;</span>
                                     <?php else :  ?>

                                     <?php endif;  ?>

                                <?php endfor; ?>
                                <span class="row_separator<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">&nbsp;</span>
                        </div>
                <?php endfor;
        endif; ?>
<?php endif; ?>

<?php if (!empty($this->link_items)) : ?>




     <?php    echo $this->loadTemplate('links');


        ?>

<?php endif; ?>
<?php echo $this->pagination ; ?>

<?php  /* if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">


		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif;  */ ?>

</section>

