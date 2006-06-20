<?PHP
/**
 * patTemplate modfifier Translate
 *
 * $Id: Translate.php 1782 2006-01-13 02:29:37Z eddieajau $
 *
 * @package		patTemplate
 * @subpackage	Modifiers
 * @author		Andrew Eddie <eddie.andrew@gmail.com>
 */

/**
 * Implements the Joomla translation function on a var
 *
 * @package		patTemplate
 * @subpackage	Modifiers
 * @author		Andrew Eddie <eddie.andrew@gmail.com>
 */
class patTemplate_Modifier_Translate extends patTemplate_Modifier
{
   /**
	* modify the value
	*
	* @access	public
	* @param	string		value
	* @return	string		modified value
	*/
	function modify( $value, $params = array() )
	{
		if (class_exists( 'JText' )) {
			return JText::_( $value );
		} else {
			if (defined( $value )) {
				$value = constant( $value );
			}
			return $value;
		}
	}
}
?>