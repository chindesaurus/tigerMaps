/*
 * mashup.js
 *
 * Adam Chin 
 *
 */


// mashup's geocoder
var geocoder = null;

// map is intially centred at Frist Campus Center
var home = new GLatLng(40.346716,-74.655139);
 
// set allowable bounds for map 
var allowedBounds = new GLatLngBounds(new GLatLng(40.341585, -74.668405), new GLatLng(40.349707, -74.638402));

// Google Map
var map = null;

// store corresponding GLayers
var photos, wiki, youtube;


/*
 * bool
 * disableEnterKey()
 *
 * Disallow use of enter/return key to submit form.
 */

function disableEnterKey(e) {
	
	var key;
	if(window.event)	// Internet Explorer's key
		key = window.event.keyCode;
	else				// other browsers
		key = e.which;
	
	// ASCII 13 is carriage return
	if(key == 13) {
		
		document.getElementById("message").style.color = "#f00";

		// display data.txt in message div
		getData("data.txt", "message");
	}
	return (key != 13);
}
			 

/*
 * void
 * getData(dataSource, divID)
 *
 * AJAX display of data.txt in div with id=divID
 */
			 
function getData(dataSource, divID) {  
		var XMLHttpRequestObject = false;
		
		if (window.XMLHttpRequest)  
			XMLHttpRequestObject = new XMLHttpRequest(); 
		// handle Internet Explorer
		else if (window.ActiveXObject)
			XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		
		if(XMLHttpRequestObject) { 
			var obj = document.getElementById(divID);  
			XMLHttpRequestObject.open("GET", dataSource);  
			XMLHttpRequestObject.onreadystatechange = function()  
			{  
				if (XMLHttpRequestObject.readyState == 4 &&  
					XMLHttpRequestObject.status == 200) {  
							  
					obj.innerHTML = XMLHttpRequestObject.responseText;  
				}  
			}  
		XMLHttpRequestObject.send(null);  
		}
}


/*
 * void
 * go()
 *
 * Pans map to desired location if possible, else apologises.
 */

function go() {
    
	// go to location
    geocoder = new GClientGeocoder();
    
	var input = document.getElementById("myInput").value + ", Princeton";
	
	// disallow invalid input; YUI force selection deletes myInput value from input string
	if(input.length == 11) {
		
		// apologise to user in red 
		document.getElementById("message").style.color = "#f00";
		getData("apology.txt", "message");
		return;
	}

	// overwrite data.txt with blank.txt in message div
	getData("blank.txt", "message");
	
function showAddress(input) {
  geocoder.getLatLng(
      input,
      function(point) {
      	  if (!point || !allowedBounds.contains(point)) {
          	  alert("Sorry; " + input + " not found!\nPlease use the form autocomplete for exact building names.");
          } else {
				map.setCenter(point, 17);
            	var marker = new GMarker(point);
				map.addOverlay(marker);
				marker.setImage("redMarker.png");
            	marker.openInfoWindowHtml(input);
		  		
				// play synthetic voice of input value
				myWamiApp.speak(document.getElementById("myInput").value);

				// add mouseover event listener
				GEvent.addListener(marker, "mouseover", function() {
				    marker.setImage("blueMarker.png");
					marker.openInfoWindowHtml(input);
				});  
		  		
				// add mouseout event listener
				GEvent.addListener(marker, "mouseout", function() {
				    marker.setImage("redMarker.png");
				});
		  }
        }
  );
    }
    showAddress(input);
	
	// update markers
	update();
}


/*
 * void
 * voiceGo()
 *
 * Pans map to desired location if possible, else apologises.
 */

function voiceGo(input) {
    // go to location
    geocoder = new GClientGeocoder();

    input += ", Princeton";
	
function showAddress(input) {
  geocoder.getLatLng(
      input,
      function(point) {
        if (!point) {
            alert("Sorry; " + input + " not found!");
        } else {
			map.setCenter(point, 17);
            var marker = new GMarker(point);
            map.addOverlay(marker);
			marker.setImage("redMarker.png");
            marker.openInfoWindowHtml(input);
				
			// add mouseover event listener
			GEvent.addListener(marker, "mouseover", function() {
				marker.setImage("blueMarker.png");
				marker.openInfoWindowHtml(input);
			});  
			
			// add mouseout event listener
			GEvent.addListener(marker, "mouseout", function() {
			marker.setImage("redMarker.png");
			});
       		 
		}
        }
  );
    }
    showAddress(input);
    
    // update markers
    update();
}

/*
 * void
 * zoomIn(), zoomOut(), panUp(), panDown(), panLeft(), panRight()
 *
 * functions for map control 
 */
function zoomIn() {
	map.setZoom(map.getZoom() + 1);
}
function zoomOut() {
	map.setZoom(map.getZoom() - 1);
}
function panUp() {
	map.panDirection(0, 1);
}
function panDown() {
	map.panDirection(0, -1);
}
function panLeft() {
	map.panDirection(1, 0);
}
function panRight() {
	map.panDirection(-1, 0);
}


/*
 * void
 * clearAll()
 *
 * remove all overlays from the map
 */
function clearAll() {
	
	map.clearOverlays();
	
	// clear checkboxes, too
	document.getElementById("photos").checked = false;
	document.getElementById("wikipedia").checked = false;
	document.getElementById("videos").checked = false;

	// overwrite data.txt in message div
	document.getElementById("message").innerHTML = "";

	// clear text in search bar
	document.getElementById("myInput").value = "";

    // reset map
	map.setCenter(home, 15);
}

/*
 * void
 * load()
 *
 * Loads (and configures) Google Map.
 */

function load() {
    // ensure browser supports Google Maps
    if (!GBrowserIsCompatible()) {
        alert("Sorry, your browser is not compatible with the Google Maps API!")
		return;
	}

    /* use javascript to set "autocomplete='off'" to retain valid xhtml
	turn off browser's caching of input  */
	document.getElementById("myInput").setAttribute("autocomplete", "off"); 
	// instantiate geocoder
    geocoder = new GClientGeocoder();

    // resize map's container
    resize();

    // instantiate map
    map = new GMap2(document.getElementById("map"));

    // center map on home
    map.setCenter(home, 15);

    // add control(s)
    var mapControl = new GMapTypeControl();
    map.addControl(mapControl);
    map.addControl(new GLargeMapControl3D());
	map.addMapType(G_PHYSICAL_MAP);				// terrain view
	//map.addControl(new GOverviewMapControl());	// map overview in corner
    map.addMapType(G_SATELLITE_3D_MAP);			// allow Google Earth view

	// enable continuous zooming
	map.enableContinuousZoom();

    // update markers anytime user drags or zooms map
    GEvent.addListener(map, "dragend", update);
    GEvent.addListener(map, "zoomend", update);

    // resize map anytime user resizes window
    GEvent.addDomListener(window, "resize", resize);

	/* prepare media and hide 
	   GLayers stored in global variables */	
	photos = new GLayer("com.panoramio.all");
	map.addOverlay(photos);
	photos.hide();

	wiki = new GLayer("org.wikipedia.en");
	map.addOverlay(wiki);
	wiki.hide();
	
	youtube = new GLayer("com.youtube.all");
	map.addOverlay(youtube);
	youtube.hide();

    // update markers
    update();

    // give focus to text field
    document.getElementById("myInput").focus();
}


/*
 * void
 * resize()
 *
 * Resizes map's container to fill area below form.
 */

function resize() {
    // prepare to determine map's height
    var height = 0;

    // check for non-IE browsers
    if (window.innerHeight !== undefined)
        height += window.innerHeight;

    // check for IE
    else if (document.body.clientHeight !== undefined)
        height += document.body.clientHeight;

    // leave room for logo and form
    height -= 140;

    // maximize map's height if room
    if (height > 0) {
        
		// adjust height via CSS
        document.getElementById("map").style.height = height + "px";

        // ensure map exists
        if (map) {
            
			// resize map
            map.checkResize();

            // update markers
            update();
        }
    }
}


/*
 * void
 * unload()
 *
 * Unloads Google Map.
 */

function unload() {
    // unload Google's API
    GUnload();
}


/*
 * void
 * addPhotos(), addWikipedia(), addVideos()
 *
 * add/remove media to/from map
 */
function addPhotos() {
	var box = document.getElementById("photos");
	if(box.checked)
		photos.show();
	else
		photos.hide();
}
function addWikipedia() {
	var box = document.getElementById("wikipedia");
	if(box.checked)
		wiki.show();
	else
		wiki.hide();
}
function addVideos() {
	var box = document.getElementById("videos");
	if(box.checked)
		youtube.show();
	else
		youtube.hide();
}


/*
 * void
 * update()
 *
 */

function update() {
   
	// get map's current bounds
    var bounds = map.getBounds();

	// add move listener to restrict map to Princeton
      GEvent.addListener(map, "move", function() {
        checkBounds();
      });
 
      // if the map position is out of range, move it back
      function checkBounds() {
        
		// check bounds and return if within bounds
        if (allowedBounds.contains(map.getCenter())) 
        	return;
        
		// out of bounds, so find nearest allowed point and set center there
        var C = map.getCenter();
        var X = C.lng();
        var Y = C.lat();
 
        var maxX = allowedBounds.getNorthEast().lng();
        var maxY = allowedBounds.getNorthEast().lat();
        var minX = allowedBounds.getSouthWest().lng();
        var minY = allowedBounds.getSouthWest().lat();
 
        if (X < minX) 
			X = minX;
        if (X > maxX) 
			X = maxX;
        if (Y < minY) 
			Y = minY;
        if (Y > maxY) 
			Y = maxY;
		map.setCenter(new GLatLng(Y, X));
	}
}
