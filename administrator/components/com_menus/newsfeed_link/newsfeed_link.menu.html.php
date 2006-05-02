<?php
/**
* @version $Id: newsfeed_link.menu.html.php,v 1.1 2005/08/25 14:14:33 johanjanssens Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* Disaply newsfeed item link
* @package Mambo
* @subpackage Menus
*/
class newsfeed_link_menu_html {

	function edit( &$menu, &$lists, &$params, $option, $newsfeed ) {
		global $_LANG;

		mosCommonHTML::loadOverlib();
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (trim(form.name.value) == ""){
				alert( "<?php echo $_LANG->_( 'Link must have a name' ); ?>" );
			} else if (trim(form.newsfeed_link.value) == ""){
				alert( "<?php echo $_LANG->_( 'You must select a Newsfeed to link to' ); ?>" );
			} else {
				form.link.value = "index.php?option=com_newsfeeds&task=view&feedid=" + form.newsfeed_link.value;
				form.componentid.value = form.newsfeed_link.value;
				submitform( pressbutton );
			}
		}
		</script>
		<?php
		mosMenuFactory::formStart( 'Link - Newsfeed' );
		
		mosMenuFactory::tableStart();
		mosMenuFactory::formElementName( $menu->name );
		
		mosMenuFactory::formElement( $lists['newsfeed'],	$_LANG->_( 'Newsfeed to Link' ) );

		mosMenuFactory::formElement( $lists['link'], 		'URL' );
		mosMenuFactory::formElement( $lists['target'], 		'TAR' );
		mosMenuFactory::formElement( $lists['parent'], 		'PAR' );
		mosMenuFactory::formElement( $lists['ordering'], 	'ORD' );
		mosMenuFactory::formElement( $lists['access'], 		'ACC' );
		mosMenuFactory::formElement( $lists['published'], 	'PUB' );
		mosMenuFactory::tableEnd();
		
		mosMenuFactory::formParams( $params );
		?>
		<input type="hidden" name="componentid" value="" />
		<input type="hidden" name="link" value="" />
		<?php
		mosMenuFactory::formElementHdden( $menu, $option );
	}
}
?>