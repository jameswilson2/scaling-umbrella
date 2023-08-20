/* ----------------------
   Onload Events
---------------------- */

$(document).ready(function(){
	$(".select-all").click(function(){
		this.select();
	});
});

/* ----------------------
   Scripts
---------------------- */

/* Smooth Scroller *//*
eval((function(){a="Scroller={speed:10,8dC.;d.while(dC.+C.}} J8N;d=5;&&M4M}d&&dM4dM}%4%} 0J8a,F4(F,fa@7a.4a.LP+F7Jend8e66.cancelBubble=true;6.Value=fa@;}&&(E(7J8di=Hner3||5.G3;hN.3;a=(Ed>ah-d>i7e@{-(h-d)7}e@{a=a+(d-a}To(0,aEa==a}=aJHit8KwHdow,A,A7,A82P;l=9;d=locatiP;D&&D.HdexOfL#)!=-1&&(l/+l=C)Kl,Gck,endEl.PGck=2l=this.hash.substr(1E9.name==l;i=setILL+(9)+),107}}}}}};Hit()",b=48;while(b>=0)a=a.replace(new RegExp("%23456789@ACDEFGHJKLMNP".charAt(b),"g"),("\042Scroller.entfunction(offsetParscrollwindow.returndocumattachEvntervala=.getElemsByTagName(a);if(offsetTop){for(i=0;i<a.length;i++.pathnamea+=Math.ceil((d-ae.stopPropagationTopa.addEvListenerbody)/speede.prevDefaultclearI(i)pageYOffsetend(this);Height .Elemev)}:a[i]lseload=dl.href);b,dcliin},((.=.=C||on".split(""))[b--]);return a})())
*/

/* Popups */
function popUp(URL, popWidth, popHeight, popLeft, popTop, autoCenter, fullScreen) {
	day = new Date();
	id = day.getTime();
	if (autoCenter == 1) {
		var popLeft = (screen.width - popWidth) / 2;
		var popTop = (screen.height - popHeight) / 2;
	}
	if (fullScreen == 1) {
		// Open in Full Screen window!
		eval("page"+id+" = window.open(URL, '"+id+"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width="+(screen.width-10)+",height="+(screen.height-26)+",left=0,top=0');");
	} else {
		// Open in normal window!
		eval("page"+id+" = window.open(URL, '"+id+"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width="+popWidth+",height="+popHeight+",left="+popLeft+",top="+popTop+"');");
	}
}


/* Preload Images */
function preloadimages() {
	var i = 0;
	var myimg = new Array();
  for ( i=0; i<preloadimages.arguments.length; i++ ) {
    myimg[i] = new Image();
    myimg[i].src = preloadimages.arguments[i];
  }
}
preloadimages(
	"presentation/delete_hover.gif",
	"presentation/edit_hover.gif",
	"presentation/preview_hover.gif",
	"presentation/copy_hover.gif"
);


/* Toggle Content Text */
function expandText(location, image) {
	var para = document.getElementById(location);
	var gif = document.getElementById(image);
	if ( para.style.display == "inline" ) {
		para.style.display = "none";
		gif.src = "presentation/folder_closed.gif";
	} else {
		para.style.display = "inline";
		gif.src = "presentation/folder_open.gif";
	}
}

/* Toggle Content */
function expand(location) {
	var para = document.getElementById(location);
	if ( para.style.display == "inline" ) {
		para.style.display = "none";
	} else {
		para.style.display = "inline";
	}
}


/* Copy Content */
function copy(stoput) {
	var flashcopier = 'flashcopier';
	if(!document.getElementById(flashcopier)) {
		var divholder = document.createElement('div');
		divholder.id = flashcopier;
		document.body.appendChild(divholder);
	}
	document.getElementById(flashcopier).innerHTML = '';
	var divinfo = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ' +
	'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ' +
	'width="0" height="0">' +
	'<param name="src" value="behaviour/_clipboard.swf?clipboard='+encodeURIComponent(stoput) + '" />' +
	'<embed src="behaviour/_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(stoput)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>' +
	'</object>'
	document.getElementById(flashcopier).innerHTML = divinfo;
}

/*
function copy(inElement) {
  if (inElement.createTextRange) {
    var range = inElement.createTextRange();
    if (range && BodyLoaded==1)
     range.execCommand('Copy');
  } else {
    var flashcopier = 'flashcopier';
    if(!document.getElementById(flashcopier)) {
      var divholder = document.createElement('div');
      divholder.id = flashcopier;
      document.body.appendChild(divholder);
    }
    document.getElementById(flashcopier).innerHTML = '';
    var divinfo = '<embed src="behaviour/_clipboard.swf" FlashVars="clipboard='+escape(inElement.value)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
    document.getElementById(flashcopier).innerHTML = divinfo;
  }
}*/