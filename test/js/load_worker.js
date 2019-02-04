// обновление каналов
// стоп воркера - self.close();
var i = 0;
var url = '';
var urls = ['map/trip', 'messages/', 'channels/digest', 'map/trip', 'feed', 'messages/', 'channels/digest', 'channels/subscribe', 'messages/'];
//var urls = ['messages/'];

self.addEventListener('message', function(e) 
{
//    var timerId = setTimeout(function start() 
//    {

//console.log('e.data.lastupdate=',e.data.digestlastupdate);

if (e.data.digestlastupdate == 'undefined')
    digestlastupdate = 0;
else
    digestlastupdate = e.data.digestlastupdate;


        if (!navigator.onLine) self.close();
       
        if (i>urls.length-1) i = 0;

        url = urls[i]; 

        if (urls[i]=='channels/digest') {
            url = urls[i]+'?filter=ts greaterequal '+digestlastupdate;
        }
           

        if (urls[i]=='messages/') 
            url = urls[i]+e.data.messagelastupdate;
       
        transport = new XMLHttpRequest();
        transport.open('GET', 'https://app.motohelplist.com/api/v1/'+url, false);  
        transport.setRequestHeader('x-access-token', e.data.token);
        transport.send();

            if(transport.status == 200)
            {
                postMessage({'url' : urls[i], 'results' : transport.response});
                i++;    
            } 
}, false);
