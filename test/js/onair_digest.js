var digestMax;
function digestFill(type, skip, qty)
{   
    photoBrowser.params.photos = [];
    myApp.showProgressbar();

    var channels = [];
    
    // дайджест каналов
    db.readTransaction(function (tx) 
    {
        if (type==0) //личка
        var query = 'SELECT digest_personal.*, COALESCE(sum(messages.unread),0) as unreaded from digest_personal left join messages on digest_personal.id_user = messages.id_from  and messages.id_channel=0 where digest_personal.id_channel=0 group by digest_personal.id_user order by ts DESC LIMIT '+skip+','+qty;
        //'SELECT * from digest where id_channel=0 order by ts DESC LIMIT '+skip+','+qty;

        else // общие
        var query = 'SELECT digest.*, COALESCE(sum(messages.unread),0) as unreaded from digest left join messages on  digest.id_channel=messages.id_channel and messages.id_channel!=0 where digest.id_channel!=0 group by digest.id_channel order by id_urgency DESC, ts DESC LIMIT '+skip+','+qty;//'SELECT * from digest where id_channel!=0 order by id_urgency DESC, ts DESC LIMIT '+skip+','+qty;

        tx.executeSql(query,[], function(tx, result) 
        {
            if (parseInt(myApp.formGetData('settings').sosAirOnly) == 1 && type==1)
            {
                for (var i = 0; i < result.rows.length; i++) 
                {
                    if (result.rows.item(i).id_urgency > 0)
                    {
                        channels.push(result.rows.item(i));
                        channels[i].text = linkify(channels[i].text);                        
                    }
                }
            }
            else
            {
                for (var i = 0; i < result.rows.length; i++) 
                {
                    channels.push(result.rows.item(i));
                    channels[i].text = linkify(channels[i].text);                                            
                }
            }
            if (channels.length == 0)
            {
                $$('.infinite-scroll-preloader').hide();
            } 
            else 
            {
                //console.log(channels);
                if (type==0)
                {
                    var html = Template7.templates.digest_personal({'channels' : channels});
                    $$('#personalResults').append(html); // добавит дубли при обновлении стр
                    $$('#noPersonalResults').hide();
                    myApp.hideProgressbar();                    
                }
                else
                {
                    var html = Template7.templates.digest({'channels' : channels});
                    $$('#onairResults').append(html); // добавит дубли при обновлении стр
                    $$('#noOnairResults').hide();                    
                    myApp.hideProgressbar();
                }
                myApp.hideProgressbar();
            }
            if (channels.length > 0)
            {
                myApp.attachInfiniteScroll($$('.onair-content.infinite-scroll'));
            }

            //else 
             $$('.infinite-scroll-preloader').hide();


            doMoment();
        });
        
/*            // запомним время последнего обновления любого канала
        tx.executeSql('select max(ts) as max from digest', [], function(tx, result) 
        {
            digestMax = result.rows.item(0).max;
        }); */
        myApp.hideProgressbar();
    });
}



//! подписка
function subscribe(data) 
{
    console.log('subscribe '+data);
    $$.ajax(
    {
        method: 'POST', 
        url: API_URL+'channels/'+data+'/subscribe',
        success: function(response)
        {
            db.transaction(function (tx) 
            {
                tx.executeSql('UPDATE digest SET is_subscribe = 1 WHERE id_channel = '+data);
            });            
            myApp.addNotification(
            {
                title: 'Подписка оформлена',
                hold: 2000
            });
        },
        error: function(response)
        {
            console.log('subscribe error: '+response);                
        }    
    });
};


//! отписка
function unsubscribe(data) 
{
    console.log('unsubscribe '+data);
    $$.ajax(
    {
        method: 'DELETE', 
        url: API_URL+'channels/'+data+'/subscribe',
        success: function(response)
        {
            db.transaction(function (tx) 
            {
                tx.executeSql('DELETE FROM subscribe WHERE id_channel = '+data);
                tx.executeSql('UPDATE digest SET is_subscribe = 0 WHERE id_channel = '+data);
            });

            myApp.addNotification(
            {
                title: 'Подписка отменена',
                hold: 2000
            });
        },
        error: function(response)
        {
            console.log('unsubscribe error: '+response);                
        }    
    });
};





//! создание канала
// если хоть один френд отмечен — это private канал
function checktype() 
{
console.log('invitefriends', $$('#invitefriends').val());

    if ( $$('#invitefriends').val().length > 0 )
    {
        $$('input[name=type_channel]').val('3'); // 3 = private
        $$('#urgency').hide();
        $$('select[name=urgency]').val(0);
        $$('input#create_channel').val('Создать приватный канал');
    }
    else
    {
        $$('input[name=type_channel]').val('4'); // 4 = group
        $$('#urgency').show();
        $$('input#create_channel').val('Создать общий канал');
    }
}

function add_channel_formsubmit()
{
    var val = $$('input[name=name_channel]').val();
    if ( val.length == 0)
    {
        myApp.alert('Назови как-нибудь свой канал чтобы не потерять его в эфире');
        //$$('input[name=name_channel]').css('background-color','#dfd');
        return false;
    }
    else
    {
        //myApp.showPreloader()
        var formData = myApp.formToJSON('#add_channel_form');
        formData.username = myApp.formGetData('profile').name;

        $$.ajax(
        {
            method: 'POST', 
            url: API_URL+'channels/add',
            data: JSON.stringify(formData),
            success: function(response)
            {
                getChannelMessages(JSON.parse(response).id_channel, $$('input[name=name_channel]').val(), 0);
            },
            error: function(response)
            {
                myApp.alert('Нет связи. Попробуй позже.');
                console.log('add chnannel error: '+response);                
            }    
        });        
    }

} 



