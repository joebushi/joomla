<?php
/**
* @version $Id: Mambo.php,v 1.1 2005/08/25 14:21:16 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

class patTemplate_Function_Mambo extends patTemplate_Function
{
   /**
	* name of the function
	* @access	private
	* @var		string
	*/
	var $_name	=	'Mambo';

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
		if( !isset( $params['macro'] ) ) {
            return false;
		}

        $macro = strtolower( $params['macro'] );
		//$page =& $GLOBALS['mainframe']->getPage();	// Mambo 5.0 only

        switch ($macro) {

			case 'addtohead':
				$GLOBALS['mainframe']->addCustomHeadTag( $content );
				break;

        	case 'initeditor':	// Mambo 5.0 only
        		return initEditor( true );
        		break;

        	case 'mainbody':	// Mambo 5.0 only
        		return $page->showMainBody();
        		break;

        	case 'loadcomponent':	// Mambo 5.0 only
        		// deprecated ??
				if( !isset( $params['component'] ) ) {
		            return false;
				} else {
					return $page->showComponent( $params['component'] );
				}
        		break;

			case 'hasmodules':	// Mambo 5.0 only
				$position = mosGetParam( $params, 'position', '' );

				if ($page->countModules( $position ) > 0) {
					return $content;
				} else {
					return false;
				}
        		break;

         	case 'loadmodule':	// Mambo 5.0 only
				$name = mosGetParam( $params, 'name', '' );
				$style = mosGetParam( $params, 'style', 0 );
				ob_start();
				$page->showModule( $name, $style );
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
        		break;

        	case 'loadmodules':	// Mambo 5.0 only
				$position = mosGetParam( $params, 'position', '' );
				$style = mosGetParam( $params, 'style', 0 );
				ob_start();
				$page->showModules( $position, $style );
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
        		break;

        	case 'loadadminmodule':
				$position = mosGetParam( $params, 'position', '' );
				$style = mosGetParam( $params, 'style', 0 );
				ob_start();
				mosLoadAdminModule( $position, $style );
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
        		break;

	       	case 'showhead':	// Mambo 5.0 only
        		return $page->showHead();
        		break;

        	case 'pathway':	// Mambo 5.0 only
				$Itemid = mosGetParam( $_REQUEST, 'Itemid', '' );
				ob_start();
				require $GLOBALS['_CONFIG']->SITEPATH . '/includes/pathway.php';
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
        		break;
		}

		return false;
	}
}
?>