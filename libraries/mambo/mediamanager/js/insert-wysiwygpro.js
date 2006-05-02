// <?php !! This fools phpdocumentor into parsing this file

/** @version $Id: insert-wysiwygpro.js,v 1.1 2005/08/28 19:32:14 facedancer Exp $
  * @package Mambo
  * @copyright (C) Mateusz Krzeszowiec
  * @author Mateusz Krzeszowiec <mateusz@krzeszowiec.com>
  * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
  */

function insertImage(image)
{
	wp_current_obj.insertAtSelection('<img src="'+image+'" />');
};

function insertFile(file)
{
	var text = wp_current_obj.getSelectedText();
	wp_current_obj.insertAtSelection('<a href="'+file+'" target="_blank">'+text+'</a>');
};