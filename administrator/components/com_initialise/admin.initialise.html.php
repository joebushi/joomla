<?php
/**
* @version $Id: admin.initialise.html.php,v 1.1 2005/08/27 15:31:33 ratlaw101 Exp $
* @package $ambo
* @subpackage Initialise Manager
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* $ambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class comInitialiseScreens {
	
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
	function viewEntries( $option, &$rows, &$pageNav, $permissions=null ) {
		global $mosConfig_lang, $_LANG;
		
		mosCommonHTML::loadOverlib();

		$tmpl =& comInitialiseScreens::createTemplate();
		
		$tmpl->readTemplatesFromInput( 'view.html' );
		
		$tmplName = "comInitialiseManager";
		
		$tmpl->addVar( $tmplName, 'option', $option );
		$tmpl->addVar( $tmplName, 'action', 'index2.php' );
		$tmpl->addVar( $tmplName, 'method', 'post' );
		$tmpl->addVar( $tmplName, 'formname', 'adminForm' );
		$tmpl->addVar( $tmplName, 'formclass', 'adminform' );
		$tmpl->addVar( $tmplName, 'formid', 'cachemanagerform' );
		
		$tmpl->addVar( $tmplName, 'permissions', $permissions );
		
		$pagenav = $pageNav->getPagesLinks();
		$limitbox = $_LANG->_( 'Display Num' )." ".$pageNav->getLimitBox().$pageNav->getPagesCounter();
		
		$tmpl->addVar( $tmplName, 'pagenav', $pagenav );
		$tmpl->addVar( $tmplName, 'limitbox', $limitbox );
		
		$tmpl->addObject( 'body-list-rows', $rows, 'row_' );
		
		$tmpl->displayParsedTemplate( $tmplName );
	}
	
	/**
	 * Output Display
	 *
	 * @param String $option
	 * @param Array $rows
	 * @param mosPageNav $pageNav
	 */
	function showEntry( $option, $entry ) {
		global $mosConfig_lang, $_LANG;
		
		mosCommonHTML::loadOverlib();

		$tmpl =& comInitialiseScreens::createTemplate();
		
		$tmpl->readTemplatesFromInput( 'showEntry.html' );
		
		$tmplName = "comInitialiseManager";
		
		$tmpl->addVar( $tmplName, 'option', $option );
		$tmpl->addVar( $tmplName, 'action', 'index2.php' );
		$tmpl->addVar( $tmplName, 'method', 'get' );
		$tmpl->addVar( $tmplName, 'formname', 'adminForm' );
		$tmpl->addVar( $tmplName, 'formclass', 'adminform' );
		$tmpl->addVar( $tmplName, 'formid', 'cachemanagerform' );
		
		if( $entry )
		{
			$tmpl->addVar( $tmplName, 'id', $entry->id );
			$tmpl->addVar( $tmplName, 'string', $entry->string );
			$tmpl->addVar( $tmplName, 'value', $entry->value );
			$tmpl->addVar( $tmplName, 'type', $entry->type );
			$tmpl->addVar( $tmplName, 'published', $entry->published );
		}
		
		$tmpl->displayParsedTemplate( $tmplName );
	}
}
?>