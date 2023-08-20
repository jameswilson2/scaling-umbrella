/**
 * --------------------------------------------------------------------
 * jQuery-Plugin "pngFix"
 * Version: 1.2, 09.03.2009
 * by Andreas Eberhard, andreas.eberhard@gmail.com
 *                      http://jquery.andreaseberhard.de/
 *
 * Copyright (c) 2007 Andreas Eberhard
 * Licensed under GPL (http://www.opensource.org/licenses/gpl-license.php)
 *
 */
(function($){jQuery.fn.pngFix=function(settings){settings=jQuery.extend({blankgif:'blank.gif'},settings);var ie55=(navigator.appName=="Microsoft Internet Explorer"&&parseInt(navigator.appVersion)==4&&navigator.appVersion.indexOf("MSIE 5.5")!=-1);var ie6=(navigator.appName=="Microsoft Internet Explorer"&&parseInt(navigator.appVersion)==4&&navigator.appVersion.indexOf("MSIE 6.0")!=-1);if(jQuery.browser.msie&&(ie55||ie6)){jQuery(this).find("img[src$=.png]").each(function(){jQuery(this).attr('width',jQuery(this).width());jQuery(this).attr('height',jQuery(this).height());var prevStyle='';var strNewHTML='';var imgId=(jQuery(this).attr('id'))?'id="'+jQuery(this).attr('id')+'" ':'';var imgClass=(jQuery(this).attr('class'))?'class="'+jQuery(this).attr('class')+'" ':'';var imgTitle=(jQuery(this).attr('title'))?'title="'+jQuery(this).attr('title')+'" ':'';var imgAlt=(jQuery(this).attr('alt'))?'alt="'+jQuery(this).attr('alt')+'" ':'';var imgAlign=(jQuery(this).attr('align'))?'float:'+jQuery(this).attr('align')+';':'';var imgHand=(jQuery(this).parent().attr('href'))?'cursor:hand;':'';if(this.style.border){prevStyle+='border:'+this.style.border+';';this.style.border='';}
if(this.style.padding){prevStyle+='padding:'+this.style.padding+';';this.style.padding='';}
if(this.style.margin){prevStyle+='margin:'+this.style.margin+';';this.style.margin='';}
var imgStyle=(this.style.cssText);strNewHTML+='<span '+imgId+imgClass+imgTitle+imgAlt;strNewHTML+='style="position:relative;white-space:pre-line;display:inline-block;background:transparent;'+imgAlign+imgHand;strNewHTML+='width:'+jQuery(this).width()+'px;'+'height:'+jQuery(this).height()+'px;';strNewHTML+='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader'+'(src=\''+this.src+'\', sizingMethod=\'scale\');';strNewHTML+=imgStyle+'"></span>';if(prevStyle!=''){strNewHTML='<span style="position:relative;display:inline-block;'+prevStyle+imgHand+'width:'+jQuery(this).width()+'px;'+'height:'+jQuery(this).height()+'px;'+'">'+strNewHTML+'</span>';}
jQuery(this).hide();jQuery(this).after(strNewHTML);});jQuery(this).find("*").each(function(){var bgIMG=jQuery(this).css('background-image');if(bgIMG.indexOf(".png")!=-1){var iebg=bgIMG.split('url("')[1].split('")')[0];jQuery(this).css('background-image','none');jQuery(this).get(0).runtimeStyle.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+iebg+"',sizingMethod='scale')";}});jQuery(this).find("input[src$=.png]").each(function(){var bgIMG=jQuery(this).attr('src');jQuery(this).get(0).runtimeStyle.filter='progid:DXImageTransform.Microsoft.AlphaImageLoader'+'(src=\''+bgIMG+'\', sizingMethod=\'scale\');';jQuery(this).attr('src',settings.blankgif)});}
return jQuery;};})(jQuery);

// Copyright (C) 2010 Graham Daws
(function($){
	$.fn.fadeToReplaceWith = function(element, speed, callback){
		speed = speed || "slow";
		callback = callback || $.noop;
		return this.each(function(){
			var containerTag = ($(this).css("display") == "inline" ? "span" : "div");
			$(this).wrap(document.createElement(containerTag));
			var oldContainer = this.parentNode;
			oldContainer.style.position = "absolute";
			$(oldContainer).wrap(document.createElement(containerTag));
			$(oldContainer.parentNode).append(element);
			$(oldContainer).fadeOut(speed, function(){
				$(oldContainer).unwrap().remove();
				callback();
			});
		});
	}
})(jQuery);