var getStackTrace = function() {
  var obj = {};
  Error.captureStackTrace(obj, getStackTrace);
  return obj.stack;
};

window.onerror = function(err, url, line, col, msg) 
{


 var trace = getStackTrace();

/*
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
        
    });
*/
    
    
    


    
    var data = {
        'Error': err,
        'URL': url,
        'Line': line,
        'Column': null,
        'Message': null,
        'OS': myApp.device.os+' '+myApp.device.osVersion,
        'HomescreenIcon': JSON.stringify(myApp.device.webView),        
        'UserAgent': navigator.userAgent,
        'Profile': window.localStorage.getItem('f7form-profile')/*
        'Browser': null,
        'Device': null,
        'Digest': digest.item(0).count,
        'Digest_personal': digest_personal.item(0).count,
        'Feed': feed.item(0).count,
        'Friends': friends.item(0).count,
        'Help-list': helplist.item(0).count,
        'Messages': messages.item(0).count,
        'Отели': hotels.item(0).count,
        'Парковки': parkings.item(0).count,
        'Места': places.item(0).count,
        'Сервисы': services.item(0).count,
        'Шиномонтажи': tireservices.item(0).count,
        'В пути': trips.item(0).count*/
    };
    
            

    // HTML5 only
    data['Column'] = !col ? '' : col;
    data['Message'] = !msg ? '' : msg.toString();
    

    try {
        var ua = detect.parse(navigator.userAgent); 
        data['Browser'] = !ua.browser.name ? '' : ua.browser.name;
        data['OS'] = !ua.os.name ? '' : ua.os.name;
        data['Device'] = !ua.device.name ? '' : ua.device.name;

    }
    catch(e) {}

    console.groupCollapsed('Error: ' + data['Error']);
    console.log('URL: ' + data['URL']);
    console.log('Line: ' + data['Line']);
    if(data['Column'])
        console.log('Column: ' + data['Column']);
    if(data['Message'])
        console.log('Message: ' + data['Message']);
    data['Trace'] = trace;
    console.log(data['Trace']);
    console.groupEnd();

    try 
    {
        $$.ajax(
        {
            method: 'POST', 
            url: API_URL+'error',
            data: JSON.stringify(data),
            success: function(response)
            {
//                console.log('lostPassword response', response);
            }, error: function(response)
            {
                console.warn('Detect error', response);
            }
        });
    }
    catch(e) {}

    return true; //suppressErrorAlert
};