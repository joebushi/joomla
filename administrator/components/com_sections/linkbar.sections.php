<?php
/**
* @version $Id: linkbar.sections.php,v 1.1 2005/08/25 14:14:51 johanjanssens Exp $
* @package Mambo
* @subpackage Sections
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Linkbar for Sections Component
 * @package Mambo
 * @subpackage Sections
 */
class sectionsLinkbar extends mosLinkbar {
	/**
	 * Constructor
	 */
	function sectionsLinkbar() {
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'defaultOptions' );
	}

	function defaultOptions() {
		if ( mosGetParam( $_REQUEST, 'task', '' ) == '' ) {
			global $_LANG;
			global $my;
			
			$this->addLink( $_LANG->_( 'Content' ), 'index2.php?option=com_content', $_LANG->_( 'Content Items Manager' ) );
			$this->addLink( $_LANG->_( 'Categories' ), 'index2.php?option=com_categories&amp;section=content', $_LANG->_( 'Categories Manager' ) );
			$this->addLink( $_LANG->_( 'Media' ), 'index2.php?option=com_media', $_LANG->_( 'Media Manager' ) );
			if ( $my->usertype != 'Manager' && $my->usertype != 'manager' ) {
				$this->addLink( $_LANG->_( 'Trash' ), 'index2.php?option=com_trash', $_LANG->_( 'Trash Manager' ) );
			}
		}
	}
}

/*
$linkBar =& new sectionsLinkbar();
$linkBar->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
$linkBar->display();
unset( $linkBar );
*/
?>