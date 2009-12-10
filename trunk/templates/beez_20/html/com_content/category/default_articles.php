<?php
/**
 * @version		$Id: default_articles.php 13151 2009-10-11 17:10:52Z severdia $
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
<h3>Article Links</h3>
	<ol>
		<?php foreach ($this->articles as &$article) : ?>
			<li>
				<a href="<?php echo JRoute::_(ContentRoute::article($article->slug, $article->catslug)); ?>">
					<?php echo $article->title; ?></a>
			</li>
		<?php endforeach; ?>
	</ol>

<?php endif; ?>
