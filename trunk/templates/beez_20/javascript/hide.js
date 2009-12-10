// Angie Radtke 2009 //


window.addEvent('domready', function() {


$('nav').setProperties({
    role: 'navigation'

});

$('breadcrumbs').setProperties({
    role: 'breadcrumbs'

});

if($('mod_search_searchword'))
{
$('mod_search_searchword').setProperties({
    role: 'search'

});

}

$('main').setProperties({
    role: 'main'

});

if($('right'))
{

$('right').setProperties({
    role: 'contentinfo'

});

}




    var list=$('nav').getElements('div');
    list.each(function(element) {



        if ($(element).getElement('div')) {

           el = $(element).getElement('div.module_content');



          listelement=el.getElement('li.active');

           el.setStyle('display', 'none');

           if(listelement)
           {
            el.setStyle('display', 'block');
           }
           else

           {   el.setStyle('display', 'none');
           }



           var unique=el.id ;
           test=readCookie(unique);
           if(test=='block')
          {

          el.setStyle('display', 'block');

          }

       }
    });
});




window.addEvent('domready', function() {

was=$('right');

if (was!=null)
{
    das=was.id ;
    test1=readCookie(das);
    if(test1=='none')
    {
    was.setStyle('display', 'none');
    wrapperwidth(big);
    grafik= $('bild');
    $('bild').innerHTML=rightopen;
    grafik.focus();
    }

}
});





function auf(key)
{


    el= $(key);

       if (el.style.display=='none')
       {
       el.setStyle('display', 'block');
       eltern=el.getParent();
       elternh=eltern.getElement('h3');
       elternh.addClass('high');
       elternbild=eltern.getElement('img');
       elternbild.focus();
       elternbild.setProperties({
        alt: altclose
        });

         if(key=='right')
         {

          document.getElementById('right').setStyle('display', 'block');
          wrapperwidth(small);
          grafik= $('bild');
          $('bild').innerHTML=rightclose;
          grafik.focus();


        }

         saveIt(key);

       }

       else
       { el.setStyle('display', 'none');
       eltern=el.getParent();
       elternh=eltern.getElement('h3');
       elternh.removeClass('high');

       elternbild=eltern.getElement('img');
       elternbild.setProperties({
       alt: altopen
        });
        elternbild.focus();
       if(key=='right')
         {
          document.getElementById('right').setStyle('display', 'none');
          wrapperwidth(big);
          grafik= $('bild');
          $('bild').innerHTML=rightopen;
          grafik.focus();

         }

       saveIt(key);

       }

}

var Cookies = {
    init: function () {
        var allCookies = document.cookie.split('; ');
        for (var i=0;i<allCookies.length;i++) {
            var cookiePair = allCookies[i].split('=');
            this[cookiePair[0]] = cookiePair[1];
        }
    },
    create: function (name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
        this[name] = value;
    },
    erase: function (name) {
        this.create(name,'',-1);
        this[name] = undefined;
    }
};
Cookies.init();




function saveIt(name) {

    var x = $(name).style.display;

    if (!x)
        alert('Please fill in a value in the input box.');
    else {
        Cookies.create(name,x,7);

    }
}



function eraseIt(name) {
    Cookies.erase(name);
    alert('Cookie erased');
}

function init() {
    for (var i=1;i<3;i++) {
        var x = Cookies['status' + i];
        if (x) alert('Cookie status' + i + '\nthat you set on a previous visit, is still active.\nIts value is ' + x);
    }
}
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function wrapperwidth(width)
{
$('wrapper').setStyle('width', width);

}