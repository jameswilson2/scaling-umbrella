(function(exports){

var rootPath = "";
var baseElements = document.getElementsByTagName("base");
if(baseElements.length){
	rootPath = parseAbsoluteURL(baseElements[0].href) || {path:""};
	rootPath = normalizePath(rootPath.path);
}

var homepagePathAliases = [
	rootPath + "/index.htm",
	rootPath + "/index.php"
];

function parseAbsoluteURL(urlString) {
	var absoluteUrlPattern = /^([^:\/?#]+):\/\/(([^@\/?#]*)@)?([^:\/?#]*)(:([0123456789]*))?([^?#]*)(\?([^#]*))?(#(.*))?/g;
    var components = {};
    var a = absoluteUrlPattern.exec(urlString);
    absoluteUrlPattern.lastIndex = 0;
    if(!a) return null;
    components.scheme   = a[1];
    components.userinfo = a[3];
    components.hostname = a[4];
    components.port     = a[6];
    components.path     = a[7];
    components.query    = a[9];
    components.fragment = a[11];
    components.host = components.hostname;
    if(components.port) components.host += ":" + components.port;
    return components;
}

function isParentDirectory(parent, child){
	parent = parent.split("/");
	child = child.split("/");
	if(parent.length > child.length) return false;
	for(var i = 0; i < parent.length; i++){
		if(parent[i] != child[i]) return false;
	}
	return true;
}

function normalizePath(path){
	if(path.charAt(path.length-1) == '/'){
		path = path.substring(0, path.length - 1);
	}
	return path;
}

function isHomepage(path){
	if(path == rootPath) return true;
	for(var index in homepagePathAliases){
		if(path == homepagePathAliases[index]) return true;
	}
	return false;
}

function highlightNavigation(selector, highlighter){
	var currentLocationPath = window.location.pathname;
	currentLocationPath = normalizePath(currentLocationPath);
	$(selector).each(function(){
		var href = parseAbsoluteURL(this.href);
		if(!href || href.host != window.location.host) return;
		href.path = normalizePath(href.path);
		if(href.path == currentLocationPath){
			highlighter.apply(this,[]);
			return;
		}
		var hrefToHomepage = isHomepage(href.path);
		if(isHomepage(currentLocationPath) && hrefToHomepage){
			highlighter.apply(this,[]);
			return;
		}
		if(!hrefToHomepage && isParentDirectory(href.path, currentLocationPath)){
			highlighter.apply(this,[]);
			return;
		}
	});
}

function eachMatchURL(anchors, location, callback){
	
	var host = location.host;
	var path = normalizePath(location.pathname);
	var matchHomepage = isHomepage(path);
	
	$(anchors).each(function(){
		var href = parseAbsoluteURL(this.href);
		if(!href || href.host != host) return;
		href.path = normalizePath(href.path);
		if(href.path == path || (matchHomepage && isHomepage(href.path))){
			callback.apply(this, []);
			return;
		}
	});
}

exports.parseAbsoluteURL = parseAbsoluteURL;
exports.highlightNavigation = highlightNavigation;
exports.eachMatchURL = eachMatchURL;

})(this);
