function initialise(lat, lng) 
{
    // если пришел GET с lat,lng - перемещаемся туда
    // геолокацию не включаем
    // разрешаем custom icon
    if (lat && lng)
    {
        get = 1;
        var lat = lat;
        var lng = lng;
        var zoom = 16;
        customicon = 1;
    } 
    else // если GET нету, включаем геолокацию
    {
        var zoom = parseInt(myApp.formGetData('settings').initZoom);
        if (isNaN(zoom)) var zoom = 14;

        customicon = 0; 
        //prepareGeolocation(); // непонятно что делает, живет в geometa.js
        doGeolocation(); 

        //console.log('get',get);
        //console.log('customicon',customicon);
    }      

    if (get == 1)
        latlng = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
    else
        latlng = new google.maps.LatLng(parseFloat(myApp.ls.lastLat), parseFloat(myApp.ls.lastLng)); // кажется здесь получается бабруйск при первой загрузке


    myOptions = {
      center: latlng,
      mapTypeControl: true,
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
        mapTypeIds: [
        google.maps.MapTypeId.ROADMAP,
        google.maps.MapTypeId.TERRAIN,
        google.maps.MapTypeId.HYBRID,        
      ]        
    },
    scaleControl: true,
    scrollwheel: true,
    panControl: true,
    zoomControl: true,
    TextSearchRequest: true,
    streetViewControl: true,
    overviewMapControl: false,
    overviewMapControlOptions: {
        opened: true,
        }
    }


    map = new google.maps.Map(document.getElementById("map"), myOptions);
    map.setCenter(latlng);
    map.setZoom(zoom);

if(parseInt(myApp.ls.inTrip) == 0 || customicon == 1)
    placeMarkerImHere(latlng);


    if (document.getElementById('submitgeocoder'))
    {
        var geocoder = new google.maps.Geocoder();    
        document.getElementById('submitgeocoder').addEventListener('click', function() 
        {
            geocodeAddress(geocoder, map);
        });
    }


    // грузим маркеры из базы
    setTimeout(function(){
            placeMarkers(map);
        }, 300);


    // google пробки
    if (parseInt(myApp.formGetData('settings').showTraffic) == 1)
    {
        var trafficLayer = new google.maps.TrafficLayer();
        trafficLayer.setMap(map);
    }


get = 0;
customicon = 0;

} // end of Init




function placeMarkers(map)
{
    var pois = [];
    markers = [];
    var markerCluster = {};
    var geocodeAddr = parseInt(myApp.formGetData('settings').geocodeAddr);
    var clusterPoi = parseInt(myApp.formGetData('settings').clusterPoi);
    var showTraffic = parseInt(myApp.formGetData('settings').showTraffic);
    var showPois = myApp.formGetData('settings').pois;    
    var showTripFriendsOnly = parseInt(myApp.formGetData('settings').showTripFriendsOnly);    
    
    if (inArray('hotels', showPois))
        for (var i = 0; i < hotels.length; i++) 
            pois.push(hotels.item(i));
    
    if (inArray('parkings', showPois))    
        for (var i = 0; i < parkings.length; i++) 
            pois.push(parkings.item(i));
    
    if (inArray('places', showPois))        
        for (var i = 0; i < places.length; i++) 
            pois.push(places.item(i));
    
    if (inArray('services', showPois))            
        for (var i = 0; i < services.length; i++) 
            pois.push(services.item(i));
    
    if (inArray('tireservices', showPois))                
        for (var i = 0; i < tireservices.length; i++) 
            pois.push(tireservices.item(i));
    

    for (var i = 0; i < trips.length; i++) 
    { 
        var trip = {};
        trip.accuracy = trips.item(i).accuracy;
        trip.id_city_finish = trips.item(i).id_city_finish;            
        trip.id_city_start = trips.item(i).id_city_start;            
        trip.id_trip = trips.item(i).id_trip;                        
        trip.id_user = trips.item(i).id_user;                                    
        trip.lat = trips.item(i).lat;
        trip.lng = trips.item(i).lng;
        trip.ts = trips.item(i).ts;
        // иконка друзей
        if(trips.item(i).name)
        {
            trip.name = trips.item(i).name;
            trip.map_icon = 'tripfriend';
        }
        // моя иконка
        else if (trip.id_user == myIdUser)
        {
            trip.map_icon = 'tripim';
        }
        // обычная иконка
        else
            trip.map_icon = 'trip';

        // показывать только друзей?
        if (showTripFriendsOnly == 1)
        {
            if (trip.name || trip.id_user == myIdUser)
                pois.push(trip);                
        }
        else
            pois.push(trip);
    }


    //console.log('hotels',hotels.length, 'parkings',parkings.length, 'places',places.length, 'services',services.length,'tireservices',tireservices.length, 'trips',trips.length);


    markerCluster = new MarkerClusterer(map, [], {
        gridSize: 50,           // размеры "квадратов" на карте, в которых будут считаться POI для объединения
        minimumClusterSize: 2, // минимальное колво POI в одном кластере
        maxZoom: clusterPoi,
        imagePath :  'https://rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m'
    }); 


    $$.each(pois, function(i) 
    { 
        // добавим маркеры на карту 
        var latLng = new google.maps.LatLng(parseFloat(pois[i].lat),parseFloat(pois[i].lng));      
        var marker = new google.maps.Marker(
        { 
            position: latLng, 
            map: map,
            icon: 'img/'+pois[i].map_icon+'.png',
//            markerid: pois[i].map_icon+pois[i].id,
            clickable: true
        }); 

        pois[i].marker = marker;
        markers[pois[i].map_icon+pois[i].id] = marker;
        
        // если это иконка trip — не включаем ее в кластер
        if (pois[i].map_icon != 'trip' && pois[i].map_icon != 'tripfriend' && pois[i].map_icon != 'tripim')
        {
            markerCluster.addMarker(marker);
        }
        else // установим прозрачность иконок trip в зависимости от прошедшего времени
        {
            var timediff = (moment.now()/1000).toFixed() - pois[i].ts;
            var opacity = 1;
            var zIndex = 300;
            if (timediff > 43200) opacity = 0.8;
            if (timediff > 86400) opacity = 0.6;
            if (timediff > 172800) opacity = 0.5, zIndex = 200;
            if (timediff > 432000) opacity = 0.4, zIndex = 200;
            marker.setOptions({'opacity': opacity, 'zIndex': zIndex});
        }

        
        
        // добавим слушатель события для маркеров 
        google.maps.event.addListener(marker, 'click', function() 
        { 
            // создадим текстовую подсказку 
            var infoWindow = new google.maps.InfoWindow(); 
            var html1 = (pois[i].name ? '<b>'+pois[i].name+'</b> '
            //+(pois[i].seen_here ? '<br><span>'+seen_here : '')
            +'<br>' : '');

            // показывает "править" в зависимости от owner
            // разрешает "править" админам
            if ( (myIdUser < 3 || parseInt(pois[i].owner) == myIdUser) && !pois[i].accuracy)
                var editing = '<div class="right"><a class="updatepoi" data-idpoi="'+pois[i].id+'" data-typepoi="'+pois[i].map_icon+'">Править</a></div>';
            else var editing = '';


            var profile = (pois[i].id_user ? '<div class="right"><a href="#" class="external" onclick="getProfile('+pois[i].id_user+')" >&nbsp;Профиль</a></div>' : '');

            var html = (pois[i].price ? pois[i].price+' в сутки<br>' : '')
            +(pois[i].city ? ''+pois[i].city : '') 
            +(pois[i].accuracy ? '<br>Точность: '+pois[i].accuracy+'м' : '') 
            //+(pois[i].motorcycle ? '<br>'+pois[i].motorcycle+'<br>' : '')                
            +(pois[i].phone1 ? 'Тел.: <a href="tel:+'+pois[i].phone1+'" class="external">+'+pois[i].phone1+'</a> ' : '')
            +(pois[i].phone2 ? '&nbsp;&nbsp;<a href="tel:+'+pois[i].phone2+'" class="external">+'+pois[i].phone2+'</a>' : '')
            +(pois[i].phone1 || pois[i].phone2 ? '<br>' : '' )
            +(pois[i].parking == 1 ? '<span class="blue tip">паркинг во дворе</span>' : '')      
            +(pois[i].ac == 1 ? '<span class="blue tip">кондиционер</span>' : '')
            +(pois[i].wifi == 1 ? '<span class="blue tip">Wi-Fi</span>' : '')
            +(pois[i].sauna == 1 ? '<span class="blue tip">баня/сауна</span>' : '')
            +(pois[i].access == 1 ? '<span class="blue tip">Круглосуточный доступ</span>' : '')
            +(pois[i].camera == 1 ? '<span class="blue tip">Видеонаблюдение</span>' : '')
            +(pois[i].security == 1 ? '<span class="blue tip">Трезвая охрана</span>' : '')
            +(pois[i].big == 1 ? '<span class="blue tip">Несколько мотомест</span>' : '')
            +(pois[i].electric == 1 ? '<span class="blue tip">Электрика</span>' : '')
            +(pois[i].weld == 1 ? '<span class="blue tip">Сварка</span>' : '')            
            +(pois[i].stock == 1 ? '<span class="blue tip">Расходники</span>' : '')                        
            +(pois[i].tuning == 1 ? '<span class="blue tip">Глубокий тюнинг</span>' : '')                                    
            +(pois[i].renewal == 1 ? '<span class="blue tip">Капиталка</span>' : '')
            +(pois[i].germans == 1 ? '<span class="blue tip">Немцы</span>' : '')            
            +(pois[i].japanese == 1 ? '<span class="blue tip">Японцы</span>' : '')
            +(pois[i].chinese == 1 ? '<span class="blue tip">Китайцы</span>' : '')            
            +(pois[i].podkat == 1 ? '<span class="blue tip">Подкат</span>' : '')            
            +(pois[i].balancer == 1 ? '<span class="blue tip">Балансировка</span>' : '')            
            +(pois[i].rims == 1 ? '<span class="blue tip">Правка/варка дисков</span>' : '')                        
            +(pois[i].tire_repair == 1 ? '<span class="blue tip">Варка покрышек</span>' : '')                                    
            +(pois[i].description ? '<br>'+pois[i].description : '')
            +'<hr><div class="small right">Скопировать координаты: <input readonly value="'+pois[i].lat+','+pois[i].lng+'"></div>'
            ; 
        

            //показывает адреса если включено в настройках
            if (geocodeAddr == 1)
            {
                reverseGeocode(marker);
                setTimeout(function(){ infoWindow.setContent(editing+profile+html1+html+ (address ? '<br>'+address : '') )},500); 
            }
            
            // тащит инфу о юзере с сервера если это trip
            if (!isNaN(pois[i].accuracy))
            {
                html1 = html1+'<br><center><img src="img/loader.gif"></center>';

                $$.ajax({
                    method: 'GET',
                    url: API_URL+"users/"+pois[i].id_user,
                    success: function(user)
                    { 
                        var user = JSON.parse(user);

                        // проверяет active юзера и показывает телефон
                        if (myApp.formGetData('profile').active == '1') 
                            var tel = '<br><a href="tel:+'+user[0].phone+'" class="external" >+'+user[0].phone+'</a>';
                        else var tel = '';

                        html1 = '<b>'+user[0].name+'</b>';
                        
                        if (pois[i].ts < (moment.now()/1000).toFixed() )
                            html1 = html1+ ', <span time="'+pois[i].ts+'"></span>';
                        
                        html1 = html1 +'<br>'+user[0].motorcycle+'<br>'
                        +user[0].city
                        +tel;

                        infoWindow.setContent(editing+profile+html1+html);
                        infoWindow.open(map, marker); 
                        doMoment();
                        return;
                    }, 
                    error:  function(response)
                    {
                        console.log('pois[i].id_user ERROR',JSON.parse(response));
                    }
                });
            }
            
            infoWindow.setContent(editing+profile+html1+html);
            infoWindow.open(map, marker); 
            setTimeout(function(){ doMoment() },100); 

            $$('.updatepoi').on('click', function (e) 
            {
                updatepoi(e.target.dataset.idpoi, e.target.dataset.typepoi);
            });
            
        });   
    });
    

} // end of placeMarkers
  
  
  
  
  
  

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
function geocodeAddress(geocoder, resultsMap) 
{
    var address = document.getElementById('geocoderaddress').value;
    geocoder.geocode({'address': address}, function(results, status) 
    {
        if (status === google.maps.GeocoderStatus.OK) 
        {
            resultsMap.setCenter(results[0].geometry.location);
            resultsMap.setZoom(15);
            /*var marker = new google.maps.Marker(
                {
                    map: resultsMap,
                    position: results[0].geometry.location
                });*/
        } else {
            myApp.addNotification(
            {
                title: 'Ничего не найдено',
                message: status,
                hold: 2000
            });          
        }
    });
}
  
  

function contains(array, item) {
  for (var i = 0, I = array.length; i < I; ++i) {
	  if (array[i] == item) return true;
	}
	return false;
}




// ставит маркер "я здесь"        
//function placeMarkerImHere(latlng,customicon)
function placeMarkerImHere(latlng)
{    
    if (customicon == 1) 
    {
        var icon = {
        path: google.maps.SymbolPath.CIRCLE,
        fillOpacity: 0.8,
        fillColor: '#A00',
        strokeOpacity: 1.0,
        strokeColor: '#fff',
        strokeWeight: 0, 
        scale: 10 //pixels
        }
    }

    var marker = new google.maps.Marker(
    {
        map: map,
        position: latlng,
        icon: icon,
        title: 'Why, there you are!',
        zoom: parseInt(myApp.formGetData('settings').initZoom),
    });
    
    (new google.maps.Geocoder()).geocode({latLng: latlng}, function(resp) 
    {
        var place = "<b>Я где-то здесь</b>";
        if (resp && resp[0]) {
          var bits = [];
          for (var i = 0, I = resp[0].address_components.length; i < I; ++i) {
        	  var component = resp[0].address_components[i];
        	  if (contains(component.types, 'political')) {
        		  bits.push('<b>' + component.long_name + '</b>');
        		}
        	}
        	if (bits.length) {
//            		place = bits.join(' > ');
        		place = place;
        	}
        	marker.setTitle(resp[0].formatted_address);
            marker.setOptions({'zIndex': 300});
        }
        
        // добавим слушатель события click для маркера 
        var infoWindow = new google.maps.InfoWindow(); 
        google.maps.event.addListener(marker, 'click', function() 
        { 
            infoWindow.setContent(place+'<br>'+resp[0].formatted_address+'<hr><div class="small right">Скопировать координаты: <input readonly value="'+latlng.lat().toFixed(6)+','+latlng.lng().toFixed(6)+'"></div>'); 
            infoWindow.open(map, marker); 
        }); 
    });    
}



// получает свою геопозицию
// http://badrit.com/blog/2013/9/25/javascript-and-geolocation-tricks
function doGeolocation()
{
    if (navigator.geolocation) 
    {
        if (parseInt(myApp.ls.inTrip) == 0)
        {
            navigator.geolocation.getCurrentPosition(positionSuccess, positionError, {enableHighAccuracy:true, timeout: 999999999, maximumAge: 180000 });
        }
        else
        {
            //navigator.geolocation.watchPosition(positionSuccess, positionError);            
            // watchPosition постоянно обновляет карту и она дергается            
            navigator.geolocation.getCurrentPosition(positionSuccess, positionError, {enableHighAccuracy:true, timeout: 999999999, maximumAge: 0 });
        }
    } else {
    positionError(-1);
    }
}
  

// геопозиция не получена
function positionError(err) 
{
    var msg;
    switch(err.code) 
    {
case err.TIMEOUT:
navigator.geolocation.getCurrentPosition(onSuccess, onError, { maximumAge: 3000, timeout: 20000, enableHighAccuracy: false } );
break;

      case err.UNKNOWN_ERROR:
        msg = "Ошибка определения геолокации";
        break;
      case err.PERMISSION_DENINED:
        msg = "Определение координат отключено в настройках телефона";
        break;
      case err.POSITION_UNAVAILABLE:
        msg = "Невозможно определить местоположение";
        break;
      case err.BREAK:
        msg = "Не могу найти спутники геолокации";
        break;
      default:
        msg = "Включи определение координат в настройках телефона.";
    }

    myApp.addNotification({
        title: msg,
        hold: 4000
    });
    
    setTimeout(function() { 
        latlng = new google.maps.LatLng(parseFloat(myApp.ls.lastLat), parseFloat(myApp.ls.lastLng));
        var zoom = parseInt(myApp.formGetData('settings').initZoom);
        if (isNaN(zoom)) var zoom = 14;

//map.setCenter(latlng); // вроде надо, но постояно генерит ошибку
map.setZoom(zoom);

    }, 1000)
}


// геопозиция получена удачно
function positionSuccess(position) 
{
    coords = position.coords || position.coordinate || position;
    
    localStorage.setItem('lastLat',coords.latitude);
    localStorage.setItem('lastLng',coords.longitude);    

if (mainView.url == 'map.html' && map.length > 0)
    map.setZoom(parseInt(myApp.formGetData('settings').initZoom));


    if (parseInt(myApp.ls.inTrip) == 1 && myApp.ls.token)
    {
        //console.log('пишем id_trip', parseInt(myApp.ls.inTrip), coords.latitude, coords.longitude, 'accuracy', coords.accuracy); 
        var data = {};
        data.lat = coords.latitude;
        data.lng = coords.longitude;
        data.accuracy = coords.accuracy;
        data.ts = (position.timestamp/1000).toFixed();
    
        $$.ajax(
        {
            method: 'POST', 
            url: API_URL+'map/trip',
            data: JSON.stringify(data),
            success: function(response)
            {
                // если в ответ пришло число, пишем трип в локальную базу 
                if ( !isNaN(parseInt(response)) )
                {
                    db.transaction(function (tx) 
                    {
                        tx.executeSql('INSERT OR REPLACE INTO trips (id_trip, id_user, lat, lng, accuracy, ts) VALUES ('+response+','+myIdUser+','+coords.latitude+','+coords.longitude+','+coords.accuracy+','+(position.timestamp/1000).toFixed()+')');
                    }); 
                }                
            },
            error: function(response)
            {
                if (response.status != 401)
                    console.warn('trip error', response);
            },
            complete: function() {
                //console.log('trip complete');
            }
        });
    }
    return coords;
}




// проверяет включен ли "в путь"
// 
function checktrip() 
{
    if ($$('input#trip')[0].checked)
    {
        $$('div#triphint').hide();
        $$('#tripmap').show();
        $$('span.preloader').show();        
        localStorage.setItem('inTrip', 1);
        
        if (!navigator.geolocation)
        {
            myApp.addNotification({
            title: 'Геолокация не поддерживается этим устройством',
            message: '',
            hold: 4000
        });                                      
            $$('span.preloader').hide();        
            return;
        }
        
        
        function success(position) 
        {
            getStaticmap(position);
            //console.log(position);
        };
        
        
        function error() { 
            myApp.addNotification({
            title: 'Невозможно определить координаты',
            message: 'Возможно телефон не видит спутники',
            hold: 4000
            });
            $$('span.preloader').hide();            
        };
        navigator.geolocation.getCurrentPosition(success, error);
    }
    else 
    {
        $$('div#triplink').hide();
        localStorage.setItem('inTrip', 0);
        $$('#tripmap').hide();
        $$('span.preloader').hide();
        $$("#tripmap img").detach();
        $$('div#triphint').show();        

        // удалим свой трип из базы
        deleteTrip();
    }
}




// удаляет свой трип из локальной базы и с сервера
function deleteTrip()
{
db.transaction(function (tx) {
    tx.executeSql('delete FROM trips WHERE id_user = '+myIdUser,[], function(tx, result) { 
        }, null);});             
    
$$.ajax(
{
    method: 'DELETE', 
    url: API_URL+'map/trip',
    success: function(response)
    {
        if (response.statement == 1)
        {
            myApp.addNotification({
                title: 'Удалено',
                message: 'Нам будет не хватать тебя',
                hold: 2000
            });                          
        }
    },
    error: function(response)
    {
        console.log('trip delete', response);
    },
    complete: function() {
        //console.log('trip delete complete');
    }
});
}




// получает картинку с картой
function getStaticmap(position)
{
    var latitude  = position.coords.latitude;
    var longitude = position.coords.longitude;
    var img = new Image();
    img.onload = function() 
    {
        var output = document.getElementById("tripmap");
        output.appendChild(img);
        $$('div#triplink').show();
        $$('span.preloader').hide();
     };
    img.onerror = function() 
    { 
        myApp.addNotification({
            title: 'Нет интернета',
            message: '',
            hold: 4000
        });                          
    };             
    img.src = "//maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=15&size="+window.innerWidth+"x"+(window.innerHeight/3).toFixed()+"&markers=" + latitude + "," + longitude;    
}




// спрашивает у сервера что изменилось в hotels, places.... с момента since
// пишет измененные в базу
function loadpois()
{
    //console.log('loadpois start');
    $$.ajax(
    {
        method: 'POST', // нельзя использовать GET потому что все кэшируется
        url: API_URL+'map/loadpois',
        data: JSON.stringify({"since": localStorage.getItem('pois_updated')}),
        success: function(response)
        {
            //console.warn('loadpois', response);
            try {
                    var pois = JSON.parse(response);
                    //console.log('hotels:',pois.hotels.length, 'parkings:',pois.parkings.length, 'places:',pois.places.length, 'services:',pois.services.length, 'tireservices',pois.tireservices.length);                                    
                
                    // hotels
                    var hotels = pois.hotels;
                    $$.each(hotels, function(i) 
                    {
                        db.transaction(function(tx) 
                        {
                            tx.executeSql("INSERT OR REPLACE INTO hotels (id, name, lat, lng, phone1, phone2, price, parking, ac,wifi,sauna,description,owner,map_icon,date_upd) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);",
                                [ hotels[i].id_hotel, hotels[i].name, hotels[i].lat, hotels[i].lng, hotels[i].phone1, hotels[i].phone2, hotels[i].price, hotels[i].parking, hotels[i].ac, hotels[i].wifi, hotels[i].sauna, hotels[i].description, hotels[i].owner, hotels[i].map_icon, hotels[i].ts])
                        });
                        console.log('hotel -> db');
                    });

                    
                    // parkings                        
                    var parkings = pois.parkings;
                    $$.each(parkings, function(i) 
                    {
                        db.transaction(function(tx) 
                        {
                            tx.executeSql("INSERT OR REPLACE INTO parkings (id, phone1,phone2,lat,lng,price,access,camera,security,big,description,owner,id_city,map_icon,date_upd) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);",
                                [ parkings[i].id_parking, parkings[i].phone1, parkings[i].phone2, parkings[i].lat, parkings[i].lng, parkings[i].price, parkings[i].access, parkings[i].camera, parkings[i].security, parkings[i].big, parkings[i].description, parkings[i].owner, parkings[i].id_city, parkings[i].map_icon, parkings[i].ts ])
                        });
                        console.log('parking -> db');    
                    });
                    
                    
                    // parkings
                    var places = pois.places;
                    $$.each(places, function(i) 
                    {
                        db.transaction(function(tx) 
                        {
                            tx.executeSql("INSERT OR REPLACE INTO places (id,lat,lng,name,id_city,description,owner,photo,map_icon,date_upd) values(?,?,?,?,?,?,?,?,?,?);",
                                [ places[i].id_place, places[i].lat, places[i].lng, places[i].name, places[i].id_city, places[i].description, places[i].owner, places[i].photo, places[i].map_icon, places[i].ts ])
                        });
                        console.log('places -> db');        
                    });
                    
                    
                    // services
                    var services = pois.services;
                    $$.each(services, function(i) 
                    {
                        db.transaction(function(tx) 
                        {
                            tx.executeSql("INSERT OR REPLACE INTO services (id, name, phone1, phone2, lat,lng, electric,weld, stock,tuning, renewal,germans, japanese,chinese, description,owner, id_city, map_icon, date_upd) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);",
                                [ services[i].id_service, services[i].name, services[i].phone1, services[i].phone2, services[i].lat, services[i].lng, services[i].electric, services[i].weld,services[i].stock, services[i].tuning, services[i].renewal, services[i].germans, services[i].japanese, services[i].chinese, services[i].description, services[i].owner, services[i].id_city, services[i].map_icon, services[i].ts ])
                        });
                        console.log('services -> db');        
                    });
                    
                    
                    // tireservices                    
                    var tireservices = pois.tireservices;
                    $$.each(tireservices, function(i) 
                    {
                        db.transaction(function(tx) 
                        {
                            tx.executeSql("INSERT OR REPLACE INTO tireservices (id, lat, lng, phone1, phone2, podkat, balancer, rims, tire_repair, description, owner, id_city, map_icon, date_upd) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?);",
                                [ tireservices[i].id_tireservice, tireservices[i].lat, tireservices[i].lng, tireservices[i].phone1, tireservices[i].phone2, tireservices[i].podkat, tireservices[i].balancer, tireservices[i].rims, tireservices[i].tire_repair, tireservices[i].description, tireservices[i].owner, tireservices[i].id_city, tireservices[i].map_icon, tireservices[i].ts])
                        });
                        console.log('tireservices -> db');        
                    });
                    

                } catch(e) {
                    if(console.error) console.error(e);
                    return e;
                }

                // обновим время обновления pois
                myApp.ls.pois_updated = Date.now() / 1000 | 0;                
        },
        error: function(response)
        {
            console.warn('loadpois err', response);            
        },
        complete : function(response) {
            //console.warn('loadpois complete', response);
        }
    });
}


// добавляет новый маркер который можно перетащить
// грузит форму редактирования точки POI
function add_poi(type, edit)
{
    myApp.closeModal();
//console.log('edit', typeof(edit),edit)
    var baseMarker = new google.maps.Marker(
    {
        position: map.getCenter(),
        animation: google.maps.Animation.BOUNCE,
        map: map,
        icon: 'img/'+type+'.png',
        draggable: true
    });

    // запросим у Гугла город-область по координатам
    //reverseGeocode(baseMarker);

    // добавим маркер и попросим юзера его подвинуть
    google.maps.event.addListener(baseMarker, 'dragend', function (coord) 
    {
        var lat = baseMarker.getPosition().lat();
        var lng = baseMarker.getPosition().lng();
        
        myApp.popup('.add_'+type);
        form = $$('form[name='+type+']');
        
        if (edit && edit == 1)
        {
            myApp.formFromJSON(form, myApp.formGetData('editpoi'));
        }
        else
        {
            document.getElementById('form_'+type).reset();
            $$('input[name=id]').val(0);
        }

        $$('input[name=latlng]').val('[Изменить] '+lat.toFixed(6)+', '+lng.toFixed(6));
        $$('input[name=lat]').val(lat);
        $$('input[name=lng]').val(lng);        

        //$$('input[name=city]').val(city);
        //$$('input[name=region]').val(region);        

        baseMarker.setMap(null);   
        baseMarker = [];     
        myApp.formDeleteData('editpoi');
    });
}


// редактирует существующую точку POI
// грузит форму, заполняет ее данными о точке из базы
function updatepoi(id, type)
{
    db.readTransaction(function (tx) 
    {
        tx.executeSql('SELECT * FROM '+type+'s WHERE id ='+id,[], function(tx, result) 
            { 
                formdata = JSON.stringify(result.rows.item(0));
                myApp.ls.setItem('f7form-editpoi', formdata)

                markers[type+id].setMap(null);
                add_poi(type, 1);
            });
    });
}






function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
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




