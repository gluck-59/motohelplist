<?
include_once('../config/config.inc.php');
echo '<pre>'; 
// берет координаты из базы
// возвращает нужную часть адреса
if ($_GET['result'])
{
//    print_r($_GET);
    $limit = $_GET['limit'];
    $result = str_ireplace(' oblast', '', $_GET['result']);
    $stmt = $pdo->query('UPDATE `cities` SET region_new = "'.$result.'" where id_city = '.$_GET['id_city']);
}


$autosubmit = 1;



if (!$_GET['limit']) $limit = 0;
$stmt = $pdo->query('SELECT * FROM `cities` where `id_country`= 112 AND region_new = \'\' LIMIT '.$limit.',1 ');
while ($row = $stmt->fetch() )
{
    print_r($row);
echo '</pre>'; 
?>

            <!DOCTYPE html>
            <html>
              <head>
                <meta charset="utf-8">
                <title>Google Maps JavaScript API v3 Example: Reverse Geocoding</title>

<!-------- УСТАНОВКА ЯЗЫКА ОТВЕТА GOOGLE --------->                
                <script src="https://maps.googleapis.com/maps/api/js?v=3&language=en"></script>
    <link rel="stylesheet" href="sass/templates.css"/>


            <style type="text/css">
            html, body {
              height: 100%;
              margin: 0;
              padding: 0;
            }
            
            #map-canvas, #map_canvas {
              height: 100%;
            }
            
            @media print {
              html, body {
                height: auto;
              }
            
              #map_canvas {
                height: 300px;
              }
            }
            
            </style>
                <script>
            var geocoder;
            var map;
            var city = null;
            var county = null;
            var state = null;

/*
            if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
            } 
            //Get the latitude and the longitude;
             function successFunction(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            codeLatLng(lat, lng)
            }
            
            function errorFunction(){
            console.log("Geocoder failed");
            }
*/            
            function initialize() {
            geocoder = new google.maps.Geocoder();
            
                    var latlng = new google.maps.LatLng(40.730885,-73.997383);
                    var mapOptions = {
                      zoom: 8,
                      center: latlng,
                      mapTypeId: "roadmap"
                    }
                    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                google.maps.event.addListener(map, "click", function(evt){
                   document.getElementById("latlng").value = evt.latLng.toUrlValue(6);
                   codeLatLng(evt.latLng.lat(), evt.latLng.lng());
                });
            
                    codeLatLngFromInput();
            
            }
            
            function codeLatLngFromInput()
            {
                    var input = document.getElementById("latlng").value;
                    var latlngStr = input.split(",", 2);
                    var lat = parseFloat(latlngStr[0]);
                    var lng = parseFloat(latlngStr[1]);
                    var latlng = new google.maps.LatLng(lat, lng);
                    codeLatLng(lat,lng);
            
            }
            
            
            function codeLatLng(lat, lng) {
            city = null; 
            county = null;
            state = null;
            lat = <? echo $row->lat?>;
            lng = <? echo $row->lng?>;
          
            var latlng = new google.maps.LatLng(lat, lng);
            geocoder.geocode({"location":latlng}, function(results, status) {
              if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                 //formatted address
            
                document.getElementById("results").innerHTML += "<br>results[0].formatted_address=" + results[0].formatted_address+"<br>";
                //find country name
                    for (var i=0; i<results[0].address_components.length; i++) {
                    for (var b=0;b<results[0].address_components[i].types.length;b++) {
            
                    //there are different types that might hold a city admin_area_lvl_1 usually does in come cases looking for sublocality type will be more appropriate
                        if (results[0].address_components[i].types[b] == "administrative_area_level_1") {
                            //this is the object you are looking for
                            state= results[0].address_components[i];
                            break;
                        } if (results[0].address_components[i].types[b] == "administrative_area_level_2") {
                            //this is the object you are looking for
                            county= results[0].address_components[i];
                            break;
                        } if (results[0].address_components[i].types[b] == "administrative_area_level_3") {
                            //this is the object you are looking for
                            city= results[0].address_components[i];
                            break;
                        }
            
                    }
                }
                //city data
                // alert(city.short_name + " " + city.long_name)
                if (city) document.getElementById("results").innerHTML += "city data="+city.short_name + " " + city.long_name+"<br>";
                if (county) document.getElementById("results").innerHTML += "county data="+county.short_name + " " + county.long_name+"<br>";
                if (state) document.getElementById("results").innerHTML += "state data="+state.short_name + " " + state.long_name+"<br>";


var need;
if (state) need = state.short_name;       
//console.log(results[0]);
console.log(need);
                


selectlimit = <? echo $limit?>;
id_city = <? echo $row->id_city?>;
document.getElementById("latlng").value = lat+','+lng;
document.getElementById("id_city").value = id_city;

document.getElementById("limit").value = selectlimit+1;
document.getElementById("result").value = need;

autosubmit = <? echo $autosubmit?>;
if (autosubmit ==1) document.getElementById("setresult").submit();


            
                } else {
            //      alert("No results found");
                }
              } else {
            //    alert("Geocoder failed due to: " + status);
              }
            });
            }
            
            
                </script>
              </head>
            <body onload="initialize()">
                <div id="content" class="large-4">
                    <input id="latlng" type="textbox" value="">
                    <input type="button" value="Reverse Geocode" onclick="codeLatLngFromInput()">
                    
                    <br><br><br>                  

                    <form name="setresult" id="setresult" method="get" action="_geocode.php">
                        <input autofocus name="result" id="result" value="" type="text" placeholder="result">
                        <input name="limit" id="limit" value="" type="text" placeholder="limit">
                        <input name="id_city" id="id_city" value="" type="text" placeholder="id_city">
                        <input type="submit" class="small right round button">
                    </form>
                    <br><br>
                    <div id="map_canvas" style="height: 20px; width:20px; border: 1px solid black;"></div>
                    <p id="results"></p>
                </div>
                </script> 
                <script type="text/javascript"> 
                </script> 
            </body>
            </html>





<?php
}


print_r($rows);
if ($rows == '') echo 'Вроде как больше ничо нету';
echo '</pre>'; 
sleep(1);
?>