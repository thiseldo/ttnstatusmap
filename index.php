<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>The Things Network Gateways</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Map showing location of TTN Gateways" />
    <meta name="keywords" content="leaflet, map, javascript, thinginnovations" />
    <meta name="author" content="Andrew Lindsay, http://thinginnovations.uk based on Leaflet user map by Bryan R. McBride, GISP - http://bryanmcbride.com" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//cdn.leafletjs.com/leaflet-0.7.3/leaflet.css">
    <link rel="stylesheet" href="assets/css/app.css">

    <link rel="apple-touch-icon" href="assets/img/favicon-152.png">
    <link rel="shortcut icon" sizes="196x196" href="assets/img/favicon-196.png">
    <!--[if lte IE 8]><link type="text/css" rel="stylesheet" href="assets/leaflet/leaflet.ie.css" /><![endif]-->

    <link rel="stylesheet" href="assets/leaflet/plugins/leaflet-groupedlayercontrol/leaflet.groupedlayercontrol.css">
    <link rel="stylesheet" href="assets/leaflet/plugins/leaflet-awesome-markers/leaflet.awesome-markers.css">
    <link type="text/css" rel="stylesheet" href="assets/leaflet/plugins/leaflet.markercluster/MarkerCluster.css" />
    <link type="text/css" rel="stylesheet" href="assets/leaflet/plugins/leaflet.markercluster/MarkerCluster.Default.css" />

    <style type="text/css">
      html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        position: absolute;
        overflow:hidden;
      }
      #map {
        margin-top:40px;
        width:100%;
        height:100%;
      }
      #loading {
        position: absolute;
        width: 220px;
        height: 19px;
        top: 50%;
        left: 50%;
        margin: -10px 0 0 -110px;
        z-index: 20001;
      }
      #loading .loading-indicator {
        height: auto;
        margin: 0;
      }
      .navbar .brand {
        font-size: 25px;
        font-family: serif;
        font-weight: bold;
        color: white;
      }
      .navbar .nav > li > a {
        padding: 13px 10px 11px;
      }
      .navbar .btn, .navbar .btn-group {
        margin-top: 8px;
      }
      .leaflet-popup-content-wrapper, .leaflet-popup-tip {
        background: #f7f7f7;
      }
      .leaflet-control-geoloc {
        background-image: url(img/location.png);
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
      }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
      <script type="text/javascript">
      WebFontConfig = {
        google: {
            families: ['Norican::latin']
        }
      };
      (function () {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
      })();
      </script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-header">
        <div class="navbar-icon-container">
          <a href="#" class="navbar-icon pull-right visible-xs" id="nav-btn"><i class="fa fa-bars fa-lg white"></i></a>
        </div>
        <div class="navbar-brand" href="#">The Things Network Gateways</div>
      </div>
      <div class="navbar-collapse collapse">
      </div>
    </div>
    <div id="map"></div>
    <div id="loading-mask" class="modal-backdrop" style="display:none;"></div>
    <div id="loading" style="display:none;">
        <div class="loading-indicator">
            <img src="img/ajax-loader.gif">
        </div>
    </div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/1.3.0/handlebars.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
    <script src="//cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <script src="//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.24.0/L.Control.Locate.js"></script>

    <script src="assets/leaflet/plugins/leaflet-groupedlayercontrol/leaflet.groupedlayercontrol.js"></script>
    <script src="assets/leaflet/plugins/leaflet-awesome-markers/leaflet.awesome-markers.js"></script>
    <script type="text/javascript" src="assets/leaflet/plugins/leaflet.markercluster/leaflet.markercluster.js"></script>

    <script type="text/javascript">
      var map, mapquest, firstLoad;

      firstLoad = true;
      ttnGateways = new L.MarkerClusterGroup({spiderfyOnMaxZoom: true, showCoverageOnHover: false, zoomToBoundsOnClick: true});

      mapquest = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {
        maxZoom: 18,
        subdomains: ["otile1", "otile2", "otile3", "otile4"],
        attribution: 'Basemap tiles courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png">. Map data (c) <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors, CC-BY-SA.'
      });
var gwMarkerGreen = L.AwesomeMarkers.icon({
    icon: 'signal',
    markerColor: 'green'
});
var gwMarkerRed = L.AwesomeMarkers.icon({
    icon: 'signal',
    markerColor: 'red'
});

      map = new L.Map('map', {
        zoom: 3,
        layers: [mapquest, ttnGateways ]
      });

/* Larger screens get expanded layer control and visible sidebar */
if (document.body.clientWidth <= 767) {
  var isCollapsed = true;
} else {
  var isCollapsed = false;
}

var baseLayers = {
};

// Build this from the datasets
var groupedOverlays = {
  "Data Sets": {
        "Gateways": ttnGateways ,
  }
};

var layerControl = L.control.groupedLayers( baseLayers,  {
  collapsed: isCollapsed
}).addTo(map);

      // GeoLocation Control
      function geoLocate() {
        map.locate({setView: true, maxZoom: 10});
      }
      var geolocControl = new L.control({
        position: 'topright'
      });
      geolocControl.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'leaflet-control-zoom leaflet-control');
        div.innerHTML = '<a class="leaflet-control-geoloc" href="#" onclick="geoLocate(); return false;" title="My location"></a>';
        return div;
      };
      
      map.addControl(geolocControl);
      map.addControl(new L.Control.Scale());

// Uncomment this line to centre on your location
      map.locate({setView: true, maxZoom: 5});

      $(document).ready(function() {
        $.ajaxSetup({cache:false});
        $('#map').css('height', ($(window).height() - 40));
        getReadings();
      });

      $(window).resize(function () {
        $('#map').css('height', ($(window).height() - 40));
      }).resize();

      function geoLocate() {
        map.locate({setView: true, maxZoom: 17});
      }

      function initRegistration() {
        map.addEventListener('click', onMapClick);
        $('#map').css('cursor', 'crosshair');
        return false;
      }

      // Call php function getttn.php that queries ttnstatus.org and returns json data
      function getReadings() {
        $.getJSON("getttn.php", function (data) {
          for (var i = 0; i < data.length; i++) {
            if( data[i].location != null ) {
            	var loc = data[i].location.split(",");

              if( loc[0] != 0 && loc[1] != 0 ) {
            	var location = new L.LatLng(loc[0], loc[1]);
              var name = data[i].eui;
            	var eui = "<div style='font-size: 14px;'>EUI: "+ data[i].eui +"</div>";
            	var title = "<div style='font-size: 18px; color: #0078A8;'>"+ data[i].name +"</div>";
            	if (data[i].created_at.length > 0) {
              		var created = "<div style='font-size: 14px;'>Created: "+ data[i].created_at +"</div>";
            	} else var created = "";

            	if (data[i].updated_at.length > 0) {
              		var updated = "<div style='font-size: 14px;'>Updated: "+ data[i].updated_at +"</div>";
            	} else var updated = "";

            	if (data[i].last_seen.length > 0) {
              		var lastseen = "<div style='font-size: 14px;'>Last Seen: "+ data[i].last_seen +"</div>";
            	} else var lastseen = "";

            	if (data[i].remarks != null ) {
              		var remarks = "<div style='font-size: 14px;'>Remarks: "+ data[i].remarks +"</div>";
            	} else var remarks = "";

          		if( data[i].status == "up" ) {
            		var marker = new L.Marker(location, {
           	  	   title: name,
	    	  	       icon: gwMarkerGreen
           		   });
		            } else {
            		var marker = new L.Marker(location, {
           	  	   title: name,
	    	  	       icon: gwMarkerRed
           		   });
		            }
            	   marker.bindPopup("<div style='text-align: center; margin-left: auto; margin-right: auto;'>"+ eui + remarks + created + updated + lastseen +"</div>", {maxWidth: '400'});
                ttnGateways.addLayer(marker);
              }
            }
          }
        }).complete(function() {
          if (firstLoad == true) {
            map.fitBounds(ttnGateways.getBounds());
            firstLoad = false;
          };
        });
      }

      function showAlert( msg ) {
          //alert( msg );
	  $("#error-alert").html( msg );
          $('#insertErrorModal').modal('show');
          $("#loading-mask").hide();
          $("#loading").hide();
          return false;
      }

      function onMapClick(e) {
      }
    </script>

  </body>
</html>
