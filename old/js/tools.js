$(document).foundation();
linkExternal();
getStatus();

id_trip = parseInt(localStorage.getItem('id_trip'));
sosAirOnly = parseInt(localStorage.getItem('sosAirOnly'));
relativeTime = parseInt(localStorage.getItem('relativeTime'));
moment.locale(getCookie('language'));

localStorage.setItem('registered', '1');



function openDb()
{
    if (window.openDatabase) 
    {
        var dbsize = 5 * 1024 * 1024; // 5mb initial database
        db = openDatabase("motokofr_motohelplist", 1, "", dbsize);
        /*, 
            function(database) 
            {
//console.log(db);
            });
*/
        db.transaction(function (tx) 
        {
            tx.executeSql('CREATE TABLE IF NOT EXISTS trips (id_trip, id_user, lat, lng, id_city_start, id_city_finish, map_icon, ts)');
            tx.executeSql('CREATE TABLE IF NOT EXISTS helplist (id_user, name, city_name, phone, help_repair,help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, gender, date_last_login, lat, lng)');
        });
     }
}

/*************************/
/* 
/*  периодические функции
/*
/*************************/

$(window).load(function()
{
    if (sosAirOnly == 1)
        $('a#air_link').prop('href', 'onair.php?sosAirOnly=1&');
        
    if (parseInt(localStorage.getItem('animation')) == 0)
    {
        $('aside.left-off-canvas-menu').css('transition', 'none');
        $('.exit-off-canvas').css('transition', 'none');
        $('.left-submenu').css('transition', 'none');
    }
    
linkExternal(); // экспериментально перенесено
    updateHelplist();
    getUnread();
    doMoment();

    // раз в 55 секунд обновляет время везде где находит элемент с аттрибутом "time"
    if (relativeTime)
    {
        setInterval(function()
        {
            doMoment();
        }, 55000);
    }

    // инициализирует карту
    if (id_trip > 0 && window.location.pathname != '/map.php' && window.location.pathname != '/trip.php') 
    {
console.log('initialise from tools.js');
        prepareGeolocation(); // непонятно что делает, живет в geometa.js
        doGeolocation(); // вынесено в tools.js
        initialise(); // google maps
    }
});


// ловит элементы с атрибутом time
// преобразует время в относительное
function doMoment()
{
    $('[time]').each(function()
    { 
        if (relativeTime) $(this).text(moment.unix($(this).attr('time')).fromNow());
        else  $(this).text(moment.unix($(this).attr('time')).calendar());
    });
}



// обновлеяет координаты на сервере для режима trip 
function updateTrip()
{
    //  при включенном trip И при получении геопозиции обновляет координаты на сервере
    if (id_trip || id_trip > 0)
    {
        if (window.openDatabase) 
        {
openDb();
//console.log(db);            
            db.readTransaction(function (tx) 
            {
                tx.executeSql('SELECT rowid, id_user, lat, lng, ts FROM trips ORDER BY ts DESC LIMIT 1',[], function(tx, result) 
                    { 
                        if (result.rows.length == 0) 
                        { 
                            return false;
                        }
                        $.post(
                            "trip.php",
                            {
                                task:       'updateTrip',
                                id_user:    result.rows.item(0).id_user,
                                lat:        result.rows.item(0).lat,
                                lng:        result.rows.item(0).lng,
                                ts:        result.rows.item(0).ts
                            },
                            onAjaxSuccess
                        );
                        function onAjaxSuccess(data)
                        {
                            // должно вернуться 1
//console.log('trip.php updateTrip: '+data);
                            //if (data == 0) ohSnap('Не удалось обновить координаты на сервере');
                        }
                    })
            });            
        }
    }
}




// автоматическое обновление хелплиста
// только измененные юзеры с момента lastupdate
function updateHelplist() 
{
    openDb();

    ids = Array();
    function find(data, value) 
    {
        if (data.indexOf) { // если метод существует
            return data.indexOf(value);
        }
        for (var i = 0; i < data.length; i++) 
        {
            if (data[i] === value) return i;
        }
        return -1;
    }

    if (!window.openDatabase) {
        return false;
    }
    
    lastupdate = localStorage.getItem('helplist_updated');
// на всякий случай проверим есть ли кто в базе
db.transaction(function (tx) 
{
    var query = 'SELECT count(id_user) as count from helplist';
    tx.executeSql(query,[], function(tx, result) 
    {
        if (result.rows.item(0).count < 1)
            localStorage.removeItem('helplist_updated');
    }, function(tx, error) 
    {
        console.log(error);
    });
});



    
    if (lastupdate)
        request = '?lastupdate='+lastupdate;
    else request = '';      

    var jqxhr = $.get("helplist.php"+request)
    .success(function(data) 
    {
        data = $.parseJSON(data);
//        openDb();
        db.transaction(function (tx) 
        {
            //************* на самом деле date_last_login в базе это не date_last_login, а date_add - дата регистрации юзера */
            //************* потому что last login в хелплисте неактуален, а знать насколько "старый" юзер полезно */
            var query = 'SELECT id_user from helplist';
            tx.executeSql(query,[], function(tx, result) 
            {
                for(var i = 0; i < result.rows.length; i++) 
                {
                  ids.push(result.rows.item(i).id_user);
                }
            });
        });

        
        $.each(data, function(i) 
        {
            db.transaction(function (tx) 
            {
                if (find(ids,parseInt(data[i].id_user))!=-1) 
                   var query = 'UPDATE helplist SET name=\''+data[i].name+'\',city_name=\''+data[i].city_name+'\',phone=\''+data[i].phone+'\',help_repair='+data[i].help_repair+',help_garage='+data[i].help_garage+',help_food='+data[i].help_food+',help_bed='+data[i].help_bed+',help_beer='+data[i].help_beer+',help_strong='+data[i].help_strong+',help_party='+data[i].help_party+',help_excursion='+data[i].help_excursion+',description=\''+data[i].description+'\',gender='+data[i].gender+',date_last_login=\''+data[i].date_add+'\',lat='+data[i].lat+',lng='+data[i].lng+' WHERE id_user='+data[i].id_user;
                   else
                       var query = 'INSERT INTO helplist (id_user, name, city_name, phone, help_repair, help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, gender, date_last_login, lat, lng) VALUES ('+data[i].id_user+',\''+data[i].name+'\',\''+data[i].city_name+'\',\''+data[i].phone+'\','+data[i].help_repair+','+data[i].help_garage+','+data[i].help_food+','+data[i].help_bed+','+data[i].help_beer+','+data[i].help_strong+','+data[i].help_party+','+data[i].help_excursion+',\''+data[i].description+'\','+data[i].gender+',\''+data[i].date_add+'\','+data[i].lat+','+data[i].lng+')';
                tx.executeSql(query);
//console.log('helplist inserted');
            });
        });
        localStorage.setItem('helplist_updated', new Date().toJSON());
        if(relativeTime) $('#updateHelplistDate').text('Обновлен '+moment(localStorage.getItem('helplist_updated'), "YYYY-MM-DD H:mm:ss.Z").fromNow());
        else $('#updateHelplistDate').text('Обновлен '+moment(localStorage.getItem('helplist_updated'), "YYYY-MM-DD H:mm:ss.Z").calendar());
    });
}


// ручное обновление хелплиста ПОЛНОСТЬЮ
function updateHelplistManually()
{
    if (!window.openDatabase)
    {
        $('#updateHelplistDate').html('Технология не поддерживается');
        return false;
    }

    if (navigator.onLine)
    {
        $('#updateHelplistDate').html('<img src="../img/ui-anim_basic_16x16.gif">');

        openDb();
        db.transaction(function (tx) {
        tx.executeSql('delete FROM helplist',[], function(tx, result) { 
            }, null);});
    
        localStorage.removeItem('helplist_updated');
        updateHelplist();
    }
    else ohSnap('Нет интернета :( Попробуй позже.');
}



// получает непрочитанные мессаги
var unreadInterval = 1000;
blumm = new Audio();
blumm.src = 'new_unread_message.mp3';

function getUnread()
{
    var jqxhr = $.get("onair-ajax.php?action=getUnread") 
    .success(function(data) 
    {
        data = $.parseJSON(data);

        // эфир
        if (data.result.air.count > 0)
        {
            $('div#air-all').text(data.result.air.count);
            $('div#air-all').removeClass('hide');

$('#air-').text(data.result.air.count); // щито за хня
            
            unread_msg_air = localStorage.getItem('unread_msg_air');
            if (unread_msg_air < data.result.air.count) 
            {
                blumm.play(); // iPhone
            }
        }
        else $('div#air-all').addClass('hide');

        localStorage.setItem('unread_msg_air', data.result.air.count);

        $.each(data.result.air, function(i) 
        {
            if (!isNaN(i))
            $.each(data.result.air[i], function(key,val) 
            {
                $('#air-'+key).text(val);
                $('#air-'+key).removeClass('hide');            
            });
        });
        
        
        // личка
        if (data.result.private.count == 0) 
        {
            $('div#private-all').addClass('hide');            
            localStorage.setItem('unread_msg', data.result.private.count);            
            return true;
        }
        else
        {
            $('div#private-all').text(data.result.private.count);
            $('div#private-all').removeClass('hide');
        }


        $.each(data.result.private, function(i) 
        {
    if (!isNaN(i))

            $.each(data.result.private[i], function(key,val) 
            {
                $('div#private-'+key).text(val);
                $('div#private-'+key).removeClass('hide');            
                unread_msg = localStorage.getItem('unread_msg');
                if (unread_msg < val) 
                {
                    if (navigator.vibrate) navigator.vibrate([200, 200, 200]); // Android 
                    blumm.play(); // iPhone
                }
                localStorage.setItem('unread_msg', data.result.private.count);
            });
        });
    });
    unreadInterval = unreadInterval + 500;
    setTimeout('getUnread()', unreadInterval);
    
}



/* end of периодические функции
/*************************/



// перехватывает нажатие на ссылки с [role = external] при событии DOMNodeInserted
// выполняет их href чтобы не выпадать из приложения в браузер
// добавляет +1 к таблице click_stat 
/*var timer;
document.body.addEventListener("DOMNodeInserted",function(e)
{
    if (timer) clearTimeout(timer);
    timer =  setTimeout(function()
    {
console.log('DOMNodeInserted');
        linkExternal();
    }, 1000);
},false); */

function linkExternal()
{
    $('a[role = external]').click(function()
    {
        // перенаправляет юзера на запрошенный урл...
        window.location.href = '//app.motohelplist.com/old/'+$(this).attr('href')+'?token='+Date.now();
// token экспериментально отключен
//window.location.href = '//app.motohelplist.com/'+$(this).attr('href');

        $('div#loader').show();    

/* при событии DOMNodeInserted каждый +1 будет вызываться ДВАЖДЫ а без него не будут работать подгруженные ссылки
/* надо переделать а хуй знает как
*/
        // разрезает урл по символу & чтобы не писать все параметры
        var url = $(this).attr('href').split('&')[0];
        // ...и тем временем пишет этому урлу "+1" в базу         
        $.post(
        "index.php",
        {
            plusone: url
        },false);
    return false;
    }); 
}



// подгрузка списка каналов в Эфире
// вызывается из onair.tpl
function loadMoreChannels()
{
var qty = $('div.message_block').length;        
    $.post(
        "onair.php",
        {
            limit: qty
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        qty = qty+qty;
        $('div#content').append(data);
		$('div#loadmore_bottom').hide();
        doMoment();
        linkExternal();
    }
}



// подгрузка списка фидов в Ленте
// вызывается из feed.tpl
function loadMorefeeds()
{
var qty = $('div.message_block').length;        
    $.post(
        "feed.php",
        {
            limit: qty
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        qty = qty+qty;
        $('div#content').append(data);
		$('div#loadmore_bottom').hide();
        doMoment();
    }
}


// подписка на канал
// вызывается из onair.tpl
function subscribe(id_channel)
{
    $('img#subscribe_loader_'+id_channel).show();
    $.post(
        "subscribe.php",
        {
            subscribe: id_channel
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        if (parseInt(data) == parseInt(id_channel))
        {
            $('#subscribe_'+id_channel).hide();
            $('#unsubscribe_'+id_channel).show();
            ohSnap('Подписка оформлена');
        }
            
        else
        {   
            ohSnap('Что-то пошло не так... давай попробуем позже?');

        }
        $('img#subscribe_loader_'+id_channel).hide();
    }
}


// отписка от канала
// вызывается из onair.tpl    
function unsubscribe(id_channel)
{
    $('img#subscribe_loader_'+id_channel).show();        
    $.post(
        "subscribe.php",
        {
            unsubscribe: id_channel
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        if (parseInt(data) == parseInt(id_channel))
        {
            $('#unsubscribe_'+id_channel).hide();
            $('#subscribe_'+id_channel).show();
            $('#'+id_channel).detach();
            updatePage();
            ohSnap('Подписка отменена');
        }
            
        else
        {   
            ohSnap('Что-то пошло не так... давай попробуем позже?');
        }
        $('img#subscribe_loader_'+id_channel).hide();            
    }
} 



// считает дивы с каналами в subscribe
// если их нет - выводит заглушку    
function updatePage()
{
    if ( $('div.subscribe_block').length == 0 )
    {
        $('div#empty').removeClass('hide');
    }
}
    


// unfriend
function unfriend(id_user)
{
    $.post(
        "friends.php",
        {
            unfriend: id_user
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        if (data == id_user)
        {
            $('#unfriend_'+id_user).attr('type', 'hidden');
            $('#tofriend_'+id_user).attr('type', 'button');
            ohSnap('Минус один...');                    
        }
            
        else
        {   
            ohSnap('Что-то пошло не так: '+data);
            console.log(data);
        }
    }
}


// tofriend
function tofriend(id_user)
{
    $.post(
        "friends.php",
        {
            tofriend: id_user
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        if (data == id_user)
        {
            $('#tofriend_'+id_user).attr('type', 'hidden');
            $('#unfriend_'+id_user).attr('type', 'button');
            ohSnap('За дружбу!');                    
        }
        else
        {
            ohSnap(data);
        }
    }
}       
        
        

/**/
/* контроль Application Chache */
/**/
            
var appCache = window.applicationCache;
/*        function handleCacheEvent(e) {
console.log('handleCacheEvent = '+e);
    }
*/  
function handleCacheError(e) {
    console.log('Ошибка загрузки AppCahce: '+e.message);
};

// The manifest returns 404 or 410, the download failed,
// or the manifest changed while the download was in progress.
appCache.addEventListener('error', handleCacheError, false);



// устанавливает статус 
function setStatus()
{
    var currentstatus = $('textarea#currentstatus').val();
    $.post(
        "profile.php",
        {
            setStatus: currentstatus
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        if(data == currentstatus)
        {
            ohSnap('Статус обновлен');
            localStorage.setItem('currentstatus',currentstatus);
            getStatus();            
            $('li[class=back] a').click();
        }
        else if (data == 'nonick')
        {
            ohSnap('Человек Без Ника, мы не смогли тебя опознать :(');
        }
        else
        {
            ohSnap('Что-то пошло не так. Давай попробуем позже');                
        }
    }
    
}

// получает статус
function getStatus()
{
    // вставляет статус в главное меню
    var status = localStorage.getItem('currentstatus');
    if (status != 'undefined')
        $('textarea#currentstatus').val(status);
    else
        $('textarea#currentstatus').val('');        
}



// получает свою геопозицию
function doGeolocation()
{
    if (navigator.geolocation) 
    {
        // если мы на странице без карты, то дива #map нет
        // нужно создать его фейк с размерами 1х1 иначе нихуя не будет работать
        if (document.getElementById('map') == null)
        {
            $('body').append('<div style="width: 1px; height: 1px;" class="hide" id="map"></div>');
        }

        if (!id_trip || id_trip == 0)
        {
            navigator.geolocation.getCurrentPosition(positionSuccess, positionError);
        }
        else
        {
            navigator.geolocation.watchPosition(positionSuccess, positionError);            
            // watchPosition постоянно обновляет карту и она дергается            
            //navigator.geolocation.getCurrentPosition(positionSuccess, positionError);            
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
      case err.UNKNOWN_ERROR:
        msg = "Неизвестная ошибка определения геолокации";
        break;
      case err.PERMISSION_DENINED:
        msg = "Включи определение координат в настройках";
        break;
      case err.POSITION_UNAVAILABLE:
        msg = "Невозможно определить местоположение";
        break;
      case err.BREAK:
        msg = "Не могу найти спутники геолокации";
        break;
      default:
        msg = "Включи определение координат в настройках";
    }

    ohSnapX();
    ohSnap('Ошибка: '+err.message);
    ohSnap(msg);
    
    setTimeout(function() { map.setZoom(11) }, 1000)
}


// геопозиция получена удачно
function positionSuccess(position) 
{
    var coords = position.coords || position.coordinate || position;
    var latlng = new google.maps.LatLng(coords.latitude, coords.longitude);

    // если вклочен "в путь", центрируем карту только один раз чтобы не прыгала
    if (!id_trip || setCenter == 0)
    {
        map.setCenter(latlng);
        setCenter = 1;
    }
    
    localStorage.setItem('lastLat',coords.latitude);
    localStorage.setItem('lastLng',coords.longitude);    
//        map.setZoom(17);

    placeMarkerImHere(latlng);
    
    if (id_trip || id_trip > 0)
    {        
//console.log('пишем id_trip = '+id_trip+' '+coords.latitude+' '+coords.longitude);
        openDb();
        db.transaction(function (tx) 
        {
            tx.executeSql('INSERT INTO trips (id_trip, id_user, lat, lng, ts) VALUES ('+id_trip+','+id_user+','+coords.latitude+','+coords.longitude+','+Date.now()+')');
        });
// экспериментально отключено
updateTrip();
    }
}


// ставит маркер "я здесь"        
function placeMarkerImHere(latLng,customicon)
{    
    if (window.location.pathname == '/trip.php' || customicon == 1) 
    {
/*        var icon = {
        path: google.maps.SymbolPath.CIRCLE,
        fillOpacity: 0.8,
        fillColor: '#A00',
        strokeOpacity: 1.0,
        strokeColor: '#fff',
        strokeWeight: 0, 
        scale: 10 //pixels
        }*/
        var icon = 'img/icons/trip.png';
        
    }

    var marker = new google.maps.Marker(
    {
        map: map,
        position: latLng,
        title: 'Why, there you are!',
        zoom: 17,
        icon: icon
    });
    (new google.maps.Geocoder()).geocode({latLng: latLng}, function(resp) 
    {
//console.log(latLng.lat());
//console.log(latLng.lng());        
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
        }
        
        // добавим слушатель событие click для маркера 
        var infoWindow = new google.maps.InfoWindow(); 
        google.maps.event.addListener(marker, 'click', function() 
        { 
            infoWindow.setContent(place+'<br>'+resp[0].formatted_address+'<hr><div class="small right">Скопировать координаты: <input readonly value="'+latLng.lat().toFixed(6)+','+latLng.lng().toFixed(6)+'"></div>'); 
            infoWindow.open(map, marker); 
        }); 
    });    
}
    
    
// поиск друзей    
function searchToFriends()
{
    var string = $('input#searchstring').val();
    if(string.length < 2) {
        ohSnap('2 и более букв/цифр плиз');
        return false;
    }

    $('div#loader').show();            
        $.post(
            "friends.php",
            {
                searchToFriends: string
            },
            onAjaxSuccess
        );
        function onAjaxSuccess(data)
        {
            $('div#loader').hide();
            if (data == 'notfound') 
            {
                ohSnap('Никого не найдено');
            }
            else
            {
                $('div#friends').prepend(data);
                $('div#searchtofriends').hide(animation);
            }
        }
}
    
    
    
// рисует кружок с буквой если нет аватара
// теперь это делает php
/*function makeAvatars()
{
    var stringToColor = function stringToColor(str) 
    {
        var hash = 0;
        var color = '#';
        var i;
        var value;
        var strLength;
    
        if(!str) {
            return color + '333333';
        }
        strLength = str.length;
    
        for (i = 0; i < strLength; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        for (i = 0; i < 3; i++) {
            value = (hash >> (i * 8)) & 0xFF;
            (value > 240 ? value=240 : value=value);                
            color += ('00' + value.toString(16)).substr(-2);
        }
        return color;
    };

    $('.letter-avatar').each(function(indx, element)
    {
        var name = element.id;
        var letter = name.substr(0, 1);
        var backgroundColor = stringToColor(name);
        element.innerHTML = letter;
        element.style.backgroundColor = backgroundColor;
    });
}   */


        
        
/*
currentCountry = getCookie('currentCountry');
language = getCookie('language');
ohSnap('currentCountry = '+currentCountry +', language = '+language);
*/



// функции работы с cookies
// getCookie(name)
// setCookie(name, value, options)
//// options например  {expires=Tue, 19 Jan 2038 03:14:07 GMT}
// deleteCookie(name)


