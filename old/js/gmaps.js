var id_user = parseInt(getCookie('id_user'));
var markerCluster;
var geocoder;
var markers = {};
var get;
var geocodeAddr = parseInt(localStorage.getItem('geocodeAddr'));
    
function initialise() {

    // если пришел GET с координатами, перемещаемся туда
    // геолокацию не включаем
    var lat = parseFloat($('span#getlat').text());
    var lng = parseFloat($('span#getlng').text());
    if (lat && lng)
    {
        get = 1;
        var lat = lat;
        var lng = lng;
        var zoom = 18;
//map = new google.maps.Map(document.getElementById("map"), myOptions);
//var latLng = new google.maps.LatLng(lat, lng);
///console.warn('а какого d хуя маркер не ставится? (gmaps.js:20)');
    customicon =1;

    } 
    else // если GET нету, включаем геолокацию
    {
        if (!localStorage.getItem('initLat') || !localStorage.getItem('initLng'))
        { 
            var lat = 53.1424223;
            var lng = 29.2242762;            
            localStorage.setItem('initLat', '53.1424223');
            localStorage.setItem('initLng', '29.2242762');
        }
        else
        {
            var lat = parseFloat(localStorage.getItem('initLat'));
            var lng = parseFloat(localStorage.getItem('initLng'));            
        }
        
        if (window.location.pathname == '/trip.php') 
        {
            var zoom = 17;
        }
        else 
        {
            var zoom = parseInt(localStorage.getItem('initZoom'));
            if (isNaN(zoom)) var zoom = 14;
        }
        customicon = 0; 
        prepareGeolocation(); // непонятно что делает, живет в geometa.js
        doGeolocation(); // вынесено в tools.js
//console.log('get '+get);        
    }      

    var latlng = new google.maps.LatLng(lat, lng);
    
    var myOptions = {
      zoom: zoom,
      center: latlng,
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
        position: google.maps.ControlPosition.TOP_LEFT,
    },
    scaleControl: true,
    scrollwheel: true,
    panControl: true,
    zoomControl: true,
    TextSearchRequest: true,
    streetViewControl: true,
    overviewMapControl: true,
    overviewMapControlOptions: {
        opened: true,
    },
    mapTypeId: google.maps.MapTypeId.ROADMAP
    }



    map = new google.maps.Map(document.getElementById("map"), myOptions);

//console.log('get '+get);

    if (get == 1) placeMarkerImHere(latlng,customicon);


    if (document.getElementById('submitgeocoder'))
    {
        var geocoder = new google.maps.Geocoder();    
        document.getElementById('submitgeocoder').addEventListener('click', function() 
        {
            geocodeAddress(geocoder, map);
        });
    }

    // слушаем событие карты и грузим маркеры placeMarkers(map)
    // gmaps-samples-v3.googlecode.com/svn/trunk/map_events/map_events.html
    google.maps.event.addListener(map, 'idle', function()
    {
        placeMarkers(map);
    }); 

/*
    google.maps.event.addListener(map, 'dragend', function()
    {
        placeMarkers(map);
    }); 
    google.maps.event.addListener(map, 'zoom_changed', function()
    {
        placeMarkers(map);
    }); 
*/
    
    if (window.location.pathname == '/trip.php') 
    { 
        $('div#map').css('height', '50vh');
/*        setTimeout(
        function()
        {
               map.setZoom(16);
        }, 1000);*/
    }

  }
// end of Init




function placeMarkers(map) 
{
    if (window.location.pathname == '/map.php')
    {
        if (!markerCluster)
        {
            markerCluster = new MarkerClusterer(map, [], {
            gridSize: 50, 
            maxZoom: 11
            }); 
        }
    
        //markerCluster.clearMarkers();
        //markers = [];
        var bounds = map.getBounds();
        //markers.length = 0;
        request = {};
        request.task = "getPOIs";
        request.bounds = true; 
        
        
        if (request.bounds && bounds)
        {
            request.bounds_ne_lat = bounds.getNorthEast().lat();
            request.bounds_ne_lng = bounds.getNorthEast().lng();
            request.bounds_sw_lat = bounds.getSouthWest().lat();
            request.bounds_sw_lng = bounds.getSouthWest().lng();
        }
        
    //console.log(request);
        
        $.post(
        "map.php",
        request,
        function(xml)
        { 
            var i = 0;
            $(xml).find("marker").each(function()
            { 
              
              if (markers[$(this).find('map_icon').text()+$(this).find('id').text()])
                  return true;                  
                poi = {};

                poi.id = $(this).find('id').text(); 
                poi.name = $(this).find('name').text(); 
                poi.price = $(this).find('price').text(); 
                poi.phone1 = $(this).find('phone1').text(); 
                poi.phone2 = $(this).find('phone2').text();       
                poi.parking = $(this).find('parking').text(); 
                poi.ac = $(this).find('ac').text();       
                poi.wifi = $(this).find('wifi').text();       
                poi.sauna = $(this).find('sauna').text();
                poi.description = $(this).find('description').text();      
                poi.map_icon = $(this).find('map_icon').text();        
                poi.access = $(this).find('access').text();                    
                poi.camera = $(this).find('camera').text();                    
                poi.security = $(this).find('security').text();
                poi.big = $(this).find('big').text();
                poi.electric = $(this).find('electric').text();            
                poi.weld = $(this).find('weld').text();                        
                poi.stock = $(this).find('stock').text();                                    
                poi.tuning = $(this).find('tuning').text();                                                
                poi.renewal = $(this).find('renewal').text();                                                            
                poi.germans = $(this).find('germans').text();
                poi.japanese = $(this).find('japanese').text();            
                poi.chinese = $(this).find('chinese').text();                        
                poi.podkat = $(this).find('podkat').text();                                    
                poi.balancer = $(this).find('balancer').text();                                                
                poi.rims = $(this).find('rims').text();                                                            
                poi.tire_repair = $(this).find('tire_repair').text();       
                poi.owner = $(this).find('owner').text();            
                poi.lat = $(this).find('lat').text(); 
                poi.lng = $(this).find('lng').text(); 
                
                // добавим маркер на карту 
                var latLng = new google.maps.LatLng(parseFloat(poi.lat),parseFloat(poi.lng));      
                var marker = new google.maps.Marker(
                { 
                    position: latLng, 
                    map: map,
                    icon: 'img/icons/'+poi.map_icon+'.png',
                    clickable: true
                }); 
                poi.marker = marker;
                //markers[poi.map_icon+poi.id]= {};
                //markers[poi.map_icon+poi.id]['marker'] = marker;
                markers[poi.map_icon+poi.id] = poi;
                markerCluster.addMarker(marker);            
    
//console.log('id_user '+id_user);
//console.log('poi.owner '+poi.owner);
    
                // создадим текстовую подсказку 
                var infoWindow = new google.maps.InfoWindow(); 
                var html1 = (poi.name ? '<b>'+poi.name+'</b><br>' : '');
                var edit = (parseInt(poi.owner) == id_user ? '<div class="right"><a href="" onclick="updatePOI(\''+poi.map_icon+poi.id+'\');   markers[\''+poi.map_icon+poi.id+'\'].marker.setMap(null); return false" >Править</a></div>' : '');
                var html = (poi.price ? poi.price+' руб в сутки<br>' : '')
                +(poi.phone1 ? 'Тел.: <a href="tel:+'+poi.phone1+'">+'+poi.phone1+'</a> ' : '')
                +(poi.phone2 ? '&nbsp;&nbsp;<a href="tel:+'+poi.phone2+'">+'+poi.phone2+'</a>' : '')
                +(poi.phone1 || poi.phone2 ? '<br>' : '' )
                +(poi.parking == 1 ? '<span class="radius label">паркинг во дворе</span>' : '')      
                +(poi.ac == 1 ? '<span class="radius label">кондиционер</span>' : '')
                +(poi.wifi == 1 ? '<span class="radius label">Wi-Fi</span>' : '')
                +(poi.sauna == 1 ? '<span class="radius label">баня/сауна</span>' : '')
                +(poi.access == 1 ? '<span class="radius label">Круглосуточный доступ</span>' : '')
                +(poi.camera == 1 ? '<span class="radius label">Видеонаблюдение</span>' : '')
                +(poi.security == 1 ? '<span class="radius label">Трезвая охрана</span>' : '')
                +(poi.big == 1 ? '<span class="radius label">Несколько мотомест</span>' : '')
                +(poi.electric == 1 ? '<span class="radius label">Электрика</span>' : '')
                +(poi.weld == 1 ? '<span class="radius label">Сварка</span>' : '')            
                +(poi.stock == 1 ? '<span class="radius label">Расходники</span>' : '')                        
                +(poi.tuning == 1 ? '<span class="radius label">Глубокий тюнинг</span>' : '')                                    
                +(poi.renewal == 1 ? '<span class="radius label">Капиталка</span>' : '')
                +(poi.germans == 1 ? '<span class="radius label">Немцы</span>' : '')            
                +(poi.japanese == 1 ? '<span class="radius label">Японцы</span>' : '')
                +(poi.chinese == 1 ? '<span class="radius label">Китайцы</span>' : '')            
                +(poi.podkat == 1 ? '<span class="radius label">Подкат</span>' : '')            
                +(poi.balancer == 1 ? '<span class="radius label">Балансировка</span>' : '')            
                +(poi.rims == 1 ? '<span class="radius label">Правка/варка дисков</span>' : '')                        
                +(poi.tire_repair == 1 ? '<span class="radius label">Варка покрышек</span>' : '')                                    
                +(poi.description ? '<br>'+poi.description : '')
                +'<hr><div class="small right">Скопировать координаты: <input readonly value="'+poi.lat+','+poi.lng+'"></div>'
                ; 
                i++;
                
                
               
                // добавим слушатель события для маркеров 
                google.maps.event.addListener(marker, 'click', function() 
                { 
                    //показывает адреса если включено в настройках
                    if (geocodeAddr == 1)
                    {
                        reverseGeocode(marker);
                        setTimeout(function(){ infoWindow.setContent(edit+html1+ (address ? address+'<br>' : '') +html)},100); 
                    }                    

                    infoWindow.setContent(edit+html1+html)
                    infoWindow.open(map, marker); 
                }); 
            }); 
        });
/*
                $.each(markers,function(key, value){
        console.log(key,value);

        if (value)
        if (bounds.contains(value.marker.position) == false) {
            markers.splice(key, 1);
        }
           
        });
*/
    }
}

    
  
  
    function reverseGeocode(marker) 
    {
        //console.log(marker.position);
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({latLng: marker.position},reverseGeocodeResult);
        var city, region;
    }
    
    function reverseGeocodeResult(results, status) 
    {
        currentReverseGeocodeResponse = results;
        if(status == 'OK') 
        {
            if(results.length == 0) 
            {
                console.log('None');
            } 
            else 
            {
                address = results[0].formatted_address;
                city = results[0].address_components[2].long_name;
                region = results[0].address_components[4].long_name;
//console.log(results[0].address_components[2].long_name);
//console.log(results[0].address_components[4].long_name);
            }
        } 
        else 
        {
            address = '';
            console.log('Error');
        }
    }
    
    

    
    

    // ищет место на карте по координатам или по городу
    function geocodeAddress(geocoder, resultsMap) {
      var address = document.getElementById('geocoderaddress').value;
      geocoder.geocode({'address': address}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
          resultsMap.setCenter(results[0].geometry.location);
          resultsMap.setZoom(15);
/*          var marker = new google.maps.Marker({
            map: resultsMap,
            position: results[0].geometry.location
          });*/
        } else {
          ohSnap('Ничего не найдено. ' + status);
        }
      });
  
    }    
  
  

  function contains(array, item) {
	  for (var i = 0, I = array.length; i < I; ++i) {
		  if (array[i] == item) return true;
		}
		return false;
	}


/*document.addEventListener("deviceready", onDeviceReady, false);
function onDeviceReady() {
initialise();    
    console.log("console.log works well");
} */

