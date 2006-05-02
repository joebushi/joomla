<?php
/**
* @version $Id: separator.menu.html.php,v 1.1 2005/08/25 14:14:33 johanjanssens Exp $
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
class separator_menu_html {

	function edit( $menu, $lists, $params, $option ) {
		global $_LANG;

		mosCommonHTML::loadOverlib();
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			submitform( pressbutton );
		}
		</script>
		<?php
		mosMenuFactory::formStart( 'Separator / Placeholder' );

		mosMenuFactory::tableStart();
		mosMenuFactory::formElementName( $menu->name, $_LANG->_( 'Pattern/Name' ) );

		mosMenuFactory::formElement( $lists['parent'], 		'PAR' );
		mosMenuFactory::formElement( $lists['ordering'], 	'ORD' );
		mosMenuFactory::formElement( $lists['access'], 		'ACC' );
		mosMenuFactory::formElement( $lists['published'], 	'PUB' );
		mosMenuFactory::tableEnd();

		mosMenuFactory::formParams( $params, 1 );
		?>
		<input type="hidden" name="link" value="" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="browserNav" value="3" />
		<?php
		mosMenuFactory::formElementHdden( $menu, $option );
	}
}
?>