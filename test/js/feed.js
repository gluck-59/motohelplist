function feedFill(skip, qty)
{   

//console.log('feedFill',skip, qty)
    
    html = '';
    db.readTransaction(function (tx) 
    {
        var query = 'SELECT * from feed order by ts DESC LIMIT '+skip+','+qty;
        tx.executeSql(query,[], function(tx, result) 
        {
            var feeds = [];
            for (var i = 0; i < result.rows.length; i++) 
            {
//console.log('result.rows.length',result.rows.length)                
                var feed = {};

                feed.id_feed = result.rows.item(i).id_feed;
                feed.id_user = result.rows.item(i).id_user;                
                feed.gender = result.rows.item(i).gender;
                feed.id_object = result.rows.item(i).id_object;
                feed.city = result.rows.item(i).city;
                feed.name = result.rows.item(i).name;                
                feed.name_object = result.rows.item(i).name_object;                                
                feed.phone = result.rows.item(i).phone;                                                
                feed.text = linkify(result.rows.item(i).text);
                feed.ts = result.rows.item(i).ts;                                                                                
                feed.type_feed = result.rows.item(i).type_feed;                                                                                

                feeds.push(feed);
            }
//console.log(feeds);            

            if (feeds.length == 0)
            {
                $$('.infinite-scroll-preloader').hide();
                return;
            }

            html = Template7.templates.feed({'feeds' : feeds});

            // так делать нельзя потому что старые посты уничтожатся
            //$$('#feedResults').html(html);
            $$('#feedResults').append(html);
            $$('#noFeedResults').hide();$$('#noFeedResults').hide();
            doMoment();
        });
    });
    myApp.hidePreloader();
}


function postevent()
{
    myApp.showPreloader();
    $$.ajax(
    {
        method: 'POST', 
        url: API_URL+'feed/',
        data: JSON.stringify({"message":$$('textarea#postevent').val()}),
        success: function(response)
        {
            var html= '';
            var feeds = [];
            var result = JSON.parse(response);
            var feed = {};

            feed.id_feed = result[0].id_feed;
            feed.id_user = parseInt(result[0].id_user);
            feed.gender = parseInt(result[0].gender);
            feed.id_object = parseInt(result[0].id_object);
            feed.city = result[0].city;
            feed.name = result[0].name;                
            feed.name_object = result[0].name_object;                                
            feed.phone = result[0].phone;                                                
            feed.text = linkify(result[0].text);
            feed.ts = result[0].ts;                                                                                
            feed.type_feed = parseInt(result[0].type_feed);

            feeds.push(feed);
            
            html = Template7.templates.feed({'feeds' : feeds});
            $$('#feedResults').prepend(html);    
            doMoment();
            myApp.hidePreloader();
            myApp.addNotification({
                title: 'Готово!',
                message: 'Твои друзья увидят это у себя в ленте',
                hold: 3000
            });                
            $$('textarea#postevent')[0].value = '';
            $$('#event-share').hide();
        },
        error: function(response)
        {
            console.log(response);
            myApp.hidePreloader();            
        }    
    });        

}