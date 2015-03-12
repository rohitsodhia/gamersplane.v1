var redirectTimeout = 3000,
	API_HOST = 'http://api.gamersplane.local';
$.colorbox.settings.href = function () { return this.href + '?modal=1' };
$.colorbox.settings.iframe = true;
$.colorbox.settings.innerHeight = '100px';
$.colorbox.settings.innerWidth = '150px';

var jsCSSSheet = (function() {
	// Create the <style> tag
	var style = document.createElement("style");

	// Add a media (and/or media query) here if you'd like!
	// style.setAttribute("media", "screen")
	// style.setAttribute("media", "only screen and (max-width : 1024px)")

	// WebKit hack :(
	style.appendChild(document.createTextNode(""));

	// Add the <style> element to the page
	document.head.appendChild(style);

	return style.sheet;
})();