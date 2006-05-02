<?php
/**
* @version $Id: initialise.php,v 1.1 2005/08/27 15:36:06 ratlaw101 Exp $
* @package $ambo
* @subpackage Initialise Mambot
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* $ambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onStart', 'botInitialise' );

function botInitialise()
{
	if ( phpversion() <= '4.2.1' ) {
		$agent 	= getenv( 'HTTP_USER_AGENT' );
	} else {
		$agent 	= mosGetParam( $_SERVER, 'HTTP_USER_AGENT', '' );
	}
	
	new botInitialise( $agent, dirname( dirname(__FILE__) )."/initialise.ini" );
}

class botInitialise {
	
	function botInitialise($string, $source)
	{
		$handle = @fopen($source, "r");
		if( $handle == null ) return;
		
		$row = 1;
		while (($data = fgetcsv($handle, 1000, ",")) !== false)
		{
			if(count($data) == 4)
			{
				if( $data[3] && $this->check( $string, $data[0] ) )
				{
					$this->redirect( $data[1], $data[2] );
					fclose($handle);
					return;
				}
			}
		}
		
		fclose($handle);
	}
	
	function redirect($type, $value)
	{
		switch ($type)
		{
			case 0: // Change template
				$_REQUEST['mos_change_template'] = $value;
				break;
				
			case 1: // Redirect to url
				mosRedirect( $value, '' );
				break;
		}
	}
	
	function check( $string, $value )
	{
		if( strlen( $value ) == 0 ) return true;
		
		if( strpos( strtolower( $string ), strtolower( $value ) ) > 0) return true;
		
		return false;
	}
}
?>