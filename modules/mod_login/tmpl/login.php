<?php /** $Id$ */ defined('_JEXEC') or die('Restricted Access'); ?>
<?php if (JPluginHelper::isEnabled('authentication', 'openid')) :
		JHtml::_('behavior.mootools');
		JHtml::_('script', 'openid.js');
		$langScript = 'window.addEvent("domready", function() { new JOpenID("form-login"); });';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration($langScript);

		$lang = &JFactory::getLanguage();
		$lang->load('plg_authentication_openid', JPATH_ADMINISTRATOR);
		JText::script('WHAT_IS_OPENID');
		JText::script('LOGIN_WITH_OPENID');
		JText::script('NORMAL_LOGIN');
endif; ?>

<form action="<?php echo JRoute::_( 'index.php?option=com_members&task=member.login', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
	<?php echo $params->get('pretext'); ?>
	<?php foreach ($form->getGroups() as $group): ?>
	<fieldset>
		<?php foreach ($form->getFields($group, $group) as $name => $field): ?>
			<?php if (!$field->hidden): ?>
				<dt><?php echo $field->label; ?></dt>
				<dd><?php echo $field->input; ?></dd>
			<?php endif; ?>
		<?php endforeach; ?>
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN') ?>" />
	</fieldset>
	<?php endforeach; ?>

	<input type="hidden" name="option" value="com_members" />
	<input type="hidden" name="task" value="member.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>

<ul>
	<li>
		<a href="<?php echo JRoute::_( 'index.php?option=com_members&view=reset' ); ?>">
		<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
	</li>
	<li>
		<a href="<?php echo JRoute::_( 'index.php?option=com_members&view=remind' ); ?>">
		<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
	</li>
	<?php
	$usersConfig = &JComponentHelper::getParams('com_members');
	if ($usersConfig->get('allowUserRegistration')) : ?>
	<li>
		<a href="<?php echo JRoute::_( 'index.php?option=com_members&view=registration' ); ?>">
			<?php echo JText::_('REGISTER'); ?></a>
	</li>
	<?php endif; ?>
</ul>
<?php echo $params->get('posttext'); ?>
