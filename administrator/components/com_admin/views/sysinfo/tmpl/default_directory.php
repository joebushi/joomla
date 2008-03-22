<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
$cparams = JComponentHelper::getParams ('com_media');
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Directory Permissions' ); ?></legend>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="650">
					<?php echo JText::_( 'Directory' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Status' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			AdminViewSysinfo::writableCell( 'administrator/backups' );
			AdminViewSysinfo::writableCell( 'administrator/components' );
			AdminViewSysinfo::writableCell( 'administrator/language' );

			// List all admin languages
			$admin_langs = JFolder::folders(JPATH_ADMINISTRATOR.DS.'language');
			foreach ($admin_langs as $alang)
			{
				AdminViewSysinfo::writableCell( 'administrator/language/'.$alang );
			}

			AdminViewSysinfo::writableCell( 'administrator/modules' );
			AdminViewSysinfo::writableCell( 'administrator/templates' );
			AdminViewSysinfo::writableCell( 'components' );
			AdminViewSysinfo::writableCell( 'images' );
			AdminViewSysinfo::writableCell( 'images/banners' );
			AdminViewSysinfo::writableCell( $cparams->get('image_path'));
			AdminViewSysinfo::writableCell( 'language' );

			// List all site languages
			$site_langs	= JFolder::folders(JPATH_SITE.DS.'language');
			foreach ($site_langs as $slang)
			{
				AdminViewSysinfo::writableCell( 'language/'.$slang );
			}

			AdminViewSysinfo::writableCell( 'modules' );
			AdminViewSysinfo::writableCell( 'plugins' );
			AdminViewSysinfo::writableCell( 'plugins/content' );
			AdminViewSysinfo::writableCell( 'plugins/editors' );
			AdminViewSysinfo::writableCell( 'plugins/editors-xtd' );
			AdminViewSysinfo::writableCell( 'plugins/search' );
			AdminViewSysinfo::writableCell( 'plugins/system' );
			AdminViewSysinfo::writableCell( 'plugins/user' );
			AdminViewSysinfo::writableCell( 'plugins/xmlrpc' );
			AdminViewSysinfo::writableCell( 'tmp' );
			AdminViewSysinfo::writableCell( 'templates' );
			AdminViewSysinfo::writableCell( JPATH_SITE.DS.'cache', 0, '<strong>'. JText::_( 'Cache Directory' ) .'</strong> ' );
			AdminViewSysinfo::writableCell( JPATH_ADMINISTRATOR.DS.'cache', 0, '<strong>'. JText::_( 'Cache Directory' ) .'</strong> ' );
			?>
		</tbody>
		</table>
</fieldset>