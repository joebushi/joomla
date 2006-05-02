<?php
/**
* @version $Id: Sef.php,v 1.1 2005/08/25 14:21:16 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

class patTemplate_Function_Sef extends patTemplate_Function
{
   /**
	* name of the function
	* @access	private
	* @var		string
	*/
	var $_name	=	'Sef';

   /**
	* call the function
	*
	* @access	public
	* @param	array	parameters of the function (= attributes of the tag)
	* @param	string	content of the tag
	* @return	string	content to insert into the template
	*/
	function call( $params, $content )
	{
		/*
		if( !isset( $params['macro'] ) ) {
            return false;
		}
		*/

		return sefRelToAbs( $content );;
	}
}
?>