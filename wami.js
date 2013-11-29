/*
 *	wami.js
 *	Adam Chin
 *
 */

var myWamiApp; 
function onLoad() {
	// div in which to put the audio button
  	var audioDiv = document.getElementById('AudioContainer'); 
 
  	// JSGF grammar 
	var jsgf = 
		"#JSGF V1.0;\n" +
		"grammar parrot;\n" +
		"public <top> = pan up | pan down | pan left | pan right | zoom in | zoom out | directions | alexander hall | witherspoon hall | west college | edwards hall | dod hall | clio hall | whig hall | brown hall | holder hall | murray hall | dodge hall | stanhope hall | maclean house | joseph henry house | chancellor green | mccormick hall | dillon gymnasium | nassau hall |firestone library|burr hall|green hall|robertson hall|prospect house|jones|frist campus center|mccosh health center|1937 hall|feinberg hall|walker hall|1903 hall|1939 hall|dodge osborn hall|eno hall|spelman halls|east pyne hall|stafford little hall|madison|pyne hall|campbell|joline hall|blair hall|buyers|lockhart hall|48 university place|foulke hall|henry hall|laughlin hall|1901 hall|dickinson hall|mccosh hall|architecture school|woolworth music center|cuyler hall|wright hall|patton-wright|marx|1879 hall|thomas laboratory|icahn lab|scully dormitories|1922 hall|1915 hall|gauss hall|1938 hall|new south building|dinky station|hamilton hall|wu hall|wilcox hall|106 alexander street|116 prospect avenue|120 alexander street|126 alexander street|169 nassau street|179 nassau street|171 broadmead street|185 nassau street - lewis center for the arts|194 nassau street|201 nassau street|2 dickinson street|228 alexander street|330 alexander street|350 alexander street|41 william street|5 ivy lane|58 prospect avenue|71 university place|87 prospect avenue|91 prospect avenue|architectural lab|baker rink|bendheim finance center|fisher hall|corwin hall|bendheim hall|berlind theatre|bobst|bowen|caldwell field house|terrace club|campus club|tower club|quadrangle club|ivy club|cottage club|cap and gown club|cloister|charter|tiger inn|colonial club|fields center|center for jewish life|chilled water plant|cogen plant|computer science|friend center|denunzio pool|elementary particle labs|fine|fitzrandolph observatory|forbes main|frick laboratory|hoyt laboratory|old graduate college|jadwin gymnasium|jadwin|mcdonnell|lenz tennis center|macmillan building|mccarter theatre|guyot hall|moffett|schultz laboratory|mudd library|bloomberg hall|peyton hall|wallace social science center|wyman house|262 alexander street|scheide caldwell house|22 chambers street|von neumann|engineering quadrangle|university stadium|1 palmer square|hibben apartments|magie apartments|palmer house|200 elm drive|180 alexander street|helm building|221 nassau street|cannon club|shea rowing center|hargadon hall|lauritzen hall|south baker hall|1981 hall|1952 stadium|294 alexander street|north hall|community hall|clapp hall|powers field|roberts stadium|lewis library|new graduate college|36 university place|sherrerd hall|1967 hall|springdale clubhouse;\n";
 
  	var grammar = {"language" : "en-us", "grammar" : jsgf };
 
  	// pollForAudio: must be true for speech synthesis to work. 
  	var audioOptions = {"pollForAudio" : true};
 
  	var configOptions = {"sendIncrementalResults" : false, "sendAggregates" : false};
  
  	// handlers (functions) which are called for various events:
  	var handlers = {"onReady" : onWamiReady, // WAMI is loaded and ready
		  		    "onRecognitionResult" : onWamiRecognitionResult,  // Speech recognition result available
		  		    "onError" : onWamiError,  // an error occurred
		  		    "onTimeout" : onWamiTimeout}; //WAMI timed out due to inactivity
 
    // create WAMI application with the settings and grammar just created
  	myWamiApp = new WamiApp(audioDiv, handlers, "json", audioOptions, configOptions, grammar);
}
function onWamiReady() { }
 
// called when a speech recognition result is received
function onWamiRecognitionResult(result) {
	
	var hyp = result.hyps[0].text;  		// best guess for what user said
	//alert("You said: '" + hyp + "'");		// for debugging 
	
	// display our best guess in message div in green
	document.getElementById("message").style.color = "#0a0"; 
	document.getElementById("message").innerHTML = "You said \"" + hyp + "\""; 
	
	if(hyp.match("directions")){
		var handle = window.open("http://www.princeton.edu/~adamchin/directions/");
		if(handle == null || typeof(handle) == "undefined")
			alert("You need to disable your popup blocker to get directions!");	
	} 
	else if(hyp.match("zoom in"))
		zoomIn();
	else if(hyp.match("zoom out"))
		zoomOut();
	else if(hyp.match("pan up"))
		panUp();
	else if(hyp.match("pan down"))
		panDown();
	else if(hyp.match("pan left"))
		panLeft();
	else if(hyp.match("pan right"))
		panRight();
	else
		voiceGo(hyp);
	
	myWamiApp.speak(hyp); // speech synthesis of what we heard
	setTimeout("myWamiApp.replayLastRecording()", 500); // play back recording 
}
 
// called when an error occurs
function onWamiError(type, message) {
	alert("WAMI error: type  = " + type + ", message = " + message);	
}
 
// called when WAMI session times out due to inactivity.
function onWamiTimeout() {
	alert("WAMI speech recognition timed out due to inactivity.\nRefresh the page to start over.");
}
