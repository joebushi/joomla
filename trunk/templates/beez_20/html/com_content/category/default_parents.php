<?php
/**
 * @version		$Id: default_parents.php 13151 2009-10-11 17:10:52Z severdia $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php if (empty($this->parents)) : ?>
	no parents
<?php else : ?>
	<h3>Parents</h3>
	<ul>
		<?php foreach ($this->parents as &$item) : ?>
		<li>
			<a href="<?php echo JRoute::_(ContentRoute::category($item->slug)); ?>">
				<?php echo $this->escape($item->title); ?></a>
		</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>
