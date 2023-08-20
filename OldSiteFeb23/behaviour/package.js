
function loadRssView(url, container, mustacheTemplateString, extendItemViewData, nonCollectionViewData){
    
    function load(data){
        
        var collection = [];
        $("channel > item", data).each(function(){
            
            var item = {
                url: $("link", this).text(),
                title: $("title", this).text(),
                summary: $("description", this).text()
            };
            
            var pubDateTimestamp = Date.parse($("pubDate", this).text());
            var pubDateString;
            
            if(!isNaN(pubDateTimestamp)){
                var pubDate = new Date(pubDateTimestamp);
                item.date = pubDate;
            }
            
            if(typeof(extendItemViewData) == "function"){
                item = $.extend(item, extendItemViewData(item));
            }
            
            collection.push(item);
        });
        
        $(container).html(Mustache.to_html(mustacheTemplateString, $.extend({}, nonCollectionViewData, {collection: collection})));
    }
    
    $.ajax({
        url: url,
        success: load
    });
}
$(function() {
$(".video-link").each(function() {
	var $a = $(this);
	var url = $a.attr('href');
	if(url.match(/youtube.com/)){
		var code = url.match(/v.([^\n&?]+)/)[1];
		$a.attr('href','http://www.youtube.com/v/'+code);
	} else if(url.match(/youtu.be/)){
		var code = url.match(/youtu.be\/([^\n&?]+)/)[1];
		$a.attr('href','http://www.youtube.com/v/'+code);
	}
}).fancybox({
	type:"iframe",
	overlayShow:true,
	overlayOpacity:0.7
});});