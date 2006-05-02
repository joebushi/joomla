<?php
/**
* @version $Id: frontpage.class.php,v 1.1 2005/08/25 14:18:10 johanjanssens Exp $
* @package Mambo
* @subpackage Content
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
* @subpackage Content
*/
class mosFrontPage extends mosDBTable {
	/** @var int Primary key */
	var $content_id=null;
	/** @var int */
	var $ordering=null;

	/**
	* @param database A database connector object
	*/
	function mosFrontPage( &$db ) {
		$this->mosDBTable( '#__content_frontpage', 'content_id', $db );
	}
}
?>