// обновление хелплиста
// только измененные юзеры с момента lastupdate
// стоп воркера - self.close();

self.addEventListener('message', function(e) 
{
//console.log('hl worker lastupdate: '+e.data.lastupdate);

    if (!navigator.onLine) self.close();

    transport = new XMLHttpRequest();
    transport.open('GET', '//app.motohelplist.com/api/v1/helplist/'+e.data.lastupdate, true);
    transport.setRequestHeader('x-access-token', e.data.token);
    transport.onreadystatechange = function()
    {
        if(transport.readyState == 4)
        {
            postMessage(transport.response);
        } 
    };
    transport.send();
}, false);