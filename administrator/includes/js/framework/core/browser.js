// <?php !! This fools phpdocumentor into parsing this file
/**
* @version $Id: browser.js,v 1.1 2005/08/25 14:17:44 johanjanssens Exp $
* @package Mambo
* @subpackage javascript
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

// -- Browser information -----------------------
Browser = new Object();
Browser.agt    = navigator.userAgent.toLowerCase();
Browser.is_ie	= ((Browser.agt.indexOf("msie") != -1) && (Browser.agt.indexOf("opera") == -1));