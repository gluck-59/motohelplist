var scrollTimeout = 0;
var allowScrollMessages = 1;
myApp.onPageInit('messages', function (page) 
{
    oldPhotos = photoBrowser.params.photos;
    photoBrowser.params.photos = [];
    var messages = [];  
   // var loading = false; 
    myMessages = myApp.messages('.messages', {
        messages: messages,
        autoLayout: true,
        messageTemplate: '<div data-message-id="{{id}}" class="message message-{{type}} {{#js_compare "parseInt(this.unread)===1"}}unread{{/js_compare}} {{#if hasImage}}message-pic{{/if}} {{#if avatar}}message-with-avatar{{/if}} {{#if position}}message-appear-from-{{position}}{{/if}}">' +
                '<div class="message-name {{#js_compare "parseInt(this.gender)===1"}}fe{{/js_compare}}male" onclick="getProfile({{id_from}})">{{name}}</div>' +
                '<div class="message-text">{{text}}{{#if ts}}<div class="message-date" time="{{ts}}"></div>{{/if}}</div>' +
                '{{#if avatar}}<div class="message-avatar" style="background-image:url({{avatar}})" onclick="getProfile({{id_from}})"></div>{{/if}}' +
                '{{#if label}}<div class="message-label">{{label}}</div>{{/if}}' +
            '</div>'
    });


//! загрузка картинки
$$('#chat-attach').on('change', function(e)
{
    var messagePic = document.getElementById('chat-attach').files[0];
    var imageType = /^image\//;

    if (messagePic && imageType.test(messagePic.type))
    {
        if (window.File && window.FileReader && window.FileList && window.Blob) 
        {
            var img = document.createElement("img");
            img.src = window.URL.createObjectURL(messagePic);
            img.className='message messagePic right';
            img.onload = function()
            { 
                var MAX_WIDTH = 960;
                var MAX_HEIGHT = 1280;
                var width = img.width;
                var height = img.height;

                if(width > MAX_WIDTH || height > MAX_HEIGHT)
                    {
                        if (width > height) 
                        {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        } 
                        else 
                        {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }

                $$('.messagesHolder').append('<canvas id="messagePicCanvas" width="'+width+'" height="'+height+'">Превью невозможно: устройство не поддерживает нужные технологии.</canvas>');

                var ctx = messagePicCanvas.getContext("2d");
                var hRatio = messagePicCanvas.width / img.width    ;
                var vRatio = messagePicCanvas.height / img.height  ;
                var ratio  = Math.min ( hRatio, vRatio );
                
                messagePicCanvas.width = (img.width*ratio).toFixed();
                messagePicCanvas.height = (img.height*ratio).toFixed();
                
                ctx.drawImage(img, 0,0, img.width, img.height, 0,0, (img.width*ratio).toFixed(), (img.height*ratio).toFixed());

                var dataurl = messagePicCanvas.toDataURL("image/jpeg", 0.6);
                var id_channel = $$('.page[data-page="messages"]').dataset().id_channel;
                var id_to = $$('.page[data-page="messages"]').dataset().id_from;

                myApp.showProgressbar();                    
                var dataurl = dataURItoBlob(dataurl);
                var xhr = $$.ajax(
                {
                    url: API_URL+'img/'+id_channel+'/'+id_to,
                    type: "POST",
                    dataType: 'json', 
                    data: dataurl,
                    success: function (response) 
                    {
                        console.log('картинка загружена', response);
                        myApp.hideProgressbar();                    
                        $$(messagePicCanvas).detach();
                        photoBrowser.params.photos.push(response.text);

                        myMessages.appendMessage({
                        text: '<img data-src="'+response.text+'" class="lazy-fadeIn lazy-loaded" src="'+response.text+'" onclick="photoBrowser.open('+photoBrowser.params.photos.length+')">',
                        type: 'sent',
                        ts: (moment.now()/1000-1).toFixed()
                        },animateChats);
                    }, error: function (error) 
                    {
                        console.log('ошибка загрузки картинки в чат', error);
                        myApp.hideProgressbar();                    
                    }
                });
            }

            setTimeout(function(){ myMessages.scrollMessages() }, 100);
        } else {
            myApp.alert('Загрузка файлов не поддерживается этим устройством');
        }
    }
});


        


    //! Add Message
    var myMessagebar = myApp.messagebar('.messagebar');
    $$('.messagebar a.send-message').on('click', function (e) 
    {
        $$('.messagebar a.send-message').addClass('disabled');
        var messageText = myMessagebar.value();
       
        if (messageText.length === 0) {
            $$('.messagebar a.send-message').removeClass('disabled');
            return;
        }

        if (page.container.dataset.id_channel==0)
            var url =  'users/'+page.container.dataset.id_from+'/messages';
        else 
            var url = 'channels/'+page.container.dataset.id_channel+'/messages';



        // отправим мессагу на сервер...
        //console.log('POST мессага');
        myApp.showProgressbar();  
        $$.ajax(
        {
            method: 'POST', 
            url: API_URL+url,//'channels/'+page.container.dataset.id_channel+'/messages',
            data: JSON.stringify({"message": messageText}),
            success: function(response)
            {

                // ...если успешно — добавим ее на экран...
                myMessagebar.clear();
                $$('.messagebar a.send-message').removeClass('disabled');

                myMessages.appendMessage({
                    text: messageText,
                    type: 'sent',
                    ts: (moment.now()/1000-1).toFixed()
                },animateChats);
            
                tick.play();
                doMoment();
                myApp.hideProgressbar();    

                
                // ...и положим ее же в локальную базу
                // писать в базу не дожидаясь ответа от сервера не получится - неизвестно id_line
                data = JSON.parse(response);
                //console.log('отправка',data);                
                db.transaction(function (tx) 
                {
                    tx.executeSql("INSERT OR REPLACE INTO messages (id, id_channel, id_from, id_to, text, ts, name, gender, unread) values(?,?,?,?,?,?,?,?,?);",[data[0].id, data[0].id_channel, data[0].id_from, (data[0].id_to ? data[0].id_to : 0), data[0].text,data[0].ts, myApp.formGetData('profile').name, myApp.formGetData('profile').gender, 0]);
                });

            }, error: function(response)
            {
                myApp.hideProgressbar();
                myApp.addNotification({
                    title: 'Кажется, нет интернета...',
                    text: 'Давай попробуем позже.',                    
                    hold: 4000
                });
                console.log('addMessage error', response);
                // если неуспешно, надо чото делать
                if (response.status == 404)
                {
                    myApp.alert('Канал не существует. Нажми "Выход" и зайди снова для обновления списка каналов.');
                }
            }
        });        
    });
    $$('.page').on('scroll', function(){checkRead();} ,true);
    
    
    //! скролл чата            
    $$('.messages-content.infinite-scroll').on('infinite', function () 
    {

        myApp.detachInfiniteScroll($$('.messages-content.infinite-scroll'));
        $$('.preloader-top').show();
        //console.log($$('.messages .message:first-child').dataset().messageId);
        if ($$('.messages .message:first-child').dataset())
          fillMessages(page.container.dataset.id_channel, $$('.messages .message:first-child').dataset().messageId, page.container.dataset.id_from, itemsPerLoad);    
    });    
            
 
    fillMessages(page.container.dataset.id_channel, 0, page.container.dataset.id_from, 20); // начальное заполнение чата
    setTimeout(function(){ myMessages.scrollMessages() }, 100);
    
    $$('a.open-channelinfo').on('click', function()
    {
        getChannelInfo(page.container.dataset.id_channel);
    });

});




//! инфо о канале
function getChannelInfo(id_channel)
{
    myApp.showPreloader();    
    var channelInfo = new Promise(function(resolve, reject) 
    {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', API_URL+'channels/'+id_channel+'/info');
        xhr.setRequestHeader('x-access-token', myApp.ls.getItem('token'));
        
        xhr.onload = function() {
          if (this.status == 200) {
            //console.log('channelInfo загружено, делаем resolve');
            resolve(this.response);
          } else {
            var error = new Error(this.statusText);
            error.code = this.status;
            reject(error);
          }
        };
        
        xhr.onerror = function() {
          reject(new Error("Network Error"));
          myApp.hidePreloader();
          myApp.alert('Кажется нет интернета... Давай попробуем позднее.');
          return;
        };
        myApp.mainView.loadPage('channel_info.html');

        xhr.send();
    });



    channelInfo.then(function(response)
    {
        var response = JSON.parse(response);
        //console.log(response);


        var channelname = response.name;
        var channelcount = response.count + ' участников';

        if (response.name) 
            $$('.channelname').text(channelname);
    
        $$('.channelcount').text(channelcount);
        
        var members = response.members;
        var html = Template7.templates.channelmembers({'members' : members});
        $$('#membersResults').html('').append(html); 

        if ( response.owner && parseInt(response.owner) == parseInt(myIdUser) )
        {
            $$('.formoder').removeClass('hide');
        }
        else
        {
            $$('#membersResults .swipeout-actions-right').detach();
        }
        
        myApp.hidePreloader();
    });
}
    
    


// Возвращает массив сообщений
// Сперва пытается получить из базы, если там ничего нет - лезет на сервер и одновременно пишет в базу
// Не сохраняет сообщения в каналах, на которые нет подписки
// аргументы channel - id канала, lastid - id последнего известного сообщения, itemsPerload - сколько нужно получить
function fillMessages(channel, lastid, id_from, itemsPerLoad) 
{
    var promise = new Promise(function(resolve, reject) 
    {
        var messages = [];
        // достаем из локальной базы itemsPerLoad мессаг
        db.readTransaction(function (tx) 
        {
            var order = 'DESC';// (lastid == 0 ? 'DESC' : 'ASC'); 

            var dbfilter = (lastid == 0 ? '' : 'and id < '+lastid);
            var url = '';

            if (channel == 0) 
            {
                url = 'users/'+id_from+'/messages';
                //dbfilter = dbfilter + ' and (id_from='+id_from;
                dbfilter = dbfilter + ' and ((id_from = '+id_from+' and id_to='+myIdUser+') or (id_to ='+id_from+' and id_from='+myIdUser+'))';
            }
            else url = 'channels/'+channel+'/messages';

            var query = 'SELECT * from ( SELECT id, id_from, gender, name, text, ts, COALESCE(unread,0) as unread FROM messages WHERE id_channel = '+channel+' '+dbfilter+' order by ts DESC LIMIT 0,'+itemsPerLoad+') t1 ORDER BY t1.ts '+order; 
            
            tx.executeSql(query,[], function(tx, result) 
            {
                for (var i = 0; i < result.rows.length; i++) 
                {
                    messages.push(result.rows.item(i));
                }
                //console.log('messages из DB: '+messages.length);
                
                // если в базе больше ничего нет, идем на севрер
//                if (result.rows.length < 20) 
                {
                    resolve( new Promise(function(resolve, reject) 
                    {
                        //console.log('try to reach server...');

                        var filter = (lastid == 0 ? '?' : '?filter=id less '+lastid+'&');
                        $$.ajax(
                        {
                            method: 'GET', 
                            url: API_URL+url+filter+'results='+itemsPerLoad+'&order='+order,
                            success: function(response)
                            {
                                try {
                                    messages = JSON.parse(response);
                                    } catch(e) {
                                        console.log(e);
                                    }
                                //console.log('messages с сервера: '+messages.length);
                                writeMessages(JSON.parse(response),channel);                    
                                resolve(messages);
                            },
                            error: function(response)
                            {
                                console.log('Сервер упал? Канал удален? ');
                                resolve(messages);
                            }
                            
                        });
                    }));        
                }
                //else
                  //  resolve(messages); // resolve нельзя объединить в один потому что ajax асинхронный
            });
        });
    });




    // получили мессаги, формируем и выводим
    promise.then(function(messages) 
    {
        $$.each(messages, function(i) 
        {
            var message = {};
            message.id = messages[i].id;
            message.id_from = messages[i].id_from;
            message.gender = messages[i].gender;
            message.gender = messages[i].gender;            
            if (parseInt(messages[i].id_from) == parseInt(myIdUser))
            {
                message.type = 'sent';
            }
            else 
            {
                message.type = 'received';
                message.name = messages[i].name;
                message.avatar = '//app.motohelplist.com/img/avatar/'+messages[i].id_from+'.jpg';                
            }
            //message.label = messages[i].id;     // текст под принятыми
            //message.position = messages[i].id;  // текст под отправленными          
            try 
            {
                message.text = (linkify(messages[i].text)).replace(/\n/g,'<br>');
            }
            catch (err) 
            {
                console.error('похоже юзер написал какую-то хуйню и linkify не может ее обработать', err.message);        
                console.log('хуйня была такая: ',messages[i].text, messages[i].text.length);
            }
            message.ts = messages[i].ts;
            message.unread = messages[i].unread;

            myMessages.prependMessage(message, false);
        }); 

        if (messages.length > 0)
            myApp.attachInfiniteScroll($$('.infinite-scroll'));
        else 
            $$('.preloader-top').hide();    
        
        checkRead();
        doMoment();
        //myApp.hidePreloader();        



        // определим, коснулся ли юзер экрана
        // если не касался или уже отпустил, разрешаем автоскролл чата вниз
        document.body.addEventListener('touchstart', function(event) 
        {
            allowScrollMessages = 0;
            //console.log('касание экрана',allowScrollMessages);            
        }, false);
        document.body.addEventListener('touchend', function(event) 
        {
            allowScrollMessages = 1;
            //console.log('касание прервано',allowScrollMessages);            
        }, false);
        
        // определим, пошел ли юзер за старыми мессагами
        if ( (($$('#bottomPageMark').offset().top) - window.innerHeight) < window.innerHeight )
            allowScrollMessages = 1;
        else
            allowScrollMessages = 0;
        
        // если автоскролл разрешен И юзер не пошел за старыми мессагами, автоскроллим чат вниз
        if (allowScrollMessages == 1)
            setTimeout(function(){ myMessages.scrollMessages() },100); 
            
        // если чат создан с координатами            
        $$('.linkmap').on('click', function(e)
        {
            console.log('e',e.target.dataset.lat, e.target.dataset.lng);
            console.log('грузим координаты '+'map.html?lat='+e.target.dataset.lat+'&lng='+e.target.dataset.lng);
            myApp.mainView.loadPage('map.html?lat='+e.target.dataset.lat+'&lng='+e.target.dataset.lng);
        });

    });
}






/*
/*  пишет мессаги в базу
*/    
function writeMessages(data,channel) 
{
    $$.each(data, function(i) 
    {
        db.transaction(function (tx) 
        {
           if (channel) data[i].id_channel = channel;
           
           tx.executeSql("INSERT OR REPLACE INTO messages (id, id_channel, id_from, id_to, text, ts, name, gender, unread) values(?,?,?,?,?,?,?,?,?);", [data[i].id, data[i].id_channel, data[i].id_from, (data[i].id_to ? data[i].id_to : 0), data[i].text,data[i].ts, data[i].name, data[i].gender, data[i].unread],
                 function(tx, results) {
                    //console.log('writeMessages rowsAffected ',results.rowsAffected);
                }, function (tx, error) {
                console.log('writeMessages Error ',error,query);
                });
        });
    });
}



/*  
/*  загружает содержимое чата в шаблон
*/
function getChannelMessages(id_channel, channel_name, id_from, type_channel) 
{
    var newPageContent = '<div class="navbar"><div class="navbar-inner"><div class="left"><a href="#" class="back link icon-only"><i class="icon icon-back"></i></a></div><div class="center sliding">'+channel_name+'</div><div class="right">';
   
    if (id_channel > 0 && type_channel == 3) // загрузим иконку channel_info только если канал type3 'PRIVATE'
    {
        newPageContent = newPageContent + '<a href="#" class="open-channelinfo"><i class="icon icon-settings"></i></a>';
    }
   
    newPageContent = newPageContent +  '<a href="#" class="link icon-only open-panel"><i class="icon icon-bars"></i></a></div></div></div>' +
    '<div class="pages"><div class="page" data-page="messages" data-id_channel="'+id_channel+'" data-id_from="'+id_from+'">' +
        '<div class="toolbar messagebar">'+
            '<div class="toolbar-inner"><i class="icon icon-camera"></i>'+
            '<span class="fileinput-button" data-role="button" data-icon="plus"><input id="chat-attach" type="file" accept="image/*" id="capture"></span>'+
                '<textarea placeholder="Ответ"></textarea><a href="#" class="link send-message">Send</a>'+
            '</div>' +
        '</div>'+
        '<div class="page-content hide-bars-on-scroll messages-content infinite-scroll infinite-scroll-top"><div class="preloader-top"><div class="preloader"></div></div>' + 
            '<div class="messagesHolder" data-distance="50"><div class="messages"></div></div><span id="bottomPageMark"></span>'+
        '</div>' +
    '</div>'; 
                        
    mainView.router.loadContent(newPageContent);
}           



/*
/*  устанавивает метку "прочитано"
*/
function checkRead() 
{
    if (scrollTimeout > 0) 
        return;
    
    scrollTimeout = 1;

    setTimeout(function()
    {
        unread = $$('.message.unread');
        $$.each(unread, function(i) 
        {
            if ($$(unread[i]).offset().top - window.innerHeight < -100)
            {
                $$(unread[i]).removeClass('unread');
                var messageId = $$(unread[i]).dataset().messageId;
                var pageData = $$(myApp.getCurrentView().activePage.container).dataset();
                
                if (pageData.id_channel==0)
                    var idBadge =  '#UnreadMsg-'+pageData.id_from;
                else 
                    var idBadge = '#UnreadMsg-'+pageData.id_channel;
                
                var unreadTotal = parseInt($$(idBadge))-1;
                
                (unreadTotal <= 0 ?  $$(idBadge).text(unreadTotal) : $$(idBadge).hide());

                $$.ajax(
                {
                    method: 'PUT', 
                    url: API_URL+'messages/'+messageId+'/read',
                    success: function(response)
                    {
                        //console.log('message read to server',messageId);
                    }
                });
                
                db.transaction(function (tx) 
                {
                   var query = "UPDATE messages set unread =0 where id = "+$$(unread[i]).dataset().messageId;
                   tx.executeSql(query,[],
                         function(tx, results) {
                            //console.log('message read to DB',results.rowsAffected);
                        }, function (tx, error) {
                        console.log('writeMessages Error ',error,query);
                        });
                });
            }
        });
//            clearTimeout(scrollTimeout);
        scrollTimeout = 0;
    }, 2000)
}



// convert base64/URLEncoded data component to raw binary data held in a string
function dataURItoBlob(dataURI) 
{
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ia], {type:mimeString});
}