function subscribeFill()
{   
    db.readTransaction(function (tx) 
    {
        var query = 'SELECT * from subscribe order by ts DESC';
        tx.executeSql(query,[], function(tx, result) 
        {
            var channels = [];
            for (var i = 0; i < result.rows.length; i++) 
            {
                channels.push(result.rows.item(i));
            }
            if (channels.length > 0)
            {
                html = Template7.templates.subscribe({'subscribe' : channels});
                $$('#subscribeResults').append(html);    
                $$('#noSubscribeResults').hide();
            }
        });
    });
}



//! отписка
// в onair.js