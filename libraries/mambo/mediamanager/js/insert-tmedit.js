// <?php !! This fools phpdocumentor into parsing this file

/** @version $Id: insert-tmedit.js,v 1.3 2005/08/28 14:22:30 facedancer Exp $
  * @package Mambo
  * @copyright (C) Mateusz Krzeszowiec
  * @author Mateusz Krzeszowiec <mateusz@krzeszowiec.com>
  * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
  */

function insertImage(image)
{
	editoreditor1.insertHTML('<img src="'+image+'" />');
};

function insertFile(file)
{
	editoreditor1.surroundHTML('<a href="'+file+'" target="_blank">', '</a>');
};