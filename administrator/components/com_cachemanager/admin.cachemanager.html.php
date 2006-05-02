<?php
/**
* @version $Id: admin.cachemanager.html.php,v 1.1 2005/08/25 14:14:12 johanjanssens Exp $
* @package Mambo
* @subpackage Cache Manager
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class cacheManagerScreens {
	
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
	 * Output Display
	 *
	 * @param String $option
	 * @param Array $rows
	 * @param mosPageNav $pageNav
	 */
	function viewCache( $option, &$rows, &$pageNav ) {
		global $mosConfig_lang, $_LANG;
		
		mosCommonHTML::loadOverlib();

		$tmpl =& cacheManagerScreens::createTemplate();
		
		$tmpl->readTemplatesFromInput( 'view.html' );
		
		$tmplName = "comCacheManager";
		
		$tmpl->addVar( $tmplName, 'option', $option );
		$tmpl->addVar( $tmplName, 'action', 'index2.php' );
		$tmpl->addVar( $tmplName, 'method', 'post' );
		$tmpl->addVar( $tmplName, 'formname', 'adminForm' );
		$tmpl->addVar( $tmplName, 'formclass', 'adminform' );
		$tmpl->addVar( $tmplName, 'formid', 'cachemanagerform' );
		
		$pagenav = $pageNav->getPagesLinks();
		$limitbox = $_LANG->_( 'Display Num' )." ".$pageNav->getLimitBox().$pageNav->getPagesCounter();
		
		$tmpl->addVar( $tmplName, 'pagenav', $pagenav );
		$tmpl->addVar( $tmplName, 'limitbox', $limitbox );
		
		$tmpl->addObject( 'body-list-rows', $rows, 'row_' );
		
		$tmpl->displayParsedTemplate( $tmplName );
	}
}
?>