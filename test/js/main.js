if (localStorage.getItem('animation') == 0)
{
    animatePages = false;
    animateNavBackIcon = false;
    pushStateNoAnimation = false;
    swipeBackPageAnimateShadow = false;
    swipeBackPageAnimateOpacity	= false;
    animateChats = false;
}
else
{
    animatePages = true;
    animateNavBackIcon = true;
    pushStateNoAnimation = true;
    swipeBackPageAnimateShadow = true;
    swipeBackPageAnimateOpacity	= true;
    animateChats = true;
}

var myApp = new Framework7(
{
//        cache: false,
material: false,
    swipePanel: 'left',
    precompileTemplates: true,
    animatePages: animatePages,
    animateNavBackIcon: animateNavBackIcon,
    pushStateNoAnimation: pushStateNoAnimation,
    swipeBackPageAnimateShadow: swipeBackPageAnimateShadow,
    swipeBackPageAnimateOpacity: swipeBackPageAnimateOpacity
});

// Export selectors engine
var $$ = Dom7;
var API_URL = '//app.motohelplist.com/api/v2/';
if (screen.width >= 1280)
{
    $$('body').addClass('desktop');
}


/***************** основные установки  ***********************/

if (!myApp.formGetData('settings') || !myApp.formGetData('settings').relativeTime || !myApp.formGetData('settings').sosAirOnly || !myApp.formGetData('settings').animation || !myApp.formGetData('settings').geocodeAddr || !myApp.formGetData('settings').clusterPoi || !myApp.formGetData('settings').showTraffic || !myApp.formGetData('settings').initZoom || !myApp.formGetData('settings').pois || !myApp.formGetData('settings').showTripFriendsOnly)
{
    myApp.ls.setItem('f7form-settings', JSON.stringify({"relativeTime":"1","sosAirOnly":"0","animation": "1","geocodeAddr":"0","clusterPoi":"11","showTraffic":"0","initZoom":"14","pois":["hotels","services","tireservices","places","parkings"], "showTripFriendsOnly": "0"}));
}

if (!myApp.formGetData('profile'))
{
    myApp.ls.setItem('f7form-profile', JSON.stringify({
        "id_user":"0",
        "active":"1",
        "gender":"0",
        "id_city":"1",
        "id_motorcycle":"1",
        "phone":""
        }))
}

localStorage.setItem('firstpage', 'onair'); // первая страница которая будет загружена 
var firstpage = localStorage.getItem('firstpage');
var relativeTime = parseInt(myApp.formGetData('settings').relativeTime);
moment.locale(navigator.language);
myIdUser = ''; // оставить здесь в таком виде

// infinite-scroll
var loading = false;
var maxItems = 600;     // макс колво мессаг в чате
var itemsPerLoad = 20;  // колво подгружаемых мессаг с сервера
var lastIndex;
var photos = [];
var myMessages = '';
var loadWorker ='';

var blumm = new Audio();
blumm.src = 'new_unread_message.mp3';
var tick = new Audio();
tick.src = 'new_chat_message.mp3';

var lat = 53.1424223;
var lng = 29.2242762;            
localStorage.setItem('initLat', lat);
localStorage.setItem('initLng', lng);
if (!localStorage.getItem('lastLat'))           localStorage.setItem('lastLat', lat);
if (!localStorage.getItem('lastLng'))           localStorage.setItem('lastLng', lng);
if (!localStorage.getItem('messages_updated'))  localStorage.setItem('messages_updated','0');
if (!localStorage.getItem('helplist_updated'))  localStorage.setItem('helplist_updated','0');
if (!localStorage.getItem('digest_updated'))    localStorage.setItem('digest_updated','0');
if (!localStorage.getItem('pois_updated'))      localStorage.setItem('pois_updated','0');
if (!localStorage.getItem('inTrip') )           localStorage.setItem('inTrip', '0');

var map = {};
var hotels = {};
var parkings = {};
var places = {};
var services = {};
var tireservices = {};
var trips = {};
var get = 0;
var customicon = 0;
var geocoder;
var setCenter = 0;

myApp.ls.loadWorkerInterval = 1000;


// Add view
var mainView = myApp.addView('.view-main', 
{
//    pushState : true,
//    pushStateRoot : 'app.motohelplist.com/f7/dist/',
    dynamicNavbar: true,
    //animatePages:false,
    onAjaxStart: function (xhr) {

    },

    onAjaxComplete: function (xhr) {
    }
});

// создание таблиц
//https://gist.github.com/nanodeath/324073
function DBMigrator(db)
{
    var migrations = [];
    this.migration = function(number, func){
        migrations[number] = func;
    };
    var doMigration = function(number)
    {
        if(migrations[number]) {
            //console.log('DB migrated to ver.',number);
            db.changeVersion(db.version, String(number), function(t){
            migrations[number](t);
            }, function(err){
            if(console.error) console.error("Error!: %o", err);
            }, function(){
            doMigration(number+1);
            });
        }
    };
    this.doIt = function() {
        var initialVersion = parseInt(db.version) || 0;
        try {
        doMigration(initialVersion+1);
        } catch(e) {
            if(console.error) console.error(e);
        }
    }
}


if (!!window.openDatabase) 
{
    var dbsize = 24 * 1024 * 1024; // 5mb initial database
    db = openDatabase("moto_helplist", "", "", dbsize);
    var M = new DBMigrator(db);
    
    M.migration(1, function(t) 
    {
        t.executeSql('CREATE TABLE IF NOT EXISTS helplist (id_user INTEGER PRIMARY KEY ASC, name TEXT, city_name TEXT, phone TEXT, help_repair INTEGER ,help_garage INTEGER , help_food INTEGER, help_bed INTEGER, help_beer INTEGER, help_strong INTEGER, help_party INTEGER, help_excursion INTEGER, description TEXT, gender INTEGER, date_last_login TEXT, lat TEXT, lng TEXT)');

        t.executeSql('CREATE TABLE IF NOT EXISTS messages (id INTEGER PRIMARY KEY ASC, id_channel INTEGER, id_from INTEGER, id_to INTEGER, text TEXT, ts TEXT , name TEXT, gender INTEGER, unread INTEGER )');
        
        t.executeSql('CREATE TABLE IF NOT EXISTS digest (id_channel INTEGER PRIMARY KEY ASC , channel_name TEXT, type_channel INTEGER, id_user INTEGER, name TEXT, phone TEXT, gender INTEGER, text TEXT, id_urgency INTEGER, urgency TEXT, is_subscribe INTEGER, ts TEXT)');

        t.executeSql('CREATE TABLE IF NOT EXISTS digest_personal (id_channel INTEGER , channel_name TEXT, type_channel INTEGER, id_user INTEGER PRIMARY KEY ASC, name TEXT, phone TEXT, gender INTEGER, text TEXT, id_urgency INTEGER, urgency TEXT, is_subscribe INTEGER, ts TEXT)');
        
        t.executeSql('CREATE TABLE IF NOT EXISTS feed (id_feed INTEGER PRIMARY KEY ASC, type_feed INTEGER, id_object INTEGER, name_object TEXT, name TEXT, id_user INTEGER, phone TEXT, gender INTEGER, city TEXT, text TEXT, ts TEXT)');            
        
        t.executeSql('CREATE TABLE IF NOT EXISTS subscribe (id_channel INTEGER PRIMARY KEY ASC, type_channel INTEGER, channel_name TEXT, ts TEXT)');                        
        
        t.executeSql('CREATE TABLE IF NOT EXISTS friends (id_user INTEGER PRIMARY KEY ASC, name TEXT, id_city INTEGER, city TEXT, id_motorcycle INTEGER, motorcycle TEXT, motorcycle_more TEXT, phone TEXT, gender INTEGER, status TEXT,  help_repair INTEGER, help_garage INTEGER, help_food INTEGER, help_bed INTEGER, help_beer INTEGER, help_strong INTEGER, help_party INTEGER, help_excursion INTEGER, description TEXT, ts TEXT)');

        t.executeSql('CREATE TABLE IF NOT EXISTS trips (id_trip INTEGER PRIMARY KEY ASC, id_user INTEGER, lat, lng, accuracy INTEGER, id_city_start INTEGER, id_city_finish INTEGER, map_icon TEXT, ts INTEGER)');

        t.executeSql('CREATE TABLE IF NOT EXISTS hotels (id INTEGER PRIMARY KEY, name, lat, lng, phone1, phone2, price, parking, ac,wifi,sauna,description,owner,photo,map_icon,date_upd)');
        t.executeSql('CREATE TABLE IF NOT EXISTS parkings (id INTEGER PRIMARY KEY, phone1,phone2,lat,lng,price,access,camera,security,big,description,owner,id_city,map_icon,date_upd)');        
        t.executeSql('CREATE TABLE IF NOT EXISTS places (id INTEGER PRIMARY KEY,lat,lng,name,id_city,description,owner,photo,map_icon,date_upd)');
        t.executeSql('CREATE TABLE IF NOT EXISTS services (id INTEGER PRIMARY KEY, name, phone1, phone2, lat,lng, electric,weld, stock,tuning, renewal,germans, japanese,chinese, description,owner, id_city, map_icon, date_upd)');
        t.executeSql('CREATE TABLE IF NOT EXISTS tireservices (id INTEGER PRIMARY KEY, lat, lng, phone1, phone2, podkat, balancer, rims, tire_repair, description, owner, id_city, map_icon, date_upd)');
    });

            
    M.doIt();
}
else
{
    myApp.alert('Текущая конфигурация не поддерживает нужные технологии. Используйте Chrome для Android и Safari для iOS.');
}

// end of основные установки



/* db.transaction(function(tx) {
tx.executeSql("INSERT OR REPLACE INTO subscribe (id_channel , type_channel, channel_name, ts ) values (?,?,?,?), (?,?,?,?)",    [4, 4,'123','123',  5,4,'4646','23534'],
function(tx,result)
{
   console.log('rowsAffected ',result.rowsAffected);
    },
    function (tx, error) {
    console.log('Oops. Error was',error)
}) }); */


photoBrowser = myApp.photoBrowser(
{
    photos: photos,
    zoom: true,
    ofText: '/',
    lazyLoading: true,
});



if (myApp.ls.getItem('token'))
{
    myIdUser = parseInt(myApp.formGetData('profile').id_user);
    startLoadWorker();
    firstPage(firstpage);
    myApp.formDeleteData('editpoi');
    
    setTimeout(function() {
        loadpois();
        }, 3000);

    
    // счетчик входов
    transport = new XMLHttpRequest();
    transport.open('POST', API_URL+'users/count');  
    transport.setRequestHeader('x-access-token', myApp.ls.getItem('token'));
    transport.send();
    /*
    if(transport.status)
    console.log(transport.status);
    */



}
else
{
    myApp.loginScreen();

    if (!myApp.device.os)
    {
        myApp.alert('Moto Helplist разработан<br>для мобильных устройств.<br>Он будет работать<br>на компьютере, но вы не возьмете компьютер в дорогу.<br>Зайдите сюда со смартфона<br>под Android, iOS или Ubuntu.');
    }
}



// loader и проверка на онлайн
$$(document).on('ajaxStart', function (e) 
{
    var token = myApp.ls.getItem('token');
    var xhr = e.detail.xhr; 
    xhr.setRequestHeader('x-access-token',token); 
});




//! авторизация
$$('.form-to-json.login').on('click', function() 
{
    var lp = myApp.formToJSON('#login_password');
    if (lp)
    {
        if (lp.phone == '' || lp.password == '')
        {
            myApp.alert('Кажется, здесь чего-то не хватает...');
            return false;
        }
    }
    myApp.showProgressbar(); 
    $$('#login').addClass('disabled');    
    
        var url = 'auth/'+lp.phone+'/'+sha1(lp.password);
        $$.ajax(
        {
            method: 'POST', 
            url: API_URL+url,
            //data: JSON.stringify({"phone": lp.phone, "password": sha1(lp.password)}),
            success: function(response)
            {
                console.log('login success');
                var response = JSON.parse(response);
                          
                // не убирать
                myApp.hideProgressbar(); $$('#login').removeClass('disabled');
    
                //response может быть html
                myApp.ls['token'] = response.token;
    
                // тянем профиль с сервера
                $$.ajax(
                {
                    method: 'GET', 
                    url: API_URL+'profile/',
                    success: function(response)
                    {
                        // запишем профиль в LS
                        myApp.formStoreData('profile', JSON.parse(response)[0]);
                        myIdUser = parseInt(myApp.formGetData('profile').id_user);
                    },
                    error: function(response)
                    {
                        console.log('login error',response);
                    }    
                });
    
    
                // загружает все
                startLoadWorker();
                firstPage(firstpage);
                
                setTimeout(function() {
                    loadpois();
                    }, 5000);
                
                myApp.addNotification({
                    title: 'C возвращением!',
                    message: 'Мы рады снова видеть тебя',
                    hold: 2000
                });          
                myApp.closeModal();

                // очищение history чтобы не работала кнопка назад
                $$('.page-on-left').remove();

        },
        error: function(response)
        {
            console.log('login error');            
            
            // не убирать
            myApp.hideProgressbar();$$('#login').removeClass('disabled');
            
            if (response.status == 400)
            {
                myApp.alert('Неверный формат номера телефона');    
            }

            if (response.status == 403)
            {
                myApp.modal(
                {
                    title:  'Неправильный пароль',
                    text: 'Вспомни его, или мы пришлем новый (придется ждать СМС)',
                    verticalButtons: true,
                    buttons: [{
                    text: 'Я вспомню свой пароль',
                    onClick: function() 
                    {
                        return;
                    }},
                    {
                        text: 'Пришлите мне новый',
                        onClick: function() {
                        
                        myApp.formGetData('login_password');
                        lostPassword();
                    }
                    }]
                });
            }
                
            if (response.status == 401)
            {
                myApp.modal(
                {
                    title: 'Этот номер нам незнаком',
                    text: 'Ты впервые здесь или ошибся при вводе телефона?',
                    verticalButtons: true,
                    buttons: [{
                    text: 'Изменить номер',
                    onClick: function() 
                    {
                        return;
                    }},
                    {
                        text: 'Зарегистрироваться',
                        onClick: function() {
                        myApp.closeModal();
                        myApp.mainView.loadPage('registration.html');
                        
                    var reg = myApp.formGetData('login_password');
                    delete reg.password;
                    reg.action = 'sendSms';
                    doRegistration(reg);
                
                    }
                    }]
                });
            }




// тест - избавляет от повторных отправок смс при регистрации
// при смене кода ошибки отразить в API
if (response.status == 422)
{
        myApp.mainView.loadPage('registration.html');
        myApp.closeModal();
}            






        },
        complete : function() {
                    $$('#login').removeClass('disabled');
                    myApp.hideProgressbar();
        }    
    });
});





function initAutocompleteCity() {
            // смена города
        var autocompleteStandaloneAjax = myApp.autocomplete({
            openIn: 'page', //open in page
            opener: $$('#autocomplete-city'), //link that opens autocomplete
            backOnSelect: true,
            valueProperty: 'id', //object's "value" property name
            textProperty: 'name', //object's "text" property name
            limit: 50,
            searchbarPlaceholderText: 'латинскими буквами',
            preloader: true, //enable preloader
            source: function (autocomplete, query, render) {
                var results = [];
                if (query.length === 0) {
                    render(results);
                    return;
                }
                autocomplete.showPreloader();
                $$.ajax({
                    url: API_URL+'cities/search/'+query,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        // Find matched items
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].name.toLowerCase().indexOf(query.toLowerCase()) >= 0) results.push(data[i]);
                        }
                        autocomplete.hidePreloader();
                        render(results);
                    }
                });
            },
            onChange: function (autocomplete, value) 
            {
                //console.log(value);
                $$('#autocomplete-city').find('.item-after').val(value[0].name);
                // Add item value to input value
                $$('#autocomplete-city').find('#id_city').val(value[0].id);    
            }
        });

}





myApp.onPageInit('*', function (page) {
$$('body').removeClass('map');
if (parseInt(myApp.ls.inTrip) == 1) 
    doGeolocation();
});




//! bugreport
myApp.onPageInit('bugreport', function (page) 
{
    var digest, digest_personal, feed, friends, helplist, hotels, messages, parkings, places, services, subscribe, tireservices, trips;

    db.readTransaction(function (tx) 
    {
        tx.executeSql("select count(ts) as count from digest",[],function(tx, results)
        { 
            digest = results.rows 
        });
        tx.executeSql("select count(ts) as count from digest_personal",[],function(tx, results)
        { 
            digest_personal = results.rows 
        });        
        tx.executeSql("select count(ts) as count from feed",[],function(tx, results)
        { 
            feed = results.rows 
        });                
        tx.executeSql("select count(ts) as count from friends",[],function(tx, results)
        { 
            friends = results.rows 
        });                        
        tx.executeSql("select count(id_user) as count from helplist",[],function(tx, results)
        { 
            helplist = results.rows 
        });                                
        tx.executeSql("select count(owner) as count from hotels",[],function(tx, results)
        { 
            hotels = results.rows 
        });                                        
        tx.executeSql("select count(ts) as count from messages",[],function(tx, results)
        { 
            messages = results.rows 
        });                                                
        tx.executeSql("select count(owner) as count from parkings",[],function(tx, results)
        { 
            parkings = results.rows 
        });                                                        
        tx.executeSql("select count(owner) as count from places",[],function(tx, results)
        { 
            places = results.rows 
        });                                                                
        tx.executeSql("select count(owner) as count from services",[],function(tx, results)
        { 
            services = results.rows 
        });                                                                        
        tx.executeSql("select count(ts) as count from subscribe",[],function(tx, results)
        { 
            subscribe = results.rows 
        });                                                                                
        tx.executeSql("select count(owner) as count from tireservices",[],function(tx, results)
        { 
            tireservices = results.rows 
        });                                                                                        
        tx.executeSql("select count(ts) as count from trips",[],function(tx, results)
        { 
            trips = results.rows 
        });                                                                                                
        
        setTimeout(function()
            {
                $$('textarea[name=dbinfo]').html('<p>Digest: '+digest.item(0).count+'<br>Digest_personal: '+digest_personal.item(0).count+'<br>Feed: '+feed.item(0).count+'<br>Friends: '+friends.item(0).count+'<br>Help-list: '+helplist.item(0).count+'<br>Messages: '+messages.item(0).count+'<br>Отели: '+hotels.item(0).count+'<br>Парковки: '+parkings.item(0).count+'<br>Места: '+places.item(0).count+'<br>Сервисы: '+services.item(0).count+'<br>Шиномонтажи: '+tireservices.item(0).count+'<br>В пути: '+trips.item(0).count+'</p>')
            }, 2000);


    });

           
    if (useragent = navigator.userAgent)
    {
        $$('textarea[name=debug]').append(useragent);
    }
    $$('textarea[name=debug]').append('\r\n'+myApp.formGetData('profile').name+'\r\n'+myApp.formGetData('profile').phone);
        

    $$('form#bugreport').on('submit', function (e) 
    {
        e.preventDefault();
        $$('.button').addClass('disabled');
        myApp.showProgressbar();
        
        var form_data = new FormData();                  
    	form_data.append("bug_description", $$('textarea[name=bug_description]').val());
    	form_data.append("debug", $$('textarea[name=debug]').val());        	
        form_data.append("dbinfo", $$('textarea[name=dbinfo]').val());
    	form_data.append("screenshot", $$('input[name=screenshot]')[0].files[0]);        	
            	
        $$.ajax(
        {
            url: '../bugreport.php',
    //         url: API_URL+'users/bugreport',
            type: "POST",
            dataType: 'json', 
            data: form_data,
            processData: false,
            contentType: false,
            complete: 	function (xhr, status) {
                if (status = 200)
                {
                    myApp.hideProgressbar();
                    $$('.button').removeClass('disabled');                        
                    myApp.alert('Мы увидели твой баг и починим его как можно скорее.', 'Спасибо!');
                    setTimeout(function() {myApp.mainView.loadPage('onair.html')}, 500);
                }
                else 
                {
                    myApp.hideProgressbar();
                    $$('.button').removeClass('disabled');                        
                    myApp.alert('Не удается отправить сообщение. Плохая связь?');
                    setTimeout(function() {myApp.mainView.loadPage('onair.html')}, 500);                
                }
            }
        });

    
    
    });

});




//! регистрация
myApp.onPageInit('registration', function (page) 
{
    myApp.allowPanelOpen = false; // запрещает открытие панелей меню
  
    $$('.manifest').on('click', function () 
    { 
        //$$('.open-panel').hide();
        myApp.popup('.popup-manifest');    
    });

    initAutocompleteCity();
    
    $$('.form-to-json.registration').on('click', function() 
    {
        var reg = myApp.formToJSON('#registration');
        reg.phone = myApp.formGetData('login_password').phone;
        reg.password = sha1(myApp.formGetData('login_password').password);
        
        if (reg.id_city == '')
            {
                myApp.alert('Выбери город из списка');
                return false;
            }
        else if (reg.code == '')
        {
            myApp.alert('Введи код из СМС');
            return false;            
        }
        else if (reg.agree != '1')
        {
            myApp.alert('Необходимо принять Соглашение');
            return false;                        
        }
        else if (reg.code.length != 4)
        {
            myApp.alert('Неверный код из СМС');
            return false;            
        }
        else
        {
            doRegistration(reg);
            
            delete reg.password; delete reg.code; delete reg.agree;
            reg.active = "1";
            reg.id_motorcycle = "1";
            myApp.formStoreData('profile', reg);
        }
    
    
           
    //  $$('#registraion').addClass('disabled');            
          
    });
});








//! профиль стр
myApp.onPageInit('profile', function (page) 
{
    // показ аватара в профиле
    setTimeout(function(){     
        $$.ajax(
        {
            method: 'GET', 
            url: API_URL+'users/'+myIdUser+'/avatar',
            success: function(response)
            {
                if (avatar = JSON.parse(response).avatar)
                    $$('#avatar').attr('src', avatar);
            },
            error: function(response)
            {
            }    
        })
    }, 500);    

    

    
    // смена аватара    
    function readFile(input) 
    {
    	if (input.files && input.files[0]) 
    	{
            var reader = new FileReader();
            reader.onload = function (e) 
            {
            	$uploadCrop.bind({
            		url: e.target.result
            	});
            	$$('#upload-demo').addClass('ready');
            }
            reader.readAsDataURL(input.files[0]);
        }
        else 
        {
            //myApp.alert("Sorry - you need to update your OS");
        }
    }
        $uploadCrop =  new Croppie(document.getElementById('upload-demo'),{
    	exif: true,
    	viewport: {
    		width: 100,
    		height: 100//,
    //		type: 'circle'
    	},
    	boundary: {
    		width: 300,
    		height: 300
    	}
    });
    
    $$('.item-link.item-content .item-media').on('click',function(){
//        $$('#upload').click(); // автоклик не работает в мобильных

        // запрещает открытие панелей меню свайпом
        $$('.item-input').on('touchstart', function (e){e.stopPropagation()})

        $$('#avatar_upload').show(); 
    });
    
    $$('#upload').on('change', function () { readFile(this); });


    $$('.upload-result-cancel').on('click', function (ev) { $$('#avatar_upload').hide() });
    $$('.upload-result-delete').on('click', function (ev) 
    {
        myApp.confirm('Вместо него будет использоваться цветной кружок с твоими инициалами', 'Удалить аватар?', 
            function () 
            {
                $$.ajax(
                {
                    method: 'DELETE', 
                    url: API_URL+'profile/avatar',
                    success: function(response)
                    {
                        console.log('аватар удален', response);
                        window.open('https://app.motohelplist.com/', '_self');
                        setTimeout(function() {myApp.mainView.loadPage('profile.html')}, 500);
                    },
                    error: function(response)
                    {
                        console.log(response);                        
                    }    
                });                
                $$('#avatar_upload').hide();
            },
                function () { $$('#avatar_upload').hide() } 
            );
         

         });
    
    $$('.upload-result').on('click', function (ev) 
    {
    	$uploadCrop.result('canvas').then(function (resp) 
    	{
            resp = resp.toDataURL("image/jpeg", 0.8);
            $$('#avatar').attr('src', resp);
    
            // Creating object of FormData class
            var form_data = new FormData();                  
        	form_data.append("file", resp)
        				
            var jqxhr = $$.ajax(
            {
                url: API_URL+'profile/avatar',
                type: "POST",
                dataType: 'json', 
                data: form_data,
/*
                processData: false,
                contentType: false,
*/
                success: function (response) {
                    console.log('аватар загружен', response);
                    window.open('https://app.motohelplist.com/', '_self');
                    setTimeout(function() {myApp.mainView.loadPage('profile.html')}, 500);

                    myApp.addNotification({
                        title: 'Готово',
                        hold: 2000
                    });
                }
            });
            $$('#avatar_upload').hide(); 
        });
    });    


        // заполним форму данными из LS
        if (!myApp.formGetData('profile'))
            myApp.mainView.loadPage('profile.html');
        else
            myApp.formFromJSON('#profile', myApp.formGetData('profile'));
        
        
        //! пароль - смена
        $$('input#password').on('change', function() 
        { 
            // подготовим поле name=passwd для отправки на сервер
            $$('input[name=passwd]').val(sha1($$('input#password').val().trim()));

            // перепишем пароль в local storage 
            var login_password = myApp.formGetData('login_password');
            login_password.password = $$('input#password').val().trim();
            myApp.formStoreData('login_password', login_password);
        });
        
        //! профиль на сервер
        $$('.form-to-json.profile-change').on('click', function() 
        {
            $$.ajax(
            {
                method: 'PUT', 
                url: API_URL+'profile/',
                data: JSON.stringify(myApp.formToJSON('#profile')),
                processData : 'false',
                success: function(response)
                {
                    myApp.formStoreData('profile', myApp.formToJSON('#profile'));
                    if (response == 'OK') 
                    {
                        myApp.addNotification({
                            title: 'Готово',
                            hold: 2000
                        });    
                        myApp.mainView.loadPage(firstpage+'.html');
                    }
                },
                error: function(response)
                {
                    console.log('ошибка записи профиля на сервер:',response);
                    myApp.addNotification({
                        title: 'Ошибка записи профиля',
                        text: 'Пожалуйста соообщи нам об этом.',
                        hold: 4000
                    });    
                    
                }    
            });        
        });
        
        
         initAutocompleteCity();

        
        // смена мотоцикла
        var autocompleteStandaloneAjax = myApp.autocomplete({
            openIn: 'page', //open in page
            opener: $$('#autocomplete-motorcycle'), //link that opens autocomplete
            backOnSelect: true,
            valueProperty: 'id', //object's "value" property name
            textProperty: 'name', //object's "text" property name
            limit: 50,
            notFoundText: 'Not found',
            preloader: true, //enable preloader
            source: function (autocomplete, query, render) {
                var results = [];
                if (query.length === 0) {
                    render(results);
                    return;
                }
                autocomplete.showPreloader();
                $$.ajax({
                    url: API_URL+'motorcycles/search/'+query,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].name.toLowerCase().indexOf(query.toLowerCase()) >= 0) results.push(data[i]);
                        }
                        autocomplete.hidePreloader();
                        render(results);
                    }
                });
            },
            onChange: function (autocomplete, value) {
                $$('#autocomplete-motorcycle').find('.item-after').val(value[0].name);
                // Add item value to input value
                $$('#autocomplete-motorcycle').find('#id_motorcycle').val(value[0].id);    
            }
        });
        
    
        // отслеживает active user, открывает popup из index.html
        $$('#active').on('change', function() 
        {
            if ( $$('#active').prop('checked') )
            {
                $$('#may_to_help').show();
                myApp.addNotification({
                    title: 'Спасибо что ты с нами :)',
                    hold: 2000
                });
//                $$('#hl-involve').text('Да');
            }            
            else
            { 
                myApp.popup('.popup-inactive');
                $$('#may_to_help').hide();
//                $$('#hl-involve').text('Нет');
            }
        });
    
    
        setTimeout(function() 
        {
            if ( $$('#active').prop('checked') )  $$('#may_to_help').show();  
            else $$('#may_to_help').hide();
        }, 1000);    
});





    

//! settings
myApp.onPageInit('settings', function (page) 
{
    $$('#updateHelplistDate').attr('time', (new Date(myApp.ls.helplist_updated).getTime()/1000).toFixed());
    if(relativeTime) $$('#updateHelplistDate').text(moment(myApp.ls.helplist_updated, "YYYY-MM-DD H:mm:ss.Z").fromNow());
    else $$('#updateHelplistDate').text(moment(myApp.ls.helplist_updated, "YYYY-MM-DD H:mm:ss.Z").calendar());
    
 
        
});



//! onair
/*******************************************/
myApp.onPageInit('onair', function (page) 
{
     $$('div.modal.modal-no-buttons.modal-preloader.modal-out').hide(); // прелоадер полностью не исчезает в Сафари и мешает юзеру

    if (myApp.ls.getItem('token'))
    {
        //! хелп-лист воркер
        // стоп воркера - helplist_update_worker.terminate()       
        setTimeout(function hluw()
        {
            if (!!window.Worker && !!window.openDatabase && navigator.onLine)
            {
                var helplist_update_worker = new Worker('js/helplist_worker.js');
                helplist_update_worker.addEventListener('message', function(e) 
                {
                    //var users = JSON.parse(e.data).users;
                    //var qty = users.length;
                    //console.log('hl добавить: '+qty+', удалить '+JSON.parse(e.data).to_delete.length);
                    updateHelplist(JSON.parse(e.data).users, JSON.parse(e.data).to_delete);
                }, false);
        
                if (myApp.ls.helplist_updated) var lastupdate = myApp.ls.helplist_updated;
                else var lastupdate = 0;
                helplist_update_worker.postMessage({"token": myApp.ls.getItem('token'), "lastupdate": lastupdate});

                helplist_update_worker.addEventListener('error', onError, false);
                function onError(e) {
                    document.getElementById('error').textContent = [
                      'ERROR: Line ', e.lineno, ' in ', e.filename, ': ', e.message].join('');
                }
            }
            setTimeout(hluw, 600000);   // тут рекурсивный таймаут
       }, 4000);                          // первый раз срабатывает через 4000, потом каждые 600 000 мс


        // раз в 62 секунды обновляет время везде где находит элемент с аттрибутом "time"
        if (relativeTime)
        {
            setInterval(function()
            {
                //console.log('doMoment()');
                doMoment();
            }, 62000);
        }
    }
    lastIndex = itemsPerLoad;

    digestFill(1, 0, itemsPerLoad);
    updateBadges(); //console.log('вызов updateBadges из onPageInit onair');


    // таб "общие"
    $$('#air').on('show', function () {
        lastIndex = itemsPerLoad;
        $$('#onairResults').html('');
        digestFill(1,0, itemsPerLoad);
        $$('#navbar-name').text('Чат-каналы');
        updateBadges(); //console.log('вызов updateBadges из таба #air ');
    });

    // таб "личка"
    $$('#personal').on('show', function () {
        lastIndex = itemsPerLoad;
        $$('#personalResults').html('');
        digestFill(0,0, itemsPerLoad); 
//        digestPersonalFill(0, itemsPerLoad); 
        $$('#navbar-name').text('Личка');        
        updateBadges(); //console.log('вызов updateBadges из таба personal');
    });
    
    // таб "добавить канал"
    $$('#add_channel').on('show', function () {
        $$('#navbar-name').text('Создать канал'); 
        friendsFill('invite_friends'); // аргумент = имя шаблона
        
$$('input#allowgps').on("change", function()
{
    if (this.checked == true)
    {
        doGeolocation();
        console.log('allowgps включен, вызов doGeolocation()');

        // дадим телефону поймать координаты
        setTimeout(function()
        {
            $$('input[name=lat]').val(myApp.ls.lastLat);
            $$('input[name=lng]').val(myApp.ls.lastLng);
            // если не успел, отключим чекбокс и сообщим юзеру
            if (!myApp.ls.lastLat || !myApp.ls.lastLng)
            {
                $$('input[name=allowgps]')[0].checked = false;
                myApp.addNotification({
                    title: 'Геопозиция не определена',
                    message: 'Возможно, телефон не видит спутников?',
                    hold: 4000
                });                        
            }
        },1000);
    }
    else 
        {
            console.log('allowgps отключен');
/*
            // удалять нельзя
            myApp.ls.removeItem('lastLat');
            myApp.ls.removeItem('lastLng');
*/
        }
})

    });    
    
    
    // таб "найти канал"
    //! каналы - поиск
    $$('#find_channel').on('show', function () 
    {
        $$('#navbar-name').text('Найти канал');
        var autocompleteStandaloneAjax = myApp.autocomplete({
            input: '#autocomplete-dropdown-expand-channels',
            openIn: 'dropdown', //open in page
            expandInput: true, // expand input
            backOnSelect: true,
            valueProperty: 'id_channel', //object's "value" property name
            textProperty: 'name', //object's "text" property name
            limit: 20,
            cache : false,
            notFoundText: 'Nothing found',
            preloader: true, //enable preloader
            source: function (autocomplete, query, render) {
                var results = [];
                if (query.length === 0 || query.length < 2) {
                    render(results);
                    return;
                }
                autocomplete.showPreloader();
                $$.ajax({
                    url: API_URL+'channels/search/'+query,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        for (var i = 0; i < data.length; i++) {
                            //if (data[i].name.toLowerCase().indexOf(query.toLowerCase()) >= 0) 
                            results.push(data[i]);
                            //console.log($$('.autocomplete-dropdown-in'));
                            //console.log(data[i]);
                        }
                        autocomplete.hidePreloader();
                        render(data);
                    }, error: function (data) { 
                        console.log(data);                        
                        autocomplete.hidePreloader();
                        results.push(data);
                        render(data);
                        }
                });
            },
            onChange: function (autocomplete, value) 
            {
                if (value.id_channel >= 0) getChannelMessages(value.id_channel, value.name, 0);
                //console.log('грузим найденный канал',value);
            }
        });
    });


        
        
        
        



    $$('.onair-content.infinite-scroll').on('infinite', function (page) 
    {
/*
        if (loading) return;
        loading = true;
        
        setTimeout(function()
        {
            loading = false;
            */
           myApp.detachInfiniteScroll($$('.infinite-scroll'));
/*
            if (lastIndex >= maxItems) {
              myApp.detachInfiniteScroll($$('.infinite-scroll'));
              $$('.infinite-scroll-preloader').remove();
              return;
            }*/
            
            var html = '';
            if ($$('.tab-link.active').attr('href')=='#air')
            {
                digestFill(1,lastIndex, itemsPerLoad);
                lastIndex = lastIndex+ itemsPerLoad;//$$('#onairResults li').length;
            }

            if ($$('.tab-link.active').attr('href')=='#personal')
            { 
//               digestPersonalFill(0,500); // лички мало и не будем лохматить бабушку
                digestFill(0,lastIndex, itemsPerLoad); 
                lastIndex = lastIndex+ itemsPerLoad;//$$('#onairResults li').length;

            }
//        }, 10);
    });    
});





//! friends
/*******************************************/
myApp.onPageInit('friends', function (page) 
{
    friendsFill('friends'); // аргумент = имя шаблона
    
    $$('#find_friends').on('show', function () 
    {
        var profile = myApp.formGetData('profile');
        $$('span.mycity').text(profile.city);
        $$('span.mycycle').text(profile.motorcycle);    
    
        var autocompleteStandaloneAjax = myApp.autocomplete({
            input: '#autocomplete-dropdown-expand-friends',
            openIn: 'dropdown', //open in page
            expandInput: true, // expand input
            backOnSelect: true,
            valueProperty: 'id_user', //object's "value" property name
            textProperty: 'name', //object's "text" property name
            limit: 10,
            preloader: true, //enable preloader
            source: function (autocomplete, query, render) {
                var results = [];
                if (query.length === 0 || query.length < 3) {
                    render(results);
                    return;
                }
                autocomplete.showPreloader();
                $$.ajax({
                    url: API_URL+'users/search/'+JSON.stringify({"telname": query}),
                    method: 'GET',
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                            /*for (var i = 0; i < data.length; i++) {
                            ///if (data[i].name.toLowerCase().indexOf(query.toLowerCase()) >= 0) 
                            //results.push(data[i]);
                            //console.log($$('.autocomplete-dropdown-in'));
                            //console.log(data[i]);
                            }*/
                        autocomplete.hidePreloader();
                        render(data);
                    }
                });
            },
            onChange: function (autocomplete, value) 
            {
                getProfile(value.id_user);
            }
        });
    
    
        $$('#searchToFriends a').on('click', function() 
        {
    //        var profile = myApp.formGetData('profile');
            switch (this.dataset.link) 
            {
                case 'city': 
                if (!profile || !profile.id_city)
                {
                    myApp.alert('Поврежден профиль. Выйди и зайди снова.');
                    return;
                }
                var query = {"city": profile.id_city};
                break;
        
                case 'motorcycle': 
                if (!profile || !profile.id_motorcycle)
                {
                    myApp.alert('Не указан мотоцикл в профиле.');
                    return;
                }
                var query = {"motorcycle": profile.id_motorcycle};
                break;        
                
                case 'city-motorcycle':
                if (!profile || !profile.id_motorcycle || !profile.id_city)
                {
                    myApp.alert('Не указан мотоцикл в профиле или поврежден профиль. Если мотоцикл указан, выйди и зайди снова.');
                    return;            
                }
                var query = {"city": profile.id_city, "motorcycle": profile.id_motorcycle};
                break;                
            }
        
            myApp.showPreloader();        
            $$.ajax({
                url: API_URL+'users/search/'+JSON.stringify(query),
                method: 'GET',
                cache: false,        
                dataType: 'json',
                success: function(response)
                {
                    var friends = (response);
                    var html = Template7.templates.friends({'friends' : friends});
                    $$('#searchToFriendsResults').html('').append(html); // добавит дубли при обновлении стр
                    document.getElementById('searchToFriendsResults').scrollIntoView();
    
                    if (!friends || friends.length < 2)
                        $$('#noSearchToFriendsResults').show();                
                },
                error:  function(response)
                {
                    console.log('searchToFriends error',response);
                },
                complete: function()
                {
                    doMoment();                    
                    myApp.hidePreloader();                    
                }
            });
    
        });
    
    });    
});    





//! feed 
/*******************************************/
myApp.onPageInit('feed', function (page) 
{
    myApp.showPreloader();    
    photoBrowser.params.photos = [];
    
    feedFill(0, itemsPerLoad); 

    var lastIndex = itemsPerLoad;
    $$('.infinite-scroll').on('infinite', function () 
    {
        if (loading) return;

        loading = true;
        setTimeout(function()
        {
            loading = false;
            if (lastIndex >= maxItems) {
              myApp.detachInfiniteScroll($$('.infinite-scroll'));
              $$('.infinite-scroll-preloader').remove();
              return;
            }
            var html = '';

            feedFill(lastIndex, itemsPerLoad);             
            lastIndex = itemsPerLoad+lastIndex;
        }, 10);
    });     
});




/*******************************************/
//! subscribe 
myApp.onPageInit('subscribe', function (page) 
{
   subscribeFill();
});




//! map
/*******************************************/
myApp.onPageInit('map', function (page) 
{
    db.readTransaction(function (tx) 
    {
        tx.executeSql("select * from hotels",[],function(tx, results)
        { hotels = results.rows });
    
        tx.executeSql("select * from parkings",[],function(tx, results)
        { parkings = results.rows });
    
        tx.executeSql("select * from places",[],function(tx, results)
        { places = results.rows });
    
        tx.executeSql("select * from services",[],function(tx, results)
        { services = results.rows });
    
        tx.executeSql("select * from tireservices",[],function(tx, results)
        { tireservices = results.rows });
        
        tx.executeSql("SELECT trips.accuracy, trips.id_city_finish, trips.id_city_start, trips.id_trip, trips.id_user, trips.lat, trips.lng, trips.ts,  friends.name FROM trips left join friends on trips.id_user = friends.id_user",[],function(tx, results)
        { trips = results.rows });    
    },function (tx, error) 
    {
        console.log(tx,error);
    });
    

    // запрещает открытие панелей меню свайпом
    $$('#map').on('touchstart', function (e){e.stopPropagation()})
    
/*
// пробуем избавиться от неполной отрисовки карты
document.getElementById('map').style.height = (window.innerHeight-44)+'px';
document.getElementById('map').style.width = window.innerWidth+'px';
window.addEventListener("orientationchange", function () 
{
    //console.log("The orientation of the screen is");
    document.getElementById('map').style.height = (window.innerHeight-44)+'px';
    document.getElementById('map').style.width = window.innerWidth+'px';
});
*/


//console.log('map', page.query, page.query.lat, page.query.lng);

    setTimeout(function()
    {
        //console.log('init "map"');
        initialise(page.query.lat, page.query.lng); // google maps           
    }, 300);

    if (screen.width > 1280) // только для больших экранов
    {
        $$('body').addClass('map');
        // покажем сообщение 2 раза (string)11 и больше не будем
        if ( parseInt(localStorage.getItem('mapLargeScreen')) < 11 )
        {
            localStorage.setItem('mapLargeScreen', localStorage.getItem('mapLargeScreen') + 1); // это СТРОКА
            myApp.addNotification({
                title: 'Для компьютеров и планшетов мы будем расширять карту на весь экран.',
                message: 'Ты не против?',
                hold: 4000
            });        
        }
    } 

    // добавление POI 
    $$('form.add_poi').on('submit', function (e) 
    {
        $$('input.add_poi_button').addClass('disabled');
        e.stopPropagation();
        e.preventDefault();
        myApp.showProgressbar();
        var data = JSON.stringify(myApp.formToJSON(this));
    
        $$.ajax(
        {
            method: 'POST', 
            url: API_URL+'map/poi',
            data: data,
            success: function(response)
            {
                //console.log(JSON.parse(response));
                if (!isNaN(JSON.parse(response).lastInsertId) && parseInt(JSON.parse(response).lastInsertId) > 0)
                {
                    loadpois();
                    myApp.hideProgressbar();
                    myApp.closeModal();
                    myApp.addNotification({
                        title: 'Готово!',
                        message: 'Объект успешно добавлен и появится на карте через пару минут',
                        hold: 4000
                    });
                }
                else
                {
                    myApp.hideProgressbar();
                    myApp.closeModal();            
                    myApp.addNotification({
                        title: 'Что-то пошло не так',
                        message: 'Сообщи нам об этом',
                        hold: 4000
                    });
                }    
                
            },
            error: function(response)
            {
                myApp.hideProgressbar();
                myApp.closeModal();       
                myApp.addNotification({
                    title: 'Ошибка связи',
                    message: 'Кажется, нет связи с сервером',
                    hold: 4000
                });
            }    
        });
        $$('input.add_poi_button').removeClass('disabled');
    });

})



//! help_list
/*******************************************/
myApp.onPageInit('help_list', function (page) 
{
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(
		            geolocationSuccess, geolocationFailure);
	}
	else {
    myApp.addNotification({
        title: 'Это устройство не поддерживает геолокацию',
        message: 'Сортировка по расстоянию невозможна',
        hold: 4000
    });
	}
    function geolocationSuccess(position) {
        myApp.ls.lastLat = position.coords.latitude;
        myApp.ls.lastLng = position.coords.longitude;
        //console.log('запись в localStorage', position.coords.latitude, position.coords.longitude);

    }
    function geolocationFailure(positionError) {
        myApp.addNotification({
            title: 'Ошибка определения координат',
            message: 'Возможно телефон не видит спутников',
            hold: 4000
        });
    }
});





myApp.onPageBack('messages', function(page)
{
    if ($$('.tab-link.active').attr('href')=='#air')
    {
        $$('#onairResults').html('');

        digestFill(1,0, itemsPerLoad);
        lastIndex = itemsPerLoad;//$$('#onairResults li').length;
    }

    if ($$('.tab-link.active').attr('href')=='#personal')
    {
            $$('#personalResults').html('');
//               digestPersonalFill(0,500); // лички мало и не будем лохматить бабушку
        digestFill(0,0, itemsPerLoad); 
        lastIndex = itemsPerLoad;//$$('#onairResults li').length;

    }
    
    updateBadges(); //console.log('вызов updateBadges из onPageBack messages');
    if (oldPhotos)    
        photoBrowser.params.photos = oldPhotos;

    myApp.detachInfiniteScroll($$('.messages-content.infinite-scroll'));
    
    
});





// проверяет выбран ли хотя бы один юзар перед добавлением юзеров в канал
function check_select()
{
    if ( $$('#addmembers').val().length > 0 )
    {
        $$('#add_button').show();
        $$('.adding').hide();
    }

    else
    {
        $$('#add_button').hide();    
        $$('.adding').show();
    }
}


//! добавление юзера в канал
function add_members_formsubmit()
{
    var formData = myApp.formToJSON('form#add_members_form');
    formData.id_channel = $$('.page-on-left')[0].dataset.id_channel;
    
    //console.log('id_channel',id_channel, 'formData',formData);

    $$.ajax(
    {
        method: 'POST', 
        url: API_URL+'channels/addmembers',
        data: JSON.stringify(formData),
        success: function(response)
        {
            myApp.addNotification({
                    title: 'Готово.',
                    message: 'Твои друзья смогут читать этот канал.',                        
                    hold: 2000
                });
                mainView.router.back();
        },
        error: function(response)
        {
            myApp.addNotification({
                    title: 'Что-то пошло не так©',
                    message: 'Попробуй сделать это позже',
                    hold: 4000
                });
            console.log('add chnannel error: '+response);                
        }    
    });    

}





//! удаление юзера из канала
myApp.onPageInit('channel_info', function (page) 
{
    setTimeout(function()
    {
        friendsFill('add_members'); // аргумент = имя шаблона
        $$('.swipeout').on('delete', function () 
        {
            var id_channel = $$('.page-on-left')[0].dataset.id_channel;
            var id_user = this.dataset.id_user;
            
            $$.ajax(
            {
                method: 'DELETE', 
                url: API_URL+'channels/'+id_channel+'/'+id_user+'/member',
                success: function(response)
                {
                    var response = JSON.parse(response);
                    if (response.result == 1)
                    {
                        myApp.addNotification({
                                title: 'Участник удален из канала',
                                hold: 2000
                            });
                    }
                    else
                    {
                        myApp.addNotification({
                                title: 'Что-то пошло не так. Попробуй позже.',
                                hold: 4000
                            });
                    }
                                
                },
                error: function(response)
                {
                    console.log('delete Member From Channel error',response);
                }    
            });

        });
    },500)
});






//! trip
/*******************************************/
myApp.onPageInit('trip', function (page) 
{
    if (parseInt(myApp.ls.inTrip) == 1)
    {
/*
        if (!navigator.geolocation)
        {
            output.innerHTML = "<p>Это устройство не поддерживает геолокацию</p>";
            return;
        }
        
        function success(position) 
        {
            getStaticmap(position);
        };
        function error() { 
            output.innerHTML = "Unable to retrieve your location" 
            };
        
        navigator.geolocation.getCurrentPosition(success, error);
*/
        
        $$('input#trip')[0].checked = true;
        $$('div#triphint').hide();
        $$('#tripmap').show();
    }
    else
        deleteTrip();
});












//! выход
function exit()
{
    loadWorker.terminate();
//    loadWorker = null;
    myApp.ls.removeItem('token');
    myApp.ls.setItem('digest_updated', '0');
    myApp.ls.setItem('messages_updated', '0');
    myApp.ls.setItem('pois_updated', '0');    
    myApp.ls.removeItem('currentstatus');
    myApp.formDeleteData('profile');
    
    // перед возможной сменой юзера очистим "его" таблицы
    db.transaction(function (tx) 
    {
        tx.executeSql("DELETE FROM feed",[],
        function(tx, results) {
            console.log('delete feed',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete feed Error ',error);
        });
        tx.executeSql("DELETE FROM digest",[],
        function(tx, results) {
                console.log('delete digest',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete digest Error ',error);
        });

        tx.executeSql("DELETE FROM digest_personal",[],
        function(tx, results) {
                console.log('delete digest_personal',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete digest_personal Error ',error);
        });

        tx.executeSql("DELETE FROM friends",[],
        function(tx, results) {
           console.log('delete friends',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete friends Error ',error);
        });
        tx.executeSql("DELETE FROM subscribe",[],
        function(tx, results) {
           console.log('delete subscribe',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete subscribe Error ',error);
        });
        tx.executeSql("DELETE FROM messages",[],
        function(tx, results) {
           console.log('delete messages',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete messages Error ',error);
        });

        tx.executeSql("DELETE FROM hotels",[],
        function(tx, results) {
           console.log('delete hotels',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete hotels Error ',error);
        });                
        tx.executeSql("DELETE FROM parkings",[],
        function(tx, results) {
           console.log('delete parkings',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete parkings Error ',error);
        });                
        tx.executeSql("DELETE FROM places",[],
        function(tx, results) {
           console.log('delete places',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete places Error ',error);
        });                
        tx.executeSql("DELETE FROM services",[],
        function(tx, results) {
           console.log('delete services',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete services Error ',error);
        });                        
        tx.executeSql("DELETE FROM tireservices",[],
        function(tx, results) {
           console.log('delete tireservices',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete tireservices Error ',error);
        });                                
        tx.executeSql("DELETE FROM trips",[],
        function(tx, results) {
           console.log('delete trips',results.rowsAffected);
        }, function (tx, error) {
            console.log('delete trips Error ',error);
        });        
    });    

    myApp.loginScreen();
}





// вычисляет ссылки и картинки в постах юзера
// формирует для них html-окружение
// вызывается везде, где есть юзер текст
function linkify(inputText) 
{
    if (!inputText) return false;
    var patternhref = /(href=)/gi;
    if (patternhref.test(inputText)) return inputText;

//    var patternhttp = /([-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\b(\/?[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?)/gi; // оригинал
    var patternhttp = /(http.+\/\/)(.{2,256}\.[a-z]{2,4}\b(\/?[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?)/gi; // с групами и отловом протокола
    var patterngps = /\[([-0-9].+)\,([-0-9].+)\]/g;
    
    var pattern = /(http:\/\/|https:\/\/)|([a-zA-Z0-9-\_]+\.[a-zA-Z]+|\.[a-zA-Z]+)/i;
    var parts, ext = ( parts = inputText.split("/").pop().split(".") ).length > 1 ? parts.pop() : "";
    var pics = /\S*(jpg|jpeg|gif|png|svg)/ig;

    var myArray = [];
    var isphoto = false;   

    while ((myArray = pics.exec(inputText)) !== null) {
        photoBrowser.params.photos.push(myArray[0]);//inputText.replace(patternhttp, '$1'));
        isphoto = true;
    }
    if (isphoto) {
        return inputText.replace(patternhttp, '<img data-src="$1$2" class="lazy-fadeIn lazy-loaded" src="$1$2" onclick="photoBrowser.open('+(photoBrowser.params.photos.length-1)+')">');
    }
    else if (pattern.test(inputText))
    {
        //return inputText.replace(patternhttp, '<a href="#" onclick="event.stopPropagation();window.open(\'$1$2\',\'_blank\')">$2</a>'); // оригинал, если заглючит
        return inputText.replace(patternhttp, '<a href="$1$2" target="_blank" class="external" >$2</a>');
        
    }
    else if (patterngps.test(inputText)) {
        return inputText.replace(patterngps, '<br><a href="map.php?lat=$1&lng=$2" role="external">GPS-координаты</a>');
    }
    else
    return inputText;
}





//! профиль -  просмотр чужого
function getProfile(id_user) {
    var profile = new Promise(function(resolve, reject) 
    {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', API_URL+'users/'+id_user, true);
        xhr.setRequestHeader('x-access-token', myApp.ls.getItem('token'));    
        
        xhr.onload = function() {
          if (this.status == 200) {
            //console.log('данные загружены, делаем resolve');
            resolve(this.response);
          } else {
            var error = new Error(this.statusText);
            error.code = this.status;
            reject(error);
          }
        };
        
        xhr.onerror = function() {
          reject(new Error("Network Error"));
        };
        myApp.mainView.loadPage('profile_view.html');

        xhr.send();
    });

/*
    //--------------
    profile.then(function(profile)
    {
        console.log('then открываем стр профиля',profile);
        return profile;

    });    
*/    
    /******************/
    profile.then(function(response)
    {
        //console.log('then формируем-заполняем профиль');        
        myApp.mainView.loadPage('profile_view.html');
        
        var userprofile = [];
        userprofile.push(JSON.parse(response)[0]);

       // формируем кнопки - написать, позвонить, добавить/убрать
        var buttons1 = [{text: 'Написать', onClick: function () { getChannelMessages(0, userprofile[0].name, id_user) }}];
        if ( parseInt(userprofile[0].active) == 1 && parseInt(myApp.formGetData('profile').active) == 1 )
            buttons1.push({ text: '<a href="tel:+'+userprofile[0].phone+'" class="external">Позвонить</a>'} );

        if ( userprofile[0].is_friend )
            var buttons2 = [{text: 'Удалить из друзей',onClick: function () 
                { 
                    unfriend(id_user); 
                    //mainView.router.back() 
                }}];
        else
            var buttons2 = [{text: 'Добавить в друзья', onClick: function () 
                { 
                    tofriend(id_user); 
                    //mainView.router.back() 
                }}];

        var buttons3 = [{text: 'Отмена', color: 'red' }];           
        
        var friends_callto = [buttons1, buttons2, buttons3];

        // компилим шаблон
        html = Template7.templates.profile_view({'profile' : userprofile});

        $$('#profile_viewResults').append(html);    
        // по нажатию на кнопку в профиле...

        $$('#callto').on("click", function() 
        {
            // если это не я - вызываем меню
            if ( parseInt(id_user) != parseInt(myIdUser) )
                myApp.actions(friends_callto);
            // если это я — спрашиваем 
            else
                myApp.confirm('Редактировать профиль?', function () {myApp.mainView.loadPage('profile.html')});
        });

       doMoment();                
    });
    //------------
  
    profile.then(function()
    {
        //console.log('then грузим аватар');                
        $$.ajax(
        {
            method: 'GET', 
            cache: false,
            url: API_URL+'users/'+id_user+'/avatar',
            success: function(response)
            {
                var avatar = JSON.parse(response).avatar;
                $$('#avatar').attr('src', avatar);
                $$('#avatar').attr('data-src', avatar);
            }
        });
    });
    
    /******************/
    profile.catch(function(e){
        myApp.alert(e);
    });
   
}  
  



// раскладывает колво непрочитанных по badges
function updateBadges()
{
    // badges в личке
    db.transaction(function (tx) 
    {
        tx.executeSql("select id_from, sum(unread) as unread from messages where id_channel = 0 /*and unread > 0 */ group by id_from",[],function(tx, results)
        {
            for (var i = 0; i < results.rows.length; i++) 
            { 
                $$('#unreadPrivateBadge-'+results.rows.item(i).id_from).text(results.rows.item(i).unread);
                if (results.rows.item(i).unread > 0) $$('#unreadPrivateBadge-'+results.rows.item(i).id_from).removeClass('hide');
                else $$('#unreadPrivateBadge-'+results.rows.item(i).id_from).addClass('hide');
            //console.log(results.rows.item(i).unread, 'мессаг от юзера',results.rows.item(i).id_from);
            }
        });
    });
    
    

    // badges в каналах
    db.transaction(function (tx) 
    {
        tx.executeSql("select id_channel, sum(unread) as unread from messages where id_channel > 0 /*and unread  > 0*/ group by id_channel",[],function(tx, results)
        {
            for (var i = 0; i < results.rows.length; i++) 
            { 
                $$('#unreadChannelBadge-'+results.rows.item(i).id_channel).text(results.rows.item(i).unread);
                if (results.rows.item(i).unread > 0) $$('#unreadChannelBadge-'+results.rows.item(i).id_channel).removeClass('hide');
                else $$('#unreadChannelBadge-'+results.rows.item(i).id_channel).addClass('hide');
            //console.log(results.rows.item(i).unread,' мессаг в канале', results.rows.item(i).id_channel)               
            }


        //$$('#39').remove().appendTo($$('#149'))
        /*if (myApp.getCurrentView().activePage.name=='onair' && i=="channels") 
        {
            var fc = $$('#onairResults .swipeout:first-child');
            if (h==0 && fc.attr('id')!=results[i][h].id_channel) 
            {
                $$('#'+results[i][h].id_channel).remove().prependTo($$(fc));
            }
        
            if(h!=0 && fc.attr('id')!=results[i][h].id_channel)  
            {
                $$('#'+results[i][h].id_channel).remove().appendTo(fc);
            }
        }*/

        });
    });


    // общие badges внизу и в главном меню
    db.transaction(function (tx) 
    {
        tx.executeSql("SELECT *, private+channels as total from (select IFNULL(sum(unread),0) as private from messages where id_channel = 0) left join (SELECT IFNULL(sum(unread),0) as channels from messages where id_channel >0)",[],function(tx, results)
        {
            if ( parseInt($$('#totalUnreadMsg').text()) != parseInt(results.rows.item(0).total) )
            //console.warn( 'общий счетчик непрочитанных изменился: ' +$$('#totalUnreadMsg').text()+' : ' +results.rows.item(0).total )

            if ( ($$('#totalUnreadMsg').text()) < (results.rows.item(0).total) )
            {
                blumm.play(); // iPhone            
                //console.warn('BLUMM');
            }

            $$('#totalUnreadMsg').text(results.rows.item(0).total);
            (results.rows.item(0).total > 0 ? $$('#totalUnreadMsg').show() : $$('#totalUnreadMsg').hide());
            

            // общий badge каналов
            if (results.rows.item(0).channels > 0)
            {
                $$('#channelUnreadMsg').text(results.rows.item(0).channels);
                $$('#channelUnreadMsg').show();
            }
            else
            {
                $$('#channelUnreadMsg').text(results.rows.item(0).channels);
                $$('#channelUnreadMsg').show();
//                document.getElementById('channelUnreadMsg').style.display; // генерит ошибку на другой вьюхе
                $$('#channelUnreadMsg').show();
                $$('#channelUnreadMsg').hide();

            }
            
            
            // общий badge лички
            if (results.rows.item(0).private > 0)
            {
                $$('#privateUnreadMsg').text(results.rows.item(0).private);
                $$('#privateUnreadMsg').show();
            }
            else
            {
                $$('#privateUnreadMsg').hide();
            }
        });
    });
}



/*
// грузит новые мессаги в каналы и в личку
*/
function startLoadWorker()
{
    if (!!window.Worker && !!window.openDatabase)
    {
        //myApp.showProgressbar();
        
        loadWorker = new Worker('js/load_worker.js');
        loadWorker.addEventListener('message', function(e) 
        {
            function doTransaction(query ,data) 
            {
                db.transaction(function (tx) 
                {
                    if (query=='') return;
                    
                //console.log('query'+query+' data count=',data.length);
                    //if (data.length % query[1] == 0)
                    var cnt = (data.length / query[1]);
                    while (cnt>0) {
                    
                    var str = '';    
                    for ( i = 0; i <= cnt -1   /*&& (i*query[1] < 500*/; i++) 
                    {
                        //console.log(i);
                        if ((i+1)*query[1] > 500) 
                            break;
    
                        str = str + "("+Array(query[1]+1).join("?" ).split('').toString()+")";
                        if (i < cnt -1 && (i+2)*query[1] < 499)
                            str = str +",";
                    }
                    tx.executeSql(query[0]+str,data.splice(0, i*query[1]), function(tx, results) 
                    {
                        //console.log(e.data.url,' rowsAffected ',results.rowsAffected);
                    }, function (tx, error) { console.log('loadWorker Error',error,query) });
    
                    //console.log(i);
                    cnt = (data.length / query[1]);
                    }
                });
            }
            


            // обернем получение результата работы воркера в try/catch
            // если из воркера придет не JSON, то это скорее всего help_list.html
            // который возвращает AppCache при fallback
            // таким образом можно перенаправить юзера в хелплист
            try 
            {
                var results = JSON.parse(e.data.results);
            } catch (err) {
                console.log('loadworker err', err.message);
/*
                myApp.addNotification({
                    title: 'Нет интернета',
                    message: 'Будет доступен только хелп-лист',
                    hold: 2000
                });
*/
                //loadWorker.terminate();
                return;
            }

    
    
    
            var query = [];
            //console.log('из воркера: ', e.data.url);
            var data = [];
        
            $$.each(results, function(i) 
            {

                switch (e.data.url) 
                {
                    case 'channels/digest': 
                    //if (query!='' && results[i].length>0) query = query + ",";
                    //db.transaction(function(tx) {

                    $$.each(results[i], function(h) 
                    {
                        data.push((results[i][h].id_channel ? results[i][h].id_channel: 0),
                        (results[i][h].channel_name ? results[i][h].channel_name : ''),
                        (results[i][h].type_channel ? results[i][h].type_channel : 0),
                        results[i][h].id_user,
                        results[i][h].name,
                        results[i][h].phone,
                        results[i][h].gender,
                        results[i][h].text,
                        (results[i][h].id_urgency ? results[i][h].id_urgency: 0),
                        (results[i][h].urgency ? results[i][h].urgency : ''),
                        (results[i][h].is_subscribe ? results[i][h].is_subscribe : 1),
                        results[i][h].ts);

                        // digest_updated из времени мессаги кажется неправильным
                        /*if (results[i][h].ts > myApp.ls.digest_updated)
                            myApp.ls.setItem('digest_updated',results[i][h].ts); */

                        // digest_updated из текущего времени логичнее
                        myApp.ls.setItem('digest_updated', (new Date().getTime()/1000).toFixed());

                    });   

                    if (i=="channels") 
                    {
                       query = ["INSERT OR REPLACE INTO digest (id_channel, channel_name, type_channel, id_user, name, phone, gender, text, id_urgency, urgency, is_subscribe, ts) values ",12];
                       //console.log('digest to DB', data.length);
                    }
                    else                   
                    {
                        query = ["INSERT OR REPLACE INTO digest_personal (id_channel, channel_name, type_channel, id_user, name, phone, gender, text, id_urgency, urgency, is_subscribe, ts) values ",12];
                        //console.log('digest-pers to DB', data.length);
                    }                        

                    doTransaction(query ,data); 
                    data = [];
                    break;
                    
                    
                    
                    case 'feed': 
                    //console.log('feed result',results);
                    data.push(results[i].id_feed,
                        results[i].type_feed,
                        results[i].id_object,
                        results[i].name,
                        results[i].id_user,
                        results[i].phone,
                        results[i].gender,
                        results[i].city,
                        results[i].name_object,
                        results[i].text,
                        results[i].ts
                    )
                    break;
                    
                    
                    
                    case 'channels/subscribe': 
                    data.push(results[i].id_channel,
                    results[i].channel_name,
                    results[i].type_channel,
                    results[i].ts);
                    //console.log('subscribe result');
                    break;
                    


                    case 'map/trip': 
                    data.push(                        
                    results[i].id_trip,
                    results[i].id_user,                                                
                    results[i].lat,                                                
                    results[i].lng,                        
                    results[i].accuracy,
                    results[i].id_city_start,
                    results[i].id_city_finish,
                    //results[i].map_icon,
                    results[i].ts);
                    //console.log('subscribe result');
                    break;
                              
                    
                    case 'messages/': 
                        if (query!='' && results[i].length>0)
                            query = query + ",";
                            
                        $$.each(results[i], function(h) 
                        {
                            // переместим новые мессаги наверх списка
                            // ...создадим новый элемент
                            if (myApp.getCurrentView().activePage.name=='onair' && i=="channels") 
                            {
                                try 
                                {
                                    var elem = [{
                                    'id_channel' : results[i][h].id_channel,    
                                    'id_user' : results[i][h].id_from,
                                    'name' : results[i][h].name,
                                    'text' : results[i][h].text,
                                    'ts' : results[i][h].ts,
                                    'channel_name' : $$('.swipeout[id="'+results[i][h].id_channel+'"] .item-title').text(),
                                    'id_urgency' : $$('.swipeout[id="'+results[i][h].id_channel+'"]').dataset().urgency
                                    }];
                                } catch(e) {
                                    //console.warn(e);
                                    // тут ошибка при загрузке дайджеста с нуля, так и должно быть
                                    return;
                                }
                                

                                var html = Template7.templates.digest({'channels' : elem});

                                // ...и разместим его выше всех, но ниже "важных"
                                $$('#'+results[i][h].id_channel).html(html).remove().insertBefore($$('.swipeout[data-urgency="0"]')[0]);
                                
                                doMoment();
                            }
                                    
                            data.push(results[i][h].id,
                            results[i][h].id_channel,
                            results[i][h].id_from,
                            results[i][h].id_to,
                            results[i][h].text,
                            results[i][h].ts,
                            results[i][h].name,
                            results[i][h].gender,
                            results[i][h].unread
                            );
    
                            db.transaction(function (tx) 
                            {
                                //(i=="channels" ? table = "digest" : var table = "digest_personal");
                                tx.executeSql("UPDATE digest SET id_user=?, name=?, phone=?, gender=?, text=?, ts=? WHERE id_channel="+results[i][h].id_channel,[
                                results[i][h].id_from,
                                results[i][h].name,
                                results[i][h].phone,
                                results[i][h].gender,
                                results[i][h].text,
                                results[i][h].ts
                                ], function(tx, results) 
                                {
                                    //console.log(e.data.url,' rowsAffected ',results.rowsAffected);
                                }, function (tx, error) { console.log('update digest Error',error) });
                            });

                            if (myApp.getCurrentView().activePage.name=="messages" && e.data.url=="messages/") 
                            {
                                if (parseInt(results[i][h].unread)===1 && $$('.page[data-page="messages"]').dataset().id_channel==results[i][h].id_channel) 
                                {
                                    
                                    var msg = results[i][h];
                                    (msg.id_from == myIdUser ? msg.type = 'sent' :   msg.type = 'received' );
                                    
                                    if (msg.id_from != myIdUser ) 
                                    msg.avatar = '//app.motohelplist.com/img/avatar/'+msg.id_from+'.jpg';
                                    else 
                                    msg.name = '';
                                    //msg.label = msg.id;     // текст под принятыми
                                    //msg.position = msg.id;  // текст под отправленными          
                                    msg.text = linkify(msg.text);
                                    
                                    myMessages.appendMessage(msg, animateChats);
                                }
                            }
                            
                            if (results[i][h].ts > myApp.ls.messages_updated)
                            {
                                myApp.ls.setItem('messages_updated',results[i][h].ts); 
                                //console.log('myApp.ls.messages_updated', myApp.ls.messages_updated);
                            }
                        });
                        
                        

                        /*****************************************/
                        // управление периодичностью load worker - отсюда (по мессагам)
                        // если есть новые мессаги, сбрасываем интервал до минимального
                        // если нет - постепенно увеличиваем
                        if (data.length > 0)
                            myApp.ls.loadWorkerInterval = 1000;
                        else 
                            if (myApp.ls.loadWorkerInterval <= 10000) 
                                myApp.ls.loadWorkerInterval = myApp.ls.loadWorkerInterval * 1 + 500;
                        /*****************************************/

                        break;
                    }
                    //if (i!=results.length-1 && !isNaN(i)) query = query +",";
            }); //each

            
            //        if (query!='') 
            //        {    
            switch (e.data.url) 
            {
                case 'channels/digest': 
                if (data.length==0) break;
                query = '';
                //          if (data[0].id_channel!=0)
                //              query = ["INSERT INTO digest (id_channel, channel_name, type_channel, id_user, name, phone, gender, text, id_urgency, urgency, is_subscribe, ts) values ",12];
                /*          else 
                    query = ["INSERT INTO digest_personal (id_channel, channel_name, type_channel, id_user, name, phone, gender, text, id_urgency, urgency, is_subscribe, ts) values ",12];
                */
                break;
                
                case 'feed': 
                query = ["INSERT OR REPLACE INTO feed (id_feed, type_feed, id_object, name, id_user, phone, gender, city, name_object, text, ts) VALUES ",11];
                          //console.log('feed to DB', data.length);
                break;
                
                case 'channels/subscribe': 
                        //console.log('subscribe to DB', data.length);
                query = ["INSERT OR REPLACE INTO subscribe (id_channel, channel_name, type_channel, ts) VALUES ",4];
                break;
                
                case 'messages/': 
                          //console.log('messages to DB', data.length);
                query = ["INSERT OR REPLACE INTO messages (id, id_channel, id_from , id_to, text, ts, name, gender, unread) VALUES ",9];
                break;
                
                case 'map/trip': 
                          //console.log('trips to DB', data.length);

// удаляем все старые трипы перед загрузкой новых
// временно
db.transaction(function (tx) 
{
    tx.executeSql("DELETE FROM trips WHERE 1",[],
    function(tx, results) {
       //console.log('delete trips (обновление)',results.rowsAffected);
    }, function (tx, error) {
        console.log('delete trips Error (обновление)',error);
    });        
});                          
                query = ["INSERT OR REPLACE INTO trips (id_trip, id_user, lat, lng, accuracy, id_city_start, id_city_finish, ts) VALUES ",8]; //map_icon не пишем, формируем его на лету
                break;

            }

            // запишем в базу по результаты из switch
            doTransaction(query,data);
            data = [];


            if (myApp.getCurrentView().activePage.name=='onair') 
            {
                if ($$('#onairResults').children().length == 0) 
                {
                    $$('#onairResults').html('');
                    digestFill(1,0,itemsPerLoad);
                }
            }

        updateBadges(); //console.log('вызов updateBadges из loadWorker ');        
        myApp.hideProgressbar();
        }, false); // loadWorker.addEventListener

       loadWorker.postMessage({"token": myApp.ls.getItem('token'), "messagelastupdate": myApp.ls.messages_updated,"digestlastupdate": myApp.ls.digest_updated}); 


       // постоянное время работы loadWorker
       //setInterval(function(){ loadWorker.postMessage({"token": myApp.ls.getItem('token'), "messagelastupdate": myApp.ls.messages_updated,"digestlastupdate": myApp.ls.digest_updated}) }, myApp.ls.loadWorkerInterval);


        // изменяемое время работы loadWorker
        var timerId = setTimeout(function tick() 
        {
            loadWorker.postMessage({"token": myApp.ls.getItem('token'), "messagelastupdate": myApp.ls.messages_updated,"digestlastupdate": myApp.ls.digest_updated}) 
            timerId = setTimeout(tick, myApp.ls.loadWorkerInterval);
            //console.log(myApp.ls.loadWorkerInterval);    
        }, 500);
    }
}
   
   
    
    
    

// ловит элементы с атрибутом time
// преобразует время в читаемое
function doMoment()
{
    var relativeTime = parseInt(myApp.formGetData('settings').relativeTime);
    $$('[time]').each(function()
    { 
        if (relativeTime) $$(this).text(moment.unix($$(this).attr('time')).fromNow());
        else $$(this).text(moment.unix($$(this).attr('time')).calendar());
    });
}




/*
/*  регистрация нового юзера
*/
function doRegistration(reg)
{
    $$.ajax(
    {
        method: 'POST', 
        url: API_URL+'users/signup',
        data: JSON.stringify(reg),
        success: function(response)
        {
            var response = JSON.parse(response);
            if (response.status)
            {
                myApp.addNotification({
                    title: response.status,
                    message: '(ответ СМС-центра)',
                    hold: 4000
                });          
            }

            if (response.token && response.id_user)
            {
                myApp.ls.setItem('token', response.token);
                // получим из профиля что есть
                var p = myApp.formGetData('profile');
                // добавим туда выданный сервером id_user
                p.id_user = response.id_user;
                // запишем обратно
                myApp.formStoreData('profile', p);
                
                // убьем лишнее из ls
                myApp.ls.removeItem('f7form-registration');

                // загрузим все
                startLoadWorker();

                myApp.mainView.loadPage('profile.html');
                myApp.allowPanelOpen = true;
                mainView.history = [];                
            }
        },
        error: function(response)
        {
            console.log('doRegistration error', response);
            if (response.status == 400)
            {
                myApp.alert(JSON.parse(JSON.parse(response.response).text).error);
            }

            if (response.status == 403)
                myApp.alert('403');                

            if (response.status == 401)
            {
                myApp.alert('401');  
                myApp.closeModal();                  
            }
        },
        complete : function() {
            $$('#login').removeClass('disabled');
        }
    });
}
    
    


//! пароль - забыл
// отправляет смс с новым паролем
function lostPassword()
{
    $$.ajax(
    {
        method: 'POST', 
        url: API_URL+'users/password',
        data: JSON.stringify(myApp.formGetData('login_password')),
        success: function(response)
        {
            console.log('lostPassword response', response);
        }, error: function(response)
        {
            console.log('lostPassword error', response);
        }
    });
    myApp.alert('На твой номер отправлена СМС с паролем. Вводи его и заходи.');    
}







// очищает историю и отключает кнопку назад
// загружает нужную страницу 
function firstPage(page)
{
    myApp.mainView.loadPage(page+'.html');
    // очищение history и отключение кнопки назад
    mainView.history = [];
    setTimeout(function(){
        $$('.icon-back').hide();
        $$('.page-on-left').remove();
        

// если какая-то из технологий не поддерживается, показвыает алерт
if (!window.openDatabase || !window.Worker || !navigator.onLine)
{
    myApp.alert('Но вы держитесь там, всего вам доброго, хорошего здоровья и настроения.','Устройство не поддерживает нужные технологии');
}

        },1000);
}





// вычисляет тип контента
// доделать
function contentType(url)
{
/*    var http = new XMLHttpRequest();
    http.open('HEAD', url);
    http.onreadystatechange = function(e) {

console.log(e);

    };
    http.send();
*/
        $$.ajax(
        {
            method: 'GET', 
            url: url,
            crossDomain: true,
            //contentType: "OPTIONS",
            dataType : 'json',
            success: function(response)
            {
                console.log(response)
            },
            error: function(response)
            {
                console.log("error", response);
            }    
        });
    
}

  
  

// костыль для отключения анимации чтобы у али не тормозило :)
function animationCrutch()
{
    if ($$('select[name=animation]').val() == 1)
        localStorage.setItem('animation', 300);
    else
        localStorage.setItem('animation', 0);     
        
    location.reload();
}



// обновлятор
window.addEventListener('load', function (e) {
    window.applicationCache.addEventListener('updateready', function (e) {
        if (window.applicationCache.status === window.applicationCache.UPDATEREADY) 
        {
            myApp.modal(
            {
                title:  'Обновление Moto Helplist',
                text: 'Обновление загружено. Установить его прямо сейчас?', 
                buttons: 
                [{
                    text: 'Позже',
                    onClick: function () {
                    return;
                    }
                },
                {
                    text: 'Сейчас', 
                    onClick: function () {
                    window.location.reload();
                    }
                }]
            });
        }
    }, false);
}, false);



/*
// надоедает юзеру и просит добавить иконку на экран
var addtohome = addToHomescreen({
   startDelay: 15,
   skipFirstVisit: true,
   maxDisplayCount: 3,
   autostart: true
});
*/


