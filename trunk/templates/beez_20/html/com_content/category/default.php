<?php
/**
 * @version                $Id: default.php 13151 2009-10-11 17:10:52Z severdia $
 * @package                Joomla.Site
 * @subpackage        com_content
 * @copyright        Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');

$pageClass = $this->params->get('pageclass_sfx');
?>


<section class="jcategory<?php echo $pageClass;?>">
        <?php if ($this->params->get('show_page_title', 1)) : ?>
               <hgroup> <h1>
                        <?php echo $this->escape($this->params->get('page_title')); ?>
                </h1>
        <?php endif; ?>
        <h2>
                <?php echo $this->escape($this->item->title); ?>
        </h2>

          <?php if ($this->params->get('show_page_title', 1)) : ?>

          </hgroup>
          <? endif; ?>


<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) :?>

<div class="category_description">

                <?php echo $this->item->description; ?>

</div>
<?php endif; ?>


        <div class="jcat-articles">
                <?php echo $this->loadTemplate('articles'); ?>
        </div>

        <div class="jcat-siblings">
                <?php /* echo $this->loadTemplate('siblings'); */?>
        </div>

        <div class="jcat-children">
                <?php echo $this->loadTemplate('children'); ?>
        </div>

        <div class="jcat-parents">
                <?php /* echo $this->loadTemplate('parents'); */ ?>
        </div>
</section>