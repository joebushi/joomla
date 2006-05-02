// <?php !! This fools phpdocumentor into parsing this file

/** @version $Id: insert-none.js,v 1.2 2005/08/28 14:22:30 facedancer Exp $
  * @package Mambo
  * @copyright (C) Mateusz Krzeszowiec
  * @author Mateusz Krzeszowiec <mateusz@krzeszowiec.com>
  * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
  */


focusedInput = document.adminForm.introtext;

document.adminForm.introtext.onfocus = function()
{
	focusedInput = document.adminForm.introtext;
};

document.adminForm.fulltext.onfocus = function()
{
	focusedInput = document.adminForm.fulltext;
};

function insertImage(image)
{
	insertAtCursor(focusedInput, '<img src="'+image+'" />');
};

function insertFile(file)
{
	insertAtCursor(focusedInput, '<a href="'+file+'" target="_blank">'+file+'</a>');	
};