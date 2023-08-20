$(function() {

	$(window).resize(function(){initMobile()});
	initMobile();
	
	setAutoText();
	
	setupExpands($('body'));
	
	$('.scroller').infiniteScroll();
	
	$('.priceopt').change(function () {
		$i = $(this);
		if(this.checked && $i.attr('data-price')) { 
			var speed = $i.parent().text();
			var price = '&pound;' + $i.attr('data-price');
			$i.parents('.panel').find('.price').html(price).end().find('.speed').html(speed);
		};
	})
			
	var locat = location.href;
	var found = false;
	$('#nav').find('a').each(function() {
		var myhref = this.href.replace('index.htm','');
		if(myhref == locat) {
			$(this).parents('li').addClass('active');
			found = true;
		}
	});
	if(locat.lastIndexOf('/') != locat.length-1) {
		while(!found && locat.lastIndexOf('/') > $('base').attr('href').lastIndexOf('/')) {
			locat = locat.substring(0,locat.lastIndexOf('/'));
			regex = '^'+locat+'\/?';
			$('#nav').find('a').each(function() {
				var myhref = this.href.replace('index.htm','');
				if(myhref.match(regex)) {
					$(this).parents('li').addClass('active');
					found = true;
				}
			});
		}
	}
	
	//$('.galleryimage').attr('rel','gallery').fancybox();
	
});

function setupExpands(contain) {
	contain.find('.expand_head').unbind('click').click(function() {
		$a = $(this);
		$body = $a.next('.expand_body');
		if($a.hasClass('active')){
			$body.stop(true,true).animate({'height':'hide'},300);
			$a.removeClass('active');
		} else {
			$body.stop(true,true).animate({'height':'show'},300);
			$a.addClass('active');
		}
	}).filter(':not(.active)').siblings('.expand_body').hide();
}

/* Twitter Feed */
Twitter = {}

Twitter.getUserTimeline = function(screenName, callback){
    var url = "behaviour/gettweets.php?screen_name=" + encodeURIComponent(screenName) + "&count=2";
    $.getJSON(url, callback);
};

var shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
function shortMonthIndex(monthText) {
   var myIndex;
   $.each(shortMonths,function(i,a) {
		if(a == monthText) {
			myIndex = i;
		}
   });
   return myIndex;
}
function showTweets(screenName, selector){
    Twitter.getUserTimeline(screenName, function(timeline){
		var ul = document.createElement("ul");
		$.each(timeline, function(i, tweet) {
			var a = document.createElement("a");
			var li = document.createElement("li");
			li.className = "quote";
			a.href = "https://twitter.com/#!/" + screenName + "/status/" + tweet.id_str;
			
			var now = new Date();
			var v= tweet.created_at.split(/\s+/); 
			var created = new Date(Date.parse(v[1]+" "+v[2]+", "+v[5]+" "+v[3]+" UTC"));
			var e = (now.getTime() - created.getTime()) / 1000;
			var f;
			if(e<60)
				{f=e+" seconds ago"}
			else if(e<120)
				{f="a minute ago"}
			else if(e<45*60)
				{f=parseInt(e/60,10).toString()+" minutes ago"}
			else if(e<2*60*60)
				{f="an hour ago"}
			else if(e<24*60*60)
				{f=""+parseInt(e/3600,10).toString()+" hours ago"}
			else if(e<48*60*60){f="a day ago"}
			else{f=parseInt(e/86400,10).toString()+" days ago"}
			
			a.innerHTML = '<span class="colour">@'+screenName+'</span> ' + tweet.text + ' <span class="time">'+f+'</span>';
			li.appendChild(a);
			ul.appendChild(li);
		});
		$(selector).append(ul);
    });
}

var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
function monthIndex(monthText) {
	   var myIndex;
	   $.each(months,function(i,a) {
			if(a == monthText) {
				myIndex = i;
			}
	   });
	   return myIndex;
}

if((navigator.appVersion.indexOf('iPad') > 0 || navigator.appVersion.indexOf('Android') > 0) && $(window).width() > 600) {
	$('meta[name="viewport"]').remove();
	androidtest = navigator.appVersion.match(/Android ([\d]).([\d])/g);
	iostest = navigator.appVersion.match(/OS ([\d])_([\d])/g);
	if(iostest && iostest.length == 1) {
		iostest = iostest[0].replace(' ','_');
		iostest = iostest.split('_');
	}
	if((!iostest && !androidtest) || (iostest && iostest[1] < 7) || (androidtest && androidtest[1] < 4)) {
		document.write('<meta id="viewport" name="viewport" content="user-scalable=yes, maximum-scale=default, minimum-scale=default, width=default, initial-scale=default" />');
	} else {
		document.write('<meta id="viewport" name="viewport" content="user-scalable=yes" />');
	}
}
var mobileNavs = '#nav, #subnav';
 
function initMobile() {
	if ((!$.browser.msie || ($.browser.msie && $.browser.version > 8)) && document.documentElement.clientWidth <= 767) {
		$(mobileNavs).each(function(i, a) {
			var $a = $(this);
			if($a.children('.menu-toggle').length == 0) {
				$a.prepend('<div class="menu-toggle"><span class="tog-action">Show</span> <span class="tog-menu"></span></div>');
			}
			if(!$a.children('.menu-toggle').hasClass('selected')) {
				$a.children('ul').hide();
			}
			$a.children('.menu-toggle').unbind('click').click(mobileMenuDisplay);
			
			switch(this.id) {
				case 'nav':
					$a.find('.tog-menu').text('Website Navigation');
					break;
				case 'subnav':
					$a.find('.tog-menu').text('Secondary Navigation');
					break;
			}
		});
	} else {
		$('.menu-toggle').remove();
		$(mobileNavs).children('ul').show();
	}
}

function mobileMenuDisplay(e) {
	var btn = $(this);
	if(btn.hasClass('selected')) {
		btn.siblings('ul').slideUp(400).end().removeClass("selected").children('.tog-action').text("Show");
	} else {
		btn.addClass("selected").siblings('ul:not(.keephidden)').slideDown(400).end().children('.tog-action').text("Hide");
	}
	e.preventDefault();
}

function setAutoText() {
	$('.autoText').blur(function() {
		var inp = $(this);
		if(inp.val() == "") {
			inp.val(inp.attr('data-default')).addClass('defaulted')
		}
	}).focus(function() {
		var inp = $(this);
		if(inp.val() == inp.attr('data-default')) {
			inp.val('').removeClass('defaulted');
		}
	}).blur().parents('form').submit(function() {
		$(this).find('.autoText').each(function() {
			$a = $(this);
			if($a.val() == $a.attr('data-default')) {
				$a.val('')
			}
		})
	});
}

/* Gallery XML */
function loadSlideshowGalleryXml(slideshow, url, callback) {
	$('#headerplaceholder').remove();
	callback = callback || $.noop;
	var first = true;
	$.get(url, function(data){
		var dir = url.match(/(.*)\/.*$/)[1];
		
		slideshow.html('');
		$("document > gallery > photo", data).each(function(){
			var caption = this.getAttribute("caption");
			if(!caption){
				caption = "";
			} else {
				caption = '<div class="top"></div><div class="middle"><div class="contenttext">' +caption + '</div><div class="clear"></div></div><div class="bottom"></div>';
			}
			if(this.getAttribute("href")) {
				slideshow.append('<div class="galleryItem"><a href="'+this.getAttribute("href")+'"><span class="overlay"></span><img class="img" src="'+dir+'/'+this.getAttribute("src")+'" alt="" /></a><div class="overlay"></div><div class="gallery_text">'+caption+'</div></div></div>');
			} else {
				slideshow.append('<div class="galleryItem"><span class="overlay"></span><img class="img" src="'+dir+'/'+this.getAttribute("src")+'" alt="" /><div class="gallery_text">'+caption+'</div></div>');
			}
		});
		callback();
	});
}