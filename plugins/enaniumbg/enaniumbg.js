function readCookie(name) {var nameEQ = name + "=";var ca = document.cookie.split(';');for(var i=0;i < ca.length;i++){var c = ca[i];while (c.charAt(0)==' ') c = c.substring(1,c.length);if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);}return null;}
function createCookie(name,value,days){if (days){var date = new Date();date.setTime(date.getTime()+(days*24*60*60*1000));var expires = "; expires="+date.toGMTString();}else var expires = "";document.cookie = name+"="+value+expires+"; path=/";}
function eraseCookie(name) {createCookie(name,"",-1);}

function enanium_change_bg(image)
{
  var body = document.getElementsByTagName('body')[0];
  var cw = document.getElementById('cell-content');
  if ( image == 'default' )
  {
    body.style.backgroundImage = 'url(' + cdnPath + '/themes/enanium/images/background.gif)';
    body.style.backgroundRepeat = 'repeat';
    body.style.backgroundAttachment = 'scroll';
    cw.style.backgroundImage = 'none';
    cw.style.backgroundColor = '#ffffff';
  }
  else
  {
    body.style.backgroundRepeat = enanium_bg_repeat;
    body.style.backgroundAttachment = enanium_bg_attachment;
    body.style.backgroundPosition = enanium_bg_position;
    body.style.backgroundColor = '#000000';
    body.style.backgroundImage = 'url(' + scriptPath + '/plugins/enaniumbg/' + image + '.jpg)';
    cw.style.backgroundImage = 'url(' + scriptPath + '/plugins/enaniumbg/transw70.png)';
    cw.style.backgroundColor = 'transparent';
  }
  
  var as = getElementsByClassName(document.getElementById('enanium_bg_list'), 'a', 'selected');
  for ( var i = 0; i < as.length; i++ )
      $dynano(as[i]).rmClass('selected');
  
  $dynano('ebg_' + image).addClass('selected');
  
  createCookie('enanium_bg', image, 365);
}

addOnloadHook(function()
  {
    var ck = readCookie('enanium_bg');
    if ( ck )
      enanium_change_bg(ck);
  });
