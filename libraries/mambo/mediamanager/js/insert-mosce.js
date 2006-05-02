// <?php !! This fools phpdocumentor into parsing this file

/** @version $Id: insert-mosce.js,v 1.2 2005/08/28 14:22:30 facedancer Exp $
  * @package Mambo
  * @copyright (C) Mateusz Krzeszowiec
  * @author Mateusz Krzeszowiec <mateusz@krzeszowiec.com>
  * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
  */

function insertImage(image)
{
	tinyMCE.selectedInstance.execCommand('mceInsertContent',false,'<img src="'+image+'" />');	
};

function insertFile(file)
{
	tinyMCE.selectedInstance.execCommand('mceReplaceContent',false,'<a href="'+file+'" target="_blank">{$selection}</a>');
};
