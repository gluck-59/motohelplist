if (!navigator.onLine) 
{
    $$('.filter').hide();
}

function submitfilter(form)
{
    if (!myApp.formGetData('profile').active || parseInt(myApp.formGetData('profile').active) != 1)
    {
        myApp.alert('Хелп-листом могут пользоваться только те, кто сами могут помочь собратьям.');
        setTimeout(function() { myApp.mainView.loadPage('profile.html') }, 1000);
        return false;
    }

    myApp.showPreloader('Поиск участников');
    
    template = $$('#hlusers').html();
    relativeTime = parseInt(myApp.formGetData('settings').relativeTime);    
    compiledTemplate = Template7.compile(template);
    //openDb();
   
//    var user = '';
    db.readTransaction(function (tx) 
    {
        var feature = [];
        var query = 'SELECT * FROM helplist where 1 ';

        // отфильтруем юзерей по фичам
        $$('#filter_form input:checked').each(function()
        {
            feature.push(this.id);
        });
        feature = feature.join(',');
        if (feature.length > 0) query = query+'in('+feature+')';

        // выберем юзерей с нужными фичами
        tx.executeSql(query,[], function(tx, result) 
        { 
            //console.log(result.rows);
            if (result.rows.length == 0) 
            { 
                myApp.addNotification({
                    title: 'Никого не найдено',
                    message: 'Измени параметры фильтра или загрузи хелп-лист снова',
                    hold: 4000
                });
                myApp.hidePreloader();
                return false;
            }
          
            if (myApp.ls.lastLat == null || myApp.ls.lastLng == null) 
            {
                myApp.hidePreloader();                
                myApp.addNotification({
                    title: 'Подсчет расстояний невозможен',
                    message: 'Разреши определение своего местоположения.',
                    hold: 4000
                });                
            }
            else
            {
                var lastlat = parseFloat(myApp.ls.lastLat);
                var lastlng = parseFloat(myApp.ls.lastLng);
            }

            users =[];
            // рассчитаем дистанцию от "я здесь" до города прописки юзера
            for(var i = 0; i < result.rows.length; i++) 
            {
                if (lastlat && lastlng) 
                {
                    var distance = calc_distance(lastlat, lastlng, parseFloat(result.rows.item(i).lat), parseFloat(result.rows.item(i).lng));//.toFix();  
                    //distance = distance - (distance%1);
                }       
                user = {};
                user.name = result.rows.item(i).name;
                user.hlcity = result.rows.item(i).city_name;
                user.phone = result.rows.item(i).phone;
                user.distance = (distance - (distance%1)); 
                //if (relativeTime) user.since = moment.unix(result.rows.item(i).date_last_login).fromNow();
                //else user.since = moment.unix(result.rows.item(i).date_last_login).calendar();
                user.gender = (result.rows.item(i).gender == 1 ? 'female' : 'male');
                user.description = result.rows.item(i).description;
                if (result.rows.item(i).help_repair) user.help_repair = true;
                if (result.rows.item(i).help_garage) user.help_garage = true;
                if (result.rows.item(i).help_food) user.help_food = true;
                if (result.rows.item(i).help_bed) user.help_bed = true;
                if (result.rows.item(i).help_beer) user.help_beer = true;
                if (result.rows.item(i).help_strong) user.help_strong = true;
                if (result.rows.item(i).help_party) user.help_party = true;
                if (result.rows.item(i).help_excursion) user.help_excursion = true;
    
                users.push(user);

            }

            sortingDist();   
            document.getElementById('scrollto').scrollIntoView();
        }, null);
    });

//myApp.hidePreloader();             
}
    
    
function sortingABC()
{
    myApp.showPreloader('Сортировка по алфавиту');

    users.sort(function (a, b) {
        var an = a.hlcity,
            bn = b.hlcity;
        if (an && bn) {
            return an.toUpperCase().localeCompare(bn.toUpperCase());
        }
        return 0;
    });
    var html = compiledTemplate(({'users' : users}));
    document.getElementById('helplistResults').innerHTML = html;

    $$('#abc').show();
    $$('#dist').hide();                
    
    myApp.hidePreloader();
}




function sortingDist()
{
console.log('sortingDist');
    myApp.showPreloader('Сортировка по расстоянию');

    users.sort(function (a, b) 
    { 
        var an = a.distance;
        var bn = b.distance; 
        if (isNaN(an) || isNaN(bn)) return 0; 
        if(an>bn)return 1;
        if(an<bn)return -1;
        return 0; 
    });

    var html = compiledTemplate(({'users' : users}));
    document.getElementById('helplistResults').innerHTML = html;
    $$('#dist').show();
    $$('#abc').hide();                

myApp.hidePreloader();myApp.hidePreloader();// да блядь ДВА раза

}

    


function calc_distance(lat1, lon1, lat2, lon2) 
{
    var radLat1 = lat1 * (Math.PI / 180);
    var radLon1 = lon1 * (Math.PI / 180);
    var radLat2 = lat2 * (Math.PI / 180);
    var radLon2 = lon2 * (Math.PI / 180);
    var earthRadius = 6372.795;
    var radLonDif = radLon2 - radLon1;
    var atan2top = Math.sqrt(Math.pow(Math.cos(radLat2) * Math.sin(radLonDif), 2) + Math.pow(Math.cos(radLat1) * Math.sin(radLat2) - Math.sin(radLat1) * Math.cos(radLat2) * Math.cos(radLonDif), 2));
    var atan2bottom = Math.sin(radLat1) * Math.sin(radLat2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.cos(radLonDif);
    var deltaAngle = Math.atan2(atan2top, atan2bottom);
    return (earthRadius * deltaAngle);
}
    
   

function getChrome()
{
    window.open('https://www.google.com/chrome', '_blank');
}
            

// автоматическое обновление хелплиста
// только измененные юзеры с момента lastupdate
function updateHelplist(data, to_delete) 
{
/*
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
*/
    if (!window.openDatabase) 
    {
        $$('.filter').hide();
        $$('.no_websql').show();    
        return;
    }
    

    // добавим активных юзеров в базу
    var query = "";
    $$.each(data, function(i) 
    {
        query = query + '('+data[i].id_user+',\''+data[i].name+'\',\''+data[i].city_name+'\',\''+data[i].phone+'\','+data[i].help_repair+','+data[i].help_garage+','+data[i].help_food+','+data[i].help_bed+','+data[i].help_beer+','+data[i].help_strong+','+data[i].help_party+','+data[i].help_excursion+',\''+data[i].description+'\','+data[i].gender+',\''+data[i].date_add+'\','+data[i].lat+','+data[i].lng+')'; 
        
//        query = query + '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', '['+data[i].id_user+', '+data[i].name+', '+data[i].city_name+', '+data[i].phone+', '+data[i].help_repair+', '+data[i].help_garage+', '+data[i].help_food+', '+data[i].help_bed+', '+data[i].help_beer+', '+data[i].help_strong+', '+data[i].help_party+', '+data[i].help_excursion+', '+data[i].description+', '+data[i].gender+', '+data[i].date_add+', '+data[i].lat+', '+data[i].lng+']';

        if (i!=data.length-1) query = query +",";
 
//    });

    db.transaction(function (tx) 
    {

                    tx.executeSql("INSERT OR REPLACE INTO helplist (id_user, name, city_name, phone, help_repair, help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, gender, date_last_login, lat, lng ) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);",[data[i].id_user, data[i].name, data[i].city_name, data[i].phone, data[i].help_repair, data[i].help_garage, data[i].help_food, data[i].help_bed, data[i].help_beer, data[i].help_strong, data[i].help_party, data[i].help_excursion, data[i].description, data[i].gender, data[i].date_add, data[i].lat, data[i].lng]);

/*
        query = "INSERT OR REPLACE INTO helplist (id_user, name, city_name, phone, help_repair, help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, gender, date_last_login, lat, lng) VALUES "+query;
        tx.executeSql(query);
*/
//console.log('юзеров вставлено в хелп-лист');
    });
//console.log('hl worker отработал');    
    });

    
    // убьем неактивных из базы
    var del = [];
    $$.each(to_delete, function(i)
    {
        del.push(to_delete[i].id_user);
    });
    var del = del.join();
    
    if (del.length > 0)
    {
        db.transaction(function (tx) 
        {
            var query = 'DELETE from helplist WHERE id_user IN ('+del+')';
//console.log(query);
            tx.executeSql(query);        
        });
    
        delete to_delete;
    }
    myApp.ls.helplist_updated = new Date().toJSON();
//        $$('#updateHelplistDate').attr('time', (new Date(myApp.ls.helplist_updated).getTime()/1000).toFixed());
    if(relativeTime) $$('#updateHelplistDate').text(moment(myApp.ls.helplist_updated, "YYYY-MM-DD H:mm:ss.Z").fromNow());
    else $$('#updateHelplistDate').text(moment(myApp.ls.helplist_updated, "YYYY-MM-DD H:mm:ss.Z").calendar());
}




// ручное обновление хелплиста ПОЛНОСТЬЮ
function updateHelplistManually()
{
    if (!window.openDatabase)
    {
        $$('#updateHelplistDate').html('Технология не поддерживается');
        return false;
    }

    if (navigator.onLine)
    {
        $$('#updateHelplistDate').html('<img src="img/loader.gif">');
        deleteHelplist();
        localStorage.removeItem('helplist_updated');
        
        //! получение хелп-листа воркером
        // стоп воркера - helplist_worker.terminate()       
        if (!!window.Worker && !!window.openDatabase && navigator.onLine)
        {
            helplist_worker_man = new Worker('js/helplist_worker.js');
            helplist_worker_man.addEventListener('message', function(e) 
            {
                var users = JSON.parse(e.data).users;
                var qty = users.length;

                if (qty > 0)
                {
                    updateHelplist(JSON.parse(e.data).users);
//console.log('updateHelplistManually: в updateHelplist передано '+qty);                    
                }
                helplist_worker_man.terminate();
            }, false);
    
            helplist_worker_man.postMessage({"token": myApp.ls.getItem('token'), "lastupdate": 0});

            helplist_worker_man.addEventListener('error', onError, false);
            function onError(e) {
                document.getElementById('error').textContent = [
                  'ERROR: Line ', e.lineno, ' in ', e.filename, ': ', e.message].join('');
              }    
        }        
    }
    else 
    {
        myApp.addNotification(
        {
            title: 'Нет интернета :(',
            message: 'Попробуй позже.',
            hold: 4000
        });                
    }
}



function deleteHelplist()
{
    //openDb();
    db.transaction(function (tx) {
    tx.executeSql('delete FROM helplist',[], function(tx, result) { 
    }, null);});    
}