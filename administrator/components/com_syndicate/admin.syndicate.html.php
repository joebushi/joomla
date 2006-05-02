<?php
/**
* @version $Id: admin.syndicate.html.php,v 1.1 2005/08/25 14:14:53 johanjanssens Exp $
* @package Mambo
* @subpackage Syndicate
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 * @subpackage Statistics
 */
class syndicateScreens {
	/**
	 * Static method to create the template object
	 * @param array An array of other standard files to include
	 * @return patTemplate
	 */
	function &createTemplate( $files=null) {
		
		$tmpl =& mosFactory::getPatTemplate( $files );
		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl' );

		return $tmpl;
	}

	/**
	* List languages
	* @param array
	*/
	function view() {
		global $mosConfig_lang;

		$tmpl =& syndicateScreens::createTemplate();
		
		$tmpl->readTemplatesFromInput( 'view.html' );
		
		//$tmpl->addVar( 'body2', 'client', $lists['client'] );
				
		$tmpl->displayParsedTemplate( 'body2' );
	}
}

/**
* @package Mambo
* @subpackage Syndicate
*/
class HTML_syndicate {
	
	function settings( $option, &$params, $id ) {
		global $mosConfig_live_site;
  		global $_LANG;

		mosCommonHTML::loadOverlib();
		?>
		<style type="text/css">
		table.paramlist {
			width: 100%;
		}
		table.paramlist td {
			height: 35px;
			padding-left: 10px;
			vertical-align: middle;
		}
		table.paramlist td.column1 {
			width: 200px;
		}
		</style>

		<form action="index2.php" method="post" name="adminForm">

		<?php 
		syndicateScreens::view();
		?>
			<table class="adminform">
			<thead>
			<tr>
				<th>
					<?php echo $_LANG->_( 'Parameters' ); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th>
				</th>
			</tr>
			</tfoot>
			<tr>
				<td>
					<?php
					echo $params->render( 'params', 0 );
					?>
				</td>
			</tr>
			</table>
		</fieldset>
	</div>
	
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="name" value="Syndicate" />
		<input type="hidden" name="admin_menu_link" value="option=com_syndicate" />
		<input type="hidden" name="admin_menu_alt" value="Manage Syndication Settings" />
		<input type="hidden" name="option" value="com_syndicate" />
		<input type="hidden" name="admin_menu_img" value="js/ThemeOffice/component.png" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
}
?>
