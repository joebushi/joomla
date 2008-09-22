

<?php
/**
Author : Nakul Ganesh S
Joomla GSoC 2008
Mentor : Deborah Susan Clarkson
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
 
 class MediaViewStudio extends JView
 {

	function display($tpl = null)
	{
		global $mainframe;
		// Do not allow cache
		$config =& JComponentHelper::getParams('com_media');
		JResponse::allowCache(false);
		$document = &JFactory::getDocument();
		$document->addStyleSheet('components/com_media/assets/mediamanagerstudio.css');
		$document->addScript('components/com_media/assets/moocrop.js');

			
//		$model =& $this->getModel();
//		$image = & $this->get( 'image' );
//		$this->assignRef('image',		$image);
//		parent::display($tpl);
		$this->assignRef('state', $this->get('state'));
		parent::display($tpl);
	//		$document->addScriptDeclaration("
//if(location.href!=top.location.href){
//top.location.href=location.href
//}");
				JHTML::_('behavior.mootools');
/*		  $document->addScriptDeclaration("
		window.addEvent('domready', function(){ new Accordion($$('.panel h3.jpane-toggler'), $$('.panel div.jpane-slider'), {onActive: function(toggler, i) { toggler.addClass('jpane-toggler-down'); toggler.removeClass('jpane-toggler'); },onBackground: function(toggler, i) { toggler.addClass('jpane-toggler'); toggler.removeClass('jpane-toggler-down'); },duration: 300,opacity: false}); });");
  	
  */		
  	
		$document->addScriptDeclaration("
	window.addEvent('domready', function(){	
	
	var color = [0, 0, 0];
		var updateColor = function(){
		$('setColor').setStyle('color', color).setHTML(color.rgbToHex());

	};

var mySlide = new Slider($('area'), $('knob'), {
	steps: 255,	
	onChange: function(step){
		$('upd').setHTML(step);
		color[0] = step;
		updateColor();
		window.document.input.r.value = step;
		
		
	}
 
}).set(0);


var mySlide2 = new Slider($('area2'), $('knob2'), {
	steps: 255,	
	onChange: function(step){
		$('upd2').setHTML(step);
				color[1] = step;
		updateColor();
	window.document.input.g.value = step;
		
	}
 
}).set(0);
 

var mySlide3 = new Slider($('area3'), $('knob3'), {	
	steps: 255,	
		onChange: function(step){
		$('upd3').setHTML(step);
				color[2] = step;
		updateColor();
		window.document.input.b.value = step;
	
	}
}).set(0);
var mySlide4= new Slider($('area4'), $('knob4'), {	
	steps: 100,	
		onChange: function(step){
		$('upd4').setHTML(step);
		window.document.input.bright.value = step;
	}
}).set(0);

var mySlide5= new Slider($('area5'), $('knob5'), {	
	steps: 100,	
		onChange: function(step){
		$('upd5').setHTML(step);
		window.document.input.contrast.value = step;
	}
}).set(0);
var mySlide6= new Slider($('area6'), $('knob6'), {	
	steps: 8,	
		onChange: function(step){
		$('upd6').setHTML(step);
		window.document.input.opacity.value = step;
	}
}).set(0);
var mySlide7= new Slider($('area7'), $('knob7'), {	
	steps: 100,	
		onChange: function(step){
		$('upd7').setHTML(step);
		window.document.save.quality.value = step;
	}
}).set(0);

});

	");
	
	$document->addScriptDeclaration("

	 function nakul() {
	 	this.disabled =  true;
 var crop = new MooCrop('imge',{
 	'handleColor' : '#333333',
 	'handleWidth' : '10px',
 	'handleHeight' : '10px',

 });
 crop.addEvent('onCrop' , 
 	function(imgsrc,crop,bound,handle){
		window.document.input.x.value = crop.left;
		window.document.input.y.value = crop.top;
		window.document.input.w.value = crop.width;
		window.document.input.h.value = crop.height;

		
			  });
			  }
			  	");
	
		$document->addScriptDeclaration("
window.addEvent('domready', function(){	
	var stretchers = document.getElementsByClassName('box');
	var toggles = document.getElementsByClassName('tab');
	var myAccordion = new Accordion(
		toggles, stretchers, {opacity: false, height: true, duration: 600}
	);
	var stretchers1 = document.getElementsByClassName('box1');
	var toggles1 = document.getElementsByClassName('tab1');
	var myAccordion1 = new Accordion(
		toggles1, stretchers1, {opacity: false, height: true, duration: 600}
	);

});
"
);


}
}
