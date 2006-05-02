<?php
/**
* @version $Id: content_blog_section.menu.html.php,v 1.1 2005/08/25 14:14:32 johanjanssens Exp $
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
class content_blog_section_html {

	function edit( &$menu, &$lists, &$params, $option ) {
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
			<?php
			if ( !$menu->id ) {
				?>
				if ( form.name.value == '' ) {
					alert( '<?php echo $_LANG->_( 'This Menu item must have a title' ); ?>' );
					return;
				} else {
					submitform( pressbutton );
				}
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
		mosMenuFactory::formStart( 'Blog - Content Section' );
		
		mosMenuFactory::tableStart();
		mosMenuFactory::formElementName( $menu->name );
		
		mosMenuFactory::formElement( $lists['sectionid'],	'SEC', $_LANG->_( 'You can select multiple Sections' ) );

		mosMenuFactory::formElement( $lists['link'], 		'URL' );
		mosMenuFactory::formElement( $lists['target'], 		'TAR' );
		mosMenuFactory::formElement( $lists['parent'], 		'PAR' );
		mosMenuFactory::formElement( $lists['ordering'], 	'ORD' );
		mosMenuFactory::formElement( $lists['access'], 		'ACC' );
		mosMenuFactory::formElement( $lists['published'], 	'PUB' );
		mosMenuFactory::tableEnd();
		
		mosMenuFactory::formParams( $params, 3 );
		?>
		<input type="hidden" name="link" value="index.php?option=com_content&task=blogsection&id=0" />
		<input type="hidden" name="componentid" value="0" />
		<?php
		mosMenuFactory::formElementHdden( $menu, $option );
	}
}
?>