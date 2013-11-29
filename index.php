<?php
    $q = $_GET['q'];
    if (! is_null($q)) {
        require_once('./geocoded.php');
        
        # convert chars to html entities; trim whitespace from beginning and end; 
        # for each word, capitalize title character and lowercase the rest 
        $q = htmlentities(trim(ucwords(strtolower($q))));
    }
    
    $a = $_GET['a'];
    $b = $_GET['b'];
    $mode = $_GET['mode'];
    $highways = $_GET['highways'];
    $tolls = $_GET['tolls'];
?>

<!DOCTYPE html> 
<html> 
<head> 
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"> 
<meta http-equiv="content-type" content="text/html; charset=utf-8"> 

<title>Princeton Maps</title>

<!-- Combo-handled YUI CSS files: -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.1/build/reset-fonts/reset-fonts.css&2.8.1/build/assets/skins/sam/skin.css">
<style type="text/css">
html { 
    height: 100%; 
}
body { 
    height: 100%;
    margin: 0px;  
    padding: 0px; 
}
h1 { 
    font-size: 32px; 
    font-weight:bold; 
}
a { 
    text-decoration:none;
    color: #000;
}
a:hover {
    color: #ff4500;
}
h6 { font-size: 77%; }
h6 a { color: #767676; }
h6 a:hover { color: #f60; }
td {
    padding: 10px;
}
table.search td {
    padding-top: 25px; 
    padding-bottom: 25px; 
    padding-left:45px;
    padding-right:22px;
}
input.search {
    font-size: 138.5%;
    margin-top: -12px;
}
ul li {
    list-style: disc inside;
    margin: 1em;
}
.panel { color: #767676; }
.panel:hover { color: #f60; }
#map_canvas {
    height: 100%;
    width:100%; 
    border: 2px solid #eee;
}
#myAutoComplete {
    width: 35em;
}
#searchCell {
    padding: 0;
}
.yui-skin-sam .yui-ac-content li.yui-ac-highlight {
    background: #f60;
}
.yui-skin-sam .yui-ac-content li.yui-ac-prehighlight {
    background: #f90;
}
.marker {
    margin-right:10px; 
    vertical-align:middle;
}
/* background color for each panel */
.yui-skin-sam .yui-layout { 
    background-color: #fff; 
}
/* color of active resize handle */
.yui-skin-sam .yui-layout .yui-resize .yui-resize-handle-active { 
    background-color: #fff; 
}
/* style clip of sidebar when closed */
.yui-skin-sam .yui-layout .yui-layout-clip { 
    background-color: #fff;
    border: none; 
}
/* style the body */
.yui-skin-sam .yui-layout .yui-layout-unit div.yui-layout-bd {
    background-color: #fff;
    border: none;
}
/* header color */
.yui-skin-sam .yui-layout .yui-layout-hd { 
    background:url(sprite.png) repeat-x 0 -1400px; 
    border: none;
    background-color: #fff;
}
</style>

<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
</head>

<body class="yui-skin-sam" onload="initialize();">

<div id="top1">
<form id="searchForm" action="/~adamchin/maps2/" method="get"> 
    <table class="search"> 
    <tr>
        <td> 
            <h6 style="position:absolute; margin-left:240px;"><a href="http://en.wikipedia.org/wiki/Software_release_life_cycle#Alpha" target="_blank">alpha</a></h6>
            <h1><a href="/~adamchin/maps2/" onmouseover="document.getElementById('logo').src='princetonLogo.gif';" onmouseout="document.getElementById('logo').src='princetonLogoGrey.gif';">Princeton Maps</a></h1>
            <p style="position:absolute;"><a href="#" onclick="toggleDirectionsPanel();" id="directionsPanel" class="panel">Show Directions Panel</a> | <a href="#" onclick="geolocate();" class="panel">Show My Location</a></p>
        </td>
        <td> 
            <div id="myAutoComplete">   
                <input type="text" id="myInput" name="q" class="search" value="<?php echo $q ?>" maxlength="40" /> 
                <div id="myContainer"></div> 
            </div>  
        </td> 
        <td id="searchCell"> 
            <input type="submit" id="search" class="search" value="Search" /> 
        </td>
        <td style="padding: 15px;"> 
            <a id="linkbutton" href="http://webscript.princeton.edu/~adamchin/maps2/">Reset</a> 
        </td> 
    </tr> 
    </table> 
</form> 
</div>

<div id="sidebar">
    <img src="princetonLogoGrey.gif" alt="Princeton logo" id="logo" style="margin-left: 135px; position:relative;" height="80" />
    
    <form id="directionsForm" action="/~adamchin/maps2/" method="get" style="display: none;">
    <table style="margin-left:20px;">
        <tr>
            <td>A: </td>
            <td><input type="text" size="35" id="start" name="a"></td>
        </tr>
        <tr>
            <td>B: </td>
            <td><input type="text" size="35" id="end" name="b"></td>
        </tr>
    </table>

    <table style="margin-left:50px;">
        <tr>
            <td><select id="mode" name="mode"> 
                <option value="DRIVING" <?php if($mode==="DRIVING") echo "selected=\"selected\""; ?>>Driving</option> 
                <option value="BICYCLING" <?php if($mode==="BICYCLING") echo "selected=\"selected\""; ?>>Bicycling</option> 
                <option value="WALKING" <?php if($mode==="WALKING") echo "selected=\"selected\""; ?>>Walking</option> 
            </select></td>
            
            <td>
                <input type="checkbox" id="highways" name="highways" <?php if($highways==="on") echo "checked=\"checked\""; ?>> Avoid Highways<br> 
                <input type="checkbox" id="tolls" name="tolls" <?php if($tolls==="on") echo "checked=\"checked\""; ?>> Avoid Tolls
            </td>
        </tr>
        <tr>
            <td><input type="submit" id="submitbutton1" value="Get Directions"></td> 
            <td><input type="button" id="pushbutton2" value="Switch A and B"></td> 
        </tr>
    </table>

    <div id="directions" style="font-size:85%; margin: 10px;"></div>
    </form>

    <div id="results" style="margin-top: 15px;">
    <?php
        if (isset($q) && is_null($buildings["$q"])) {
                print "<div style='margin-top: 30px; margin-left: 20px; font-size: 108%;'>Sorry, \"<b>$q</b>\" not found!<br><br><b><i>Suggestions</i></b>:<br><br>";
        
        $count = 0;
        foreach ($buildings as $key => $i) {
            if (stripos($key, $q) !== FALSE) {
                print "<a href='http://webscript.princeton.edu/~adamchin/maps2/?q=" . urlencode($key) ."'><img src='red.png' alt='marker' class='marker'>" . $key . "</a><br><br>";
                $count++;
            }
        }

        if ($count == 0) {
            echo "<ul><li>Make sure the building name is spelled correctly.</li><li>Try the form autocomplete for exact building names.</li><li>Search the web for \"<a href='http://www.google.com/search?q=" . $q . "' target='_blank' style='text-decoration:underline; color:#00f'><b>" . $q . "</b></a>\".</li><li>Please report any bugs, errors, or omissions to:<br><br>adamchin at princeton dot EDU.</li></ul>";
        }
        echo "</div>";
        }
    ?>
    </div>
    </div>
</div>

<div id="center1">
    <div id="map_canvas"></div>
</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var map, myLatlng;
var directionsService = new google.maps.DirectionsService();
window.onresize = resize;

function initialize() {
    myLatlng = new google.maps.LatLng(40.345367,-74.653387);
        var myOptions = {
            zoom: 16, 
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: true
        }
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        resize();
        <?php
            if (! is_null($q) && ! is_null($buildings["$q"])) {
                echo 'addMarker();';  
            }
        
        
            if (! (is_null($a) || is_null($b)) )
                print "toggleDirectionsPanel();" .
                "document.getElementById('start').value = \"" . $a . "\";" .
                "document.getElementById('end').value = \"" . $b . "\";" .
                "calcRoute();";
        ?>

        // give focus to input so user can begin typing right away
        document.getElementById('myInput').focus();
}

function resize() {
    // prepare to determine map's height
    var height = 0;

    // check for non-IE browsers
    if (window.innerHeight !== undefined)
        height += window.innerHeight;

    // check for IE
    else if (document.body.clientHeight !== undefined)
        height += document.body.clientHeight;

    // leave room for Google copyright stuff 
    height -= 85;

    // maximize map's height if room
    if (height > 0) {
        
        // adjust height via CSS
        document.getElementById("map_canvas").style.height = height + "px";
                                
    }

    // adjust width of searchbox
    document.getElementById("myAutoComplete").style.width = document.body.clientWidth * 0.3 + "px";
}

function addMarker() {
    
        <?php
            if (! is_null($q) && ! is_null($buildings["$q"])) {
        ?>  

        myLatlng = new google.maps.LatLng(<?php echo $buildings["$q"]["latitude"] . ", " . $buildings["$q"]["longitude"] ?>);
        <?php } ?>

        map.setCenter(myLatlng);
        map.setZoom(17);

        var image = 'red.png';
        marker = new google.maps.Marker({
            position: myLatlng,
            title:"<? echo $q ?>",
            icon: image
        });
        marker.setMap(map);

        var contentString = '<b><?php echo $q; ?></b><br>' + 
                                '<span style="font-size: 85%; font-weight: bold;"><a href="javascript:prepareDirections();" target="_blank">directions</a>, <a href="javascript:map.setCenter(new google.maps.LatLng('+myLatlng.toUrlValue(6)+')); map.setZoom(parseInt(map.getZoom())+1);">zoom in</a>, <a  href="javascript:map.setCenter(new google.maps.LatLng('+myLatlng.toUrlValue(6)+')); map.setZoom(parseInt(map.getZoom())-1);">zoom out</a></span><br>' + '<br><img alt="picture of building" height="120" src="<?php echo $buildings["$q"]["picture"] ?>">';

        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });

        infowindow.open(map, marker);
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });

        // update sidebar
        document.getElementById("results").innerHTML = "<div style='font-size: 108%; font-weight: bold; text-align:center;'><a href='javascript:google.maps.event.trigger(marker, \"click\");' style='margin-right:30px;'><img src='red.png' alt='marker' class='marker'><?php echo $q ?></a></div>";
}

function reverse() {
    var temp = document.getElementById("start").value;
    document.getElementById("start").value = document.getElementById("end").value;
    document.getElementById("end").value = temp;
}

function calcRoute() {

    // clear directions div
    document.getElementById("directions").innerHTML = "";
    
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.draggable = true;
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById("directions"));
    
    var start = document.getElementById("start").value;
    var end = document.getElementById("end").value;
    var travel = document.getElementById("mode").value;

    var request = {
        origin:start, 
        destination:end,
        provideRouteAlternatives:true,
        avoidHighways: document.getElementById("highways").checked,
        avoidTolls: document.getElementById("tolls").checked,
        travelMode: travel
    };
    directionsService.route(request, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(result);
        }
        else {
            alert("Princeton Maps finds the address \"" + start + "\" ambiguous.\n\nPlease try again!");
        }
    
    });
    
    document.getElementById("directions").innerHTML += "<div style='text-align:justify; color:#767676; margin-bottom:10px;'>These directions are for planning purposes only. You may find that construction projects, traffic, weather, or other events may cause conditions to differ from the map results, and you should plan your route accordingly. You must obey all signs or notices regarding your route.</div>";
}

function toggleDirectionsPanel() {
    if (document.getElementById("directionsForm").style.display == "inline") {
        document.getElementById("directionsForm").style.display = "none";
        document.getElementById("directionsPanel").innerHTML = "Show Directions Panel";
        document.getElementById("logo").style.display = "inline";
        document.getElementById("myInput").focus();
    }
    else {
        document.getElementById("directionsForm").style.display = "inline";
        document.getElementById("directionsPanel").innerHTML = "Hide Directions Panel";
        document.getElementById("logo").style.display = "none";
        document.getElementById("start").focus();
    }
}

function prepareDirections() {
    document.getElementById("directionsForm").style.display = "inline";
    document.getElementById("directionsPanel").innerHTML = "Hide Directions Panel";
    document.getElementById("logo").style.display = "none";
    document.getElementById("end").value = "<?php echo $q ?>, Princeton, NJ 08544"; 
    document.getElementById("start").focus();
}

function geolocate() {
    var initialLocation;
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
        map.panTo(initialLocation);
        
        // marker to place on user location
        var blueDot = 'blue_dot_circle.png';

        var userMarker = new google.maps.Marker({
            position: initialLocation, 
            map: map, 
            title:"Your Location",
            icon: blueDot
        });
        userMarker.setMap(map);
        
        var foundUser = "<b>You are here!</b>";
        var infowindow = new google.maps.InfoWindow({
            content: foundUser
        });
        
        infowindow.open(map, userMarker);
        google.maps.event.addListener(userMarker, 'click', function() {
            infowindow.open(map, userMarker);
        });
        }, function() {
    
            alert("Sorry, your browser doesn't support the W3C Geolocation API.");
        });
    }
    else {
        alert("Sorry, your browser doesn't support the W3C Geolocation API.");
    }
}
</script>


<!-- Combo-handled YUI JS files: -->
<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.1/build/yahoo-dom-event/yahoo-dom-event.js&2.8.1/build/animation/animation-min.js&2.8.1/build/datasource/datasource-min.js&2.8.1/build/autocomplete/autocomplete-min.js&2.8.1/build/element/element-min.js&2.8.1/build/button/button-min.js&2.8.1/build/dragdrop/dragdrop-min.js&2.8.1/build/resize/resize-min.js&2.8.1/build/layout/layout-min.js"></script>

<script type="text/javascript">

(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;

    Event.onDOMReady(function() {
        var layout = new YAHOO.widget.Layout({
            units: [
                { position: 'top', height: 80, body: 'top1', gutter: '0px', collapse: false, resize: false, scroll: null, zIndex: 100000},
                { position: 'left', width: 350, resize: false, body: 'sidebar', gutter: '0px', collapse: true, close: false, collapseSize: 25, scroll: true, animate: true },
                { position: 'center', body: 'center1', gutter: '0px' }
            ]
        });
        
        layout.on('beforeResize', function() {
            var oldCoord = map.getCenter();
            google.maps.event.trigger(map, 'resize');
            map.panTo(oldCoord);
        });

        layout.render();
        
    });
})();
</script>
<script type="text/javascript">
    // my very own namespace =)
    YAHOO.namespace("adamchin");
</script>
        
<script type="text/javascript" src="data.js"></script>
<script type="text/javascript">
        
        // buttons
        var oSubmitButton1 = new YAHOO.widget.Button("search", { value: "query" });
        var oLinkButton = new YAHOO.widget.Button("linkbutton"); 
        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton1");
        var oPushButton2 = new YAHOO.widget.Button("pushbutton2");
            oSubmitButton1.on("click", calcRoute);
            oPushButton2.on("click", reverse);

        YAHOO.adamchin.BasicLocal = function() {
        // use a LocalDataSource
        var oDS = new YAHOO.util.LocalDataSource(YAHOO.adamchin.Data.arrayBuildings);
                            
        // optional to define fields for single-dimensional array
        oDS.responseSchema = {fields : ["building"]};
                                     
        // instantiate the AutoComplete
        var oAC = new YAHOO.widget.AutoComplete("myInput", "myContainer", oDS);
        oAC.prehighlightClassName = "yui-ac-prehighlight";
        oAC.useShadow = true;
        oAC.allowBrowserAutocomplete = false;
        oAC.queryMatchContains = true;
        oAC.animSpeed = 0.10;
        oAC.setHeader("<div style='font-size:77%; font-weight:bold; text-align:right; margin: 0 5px 1em 0;'>suggestions</div>");
        
        oAC.formatResult = function(e, h, d) {
            var f = h.replace(/\W/g," ");
            f = h.replace(/\s+/,"|");
            var g = new RegExp();
            g.compile("("+f+")", "gi");
            return d.replace(g,"<span style='font-weight:900;'>$1</span>")
        };

        // submit form once user selects from AutoComplete 
        oAC.itemSelectEvent.subscribe(onItemSelect);
        
        function onItemSelect(oSelf , elItem , oData) {
            document.forms["searchForm"].submit();
        }
        
        return {
            oDS: oDS,
            oAC: oAC
        };
        }();

        </script>
</body>
</html>
