<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	Installation
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

$languages	=& $this->languages;

JRequest::setVar('step', 'lang');
JRequest::setVar('next', 'preinstall');

?>

<script language="JavaScript" type="text/javascript">
	function validateForm( frm, task ) {
		submitForm( frm, task );
	}
</script>

<form action="index.php" method="post" name="adminForm">

	<h2><?php echo JText::_('Select Language') ?></h2>
	<div class="install-text">
		<?php echo JText::_('PICKYOURCHOICEOFLANGS') ?>
	</div>
	<div class="install-body">
		<div class="t">
			<div class="t">
				<div class="t"></div>
			</div>
		</div>
		<div class="m">
			<fieldset>
				<select name="vars[lang]" class="inputbox" size="20">
					<?php foreach ( $languages as $language ) : ?>
					<option value="<?php echo $language['value'] ?>" <?php echo @ $language['selected'] ?>><?php echo $language['value'] ?> - <?php echo $language['text'] ?></option>
					<?php endforeach; ?>
				</select>
			</fieldset>
		</div>
		<div class="b">
			<div class="b">
				<div class="b"></div>
			</div>
  		</div>
		<div class="clr"></div>
	</div>
			
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
</form>
