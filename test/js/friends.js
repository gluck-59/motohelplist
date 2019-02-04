//загружает френдов с сервера
function getFriends(template)
{
    myApp.showProgressbar();    
    $$.ajax(
    {
        method: 'GET', 
        url: API_URL+'users/'+myApp.formGetData('profile').id_user+'/friends',
        success: function(response)
        {
            if (friends = JSON.parse(response))
            {
                updateDb(friends);
                setTimeout(function() { friendsFill(template) },300);
            }
        }, error: function(response)
        {
            console.log('getFriends error: ',response);
            if (response.status == 404)
            {
                myApp.showTab('#find_friends');
            }
        }    
    });
    myApp.hideProgressbar();
}   
    



/***********************/
// пишет френдов в базу
function updateDb(friends)
{
    var query = "";
    $$.each(friends, function(i) 
    {
       query = query + "("+friends[i].id_user+",'"+friends[i].name+"',"+friends[i].id_city+",'"+friends[i].city+"',"+friends[i].id_motorcycle+",'"+friends[i].motorcycle+"','"+friends[i].motorcycle_more+"',"+friends[i].phone+","+friends[i].gender+",'"+friends[i].status+"',"+friends[i].help_repair+","+friends[i].help_garage+","+friends[i].help_food+","+friends[i].help_bed+","+friends[i].help_beer+","+friends[i].help_strong+","+friends[i].help_party+","+friends[i].help_excursion+",'"+friends[i].description+"',"+friends[i].ts+")"; 
    
       if (i!=friends.length-1) query = query +",";
    });
    
    query = "INSERT OR REPLACE INTO friends (id_user, name, id_city, city, id_motorcycle, motorcycle, motorcycle_more, phone, gender, status, help_repair, help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, ts) VALUES "+query;
    
    db.transaction(function (tx) 
    {
        tx.executeSql(query);
    });
}



/****************************/
// заполняет преданный template френдами из базы
function friendsFill(template) // агрумент - в какой шаблон заливать
{
    html = '';
    db.readTransaction(function (tx) 
    {
        var query = 'SELECT * from friends order by name';
        tx.executeSql(query,[], function(tx, result) 
        {
            if (result.rows.length == 0)
            {
                getFriends(template);
                return;
            }
            
            var friends = [];
            for (var i = 0; i < result.rows.length; i++) 
            {
                var friend = {};

                friend.id_user = result.rows.item(i).id_user;
                friend.name = result.rows.item(i).name;
                friend.id_city = result.rows.item(i).id_city;
                friend.city = result.rows.item(i).city;
                friend.id_motorcycle = result.rows.item(i).id_motorcycle;
                friend.motorcycle = result.rows.item(i).motorcycle;
                friend.gender = result.rows.item(i).gender;
                friend.status = result.rows.item(i).status;
                friend.help_repair = result.rows.item(i).help_repair;
                friend.help_garage = result.rows.item(i).help_garage;
                friend.help_food = result.rows.item(i).help_food;
                friend.help_bed = result.rows.item(i).help_bed;
                friend.help_beer = result.rows.item(i).help_beer;
                friend.help_strong = result.rows.item(i).help_strong;
                friend.help_party = result.rows.item(i).help_party;
                friend.help_excursion = result.rows.item(i).help_excursion;
                friend.description = result.rows.item(i).description;
                friend.ts = result.rows.item(i).ts;                                                                                

if (friend.help_repair == 0 && friend.help_garage == 0 && friend.help_food == 0 && friend.help_bed == 0 && friend.help_beer == 0 && friend.help_strong == 0 && friend.help_party == 0 && friend.help_excursion == 0 || !myApp.formGetData('profile').active) 
{ 
    //console.log('неактивный friend', friend.name) 
}
else friend.phone = result.rows.item(i).phone;                                                


                friends.push(friend);
            }

            if (template == 'invite_friends') // пригласить друзей при создании канала
            {
                var html = Template7.templates.invite_friends({'invitefriends' : friends});                
                $$('#invitefriends').html(html);                    
            }
            else if (template == 'add_members') // добавить друзей в существующий канал
            {
                var html = Template7.templates.add_members({'addmembers' : friends});                
                $$('#addmembers').html(html);                                    
            }
            else // показать друзей на стр friends
            {
                var html = Template7.templates.friends({'friends' : friends});
                $$('#friendResults').append(html);                    
                $$('div#nofriends').hide();                
                doMoment();
                myApp.hideProgressbar();
            }
        });
    });
}



/************************/
function tofriend(id_user)
{
    $$.ajax(
    {
        method: 'POST', 
        url: API_URL+'users/'+id_user+'/friends',
        data: JSON.stringify({"message":$$('textarea#postevent').val()}),
        success: function(response)
        {
            myApp.addNotification({
                title: 'За дружбу!',
                hold: 2000
            });                
            
            if (friends = JSON.parse(response))   
            {         
                updateDb(friends);
            }
        },
        error: function(response)
        {
            myApp.addNotification({
                title: 'Нет связи',
                message: 'Попробуй позже.',                
                hold: 2000
            });                            
            console.log('tofriend error',response);
        }    
    });        
}



/************************/
function unfriend(id_user)
{
    $$.ajax(
    {
        method: 'DELETE', 
        url: API_URL+'users/'+id_user+'/friends',
        success: function(response)
        {
            myApp.addNotification({
                title: 'Минус один...',
                hold: 2000
            });

            db.transaction(function (tx) 
            {
                tx.executeSql("DELETE FROM friends WHERE id_user = "+id_user);
            });    
        },
        error: function(response)
        {
            myApp.addNotification({
                title: 'Нет связи',
                message: 'Попробуй позже.',                
                hold: 2000
            });                            
            console.log('unfriend error',response);
        }
    });        
}