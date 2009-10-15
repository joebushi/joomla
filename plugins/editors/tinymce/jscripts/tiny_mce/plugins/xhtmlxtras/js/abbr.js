 /**
 * $Id: abbr.js 12276 2009-06-22 01:54:01Z pasamio $
 *
 * @author Moxiecode - based on work by Andrew Tetlaw
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

function init() {
	SXE.initElementDialog('abbr');
	if (SXE.currentAction == "update") {
		SXE.showRemoveButton();
	}
}

function insertAbbr() {
	SXE.insertElement(tinymce.isIE ? 'html:abbr' : 'abbr');
	tinyMCEPopup.close();
}

function removeAbbr() {
	SXE.removeElement('abbr');
	tinyMCEPopup.close();
}

tinyMCEPopup.onInit.add(init);
