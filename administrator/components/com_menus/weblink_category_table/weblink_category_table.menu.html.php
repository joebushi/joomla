<?php
/**
* @version $Id: weblink_category_table.menu.html.php,v 1.1 2005/08/25 14:14:35 johanjanssens Exp $
* @package Mambo
* @subpackage Menus
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* Writes the edit form for new and existing content item
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
* @package Mambo
* @subpackage Menus
*/
class weblink_category_table_menu_html {

	function editCategory( &$menu, &$lists, &$params, $option ) {
		global $_LANG;

		mosCommonHTML::loadOverlib();
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if ( pressbutton == 'cancel' ) {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			<?php
			if ( !$menu->id ) {
				?>
				if ( getSelectedValue( 'adminForm', 'componentid' ) < 1 ) {
					alert( '<?php echo $_LANG->_( 'You must select a category' ); ?>' );
					return;
				}
				cat = getSelectedText( 'adminForm', 'componentid' );

				form.link.value = "index.php?option=com_weblinks&catid=" + form.componentid.value;
				if ( form.name.value == '' ) {
					form.name.value = cat;
				}
				submitform( pressbutton );
				<?php
			} else {
				?>
				if ( form.name.value == '' ) {
					alert( '<?php echo $_LANG->_( 'This Menu item must have a title' ); ?>' );
				} else {
					submitform( pressbutton );
				}
				<?php
			}
			?>
		}
		</script>
		<?php
		$tip = '';
		if ( !$menu->id ) {
			$tip = $_LANG->_( 'TIPIFLEAVEBLANKCATNAMEWILLBEUSED' );
		}
		
		mosMenuFactory::formStart( 'Table - Weblink Category' );
		
		mosMenuFactory::tableStart();
		mosMenuFactory::formElementName( $menu->name, '', $tip );

		mosMenuFactory::formElement( $lists['componentid'],	'CAT' );
		mosMenuFactory::formElement( $lists['link'], 		'URL' );
		mosMenuFactory::formElement( $lists['target'], 		'TAR' );
		mosMenuFactory::formElement( $lists['parent'], 		'PAR' );
		mosMenuFactory::formElement( $lists['ordering'], 	'ORD' );
		mosMenuFactory::formElement( $lists['access'], 		'ACC' );
		mosMenuFactory::formElement( $lists['published'], 	'PUB' );
		mosMenuFactory::tableEnd();
		
		mosMenuFactory::formParams( $params );
		?>
		<input type="hidden" name="link" value="<?php echo $menu->link; ?>" />
		<?php
		mosMenuFactory::formElementHdden( $menu, $option );
	}
}
?>