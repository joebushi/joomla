<?php
/**
 * @version                $Id: default.php 12296 2009-06-22 11:14:59Z pentacle changed a.radtke  $
 * @package                Joomla.Site
 * @subpackage        com_content
 * @copyright        Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 * @modified Angie Radtke
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');

// If the page class is defined, wrap the whole output in a div.
$pageClass = $this->params->get('pageclass_sfx');
?>
<?php if ($pageClass) : ?>
<div class="<?php echo $pageClass;?>">
<?php endif;?>
<section>
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
        <?php echo $this->loadTemplate('links');?>
<?php endif; ?>


<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
        <div class="pagination">


         <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
                <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>

        <?php endif; ?>
<?php endif; ?>

<?php if ($pageClass) : ?>
</div>
<?php endif;?>

</section>



<?php
/*
echo "#<div style='text-align:left;font_size:1.2em;'><pre>";
print_r($this->intro_items);
echo "</pre></div>#";
*/