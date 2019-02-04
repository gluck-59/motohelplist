<!doctype html>
<html lang="ru" manifest="manifest.appcache.php">
<!--html lang="ru"-->
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui, user-scalable=no"/>
    
    <meta name="apple-mobile-web-app-capable" content="yes">    {* fullscreen для iOS *}
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="img/logo_152.png">
    <link rel="apple-touch-icon" href="img/logo_1024.png">     

        
    <meta name="mobile-web-app-capable" content="yes">          {* fullscreen для Android *}
    <!-- for Chrome on Android, multi-resolution icon of 196x196 -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="shortcut icon" sizes="196x196" href="img/logo_196.png">
    <!--link rel="icon" sizes="196x196" href="img/logo_196.png"-->    
    <link rel="icon" sizes="1024x1024" href="img/logo_1024.png"> 
    
    
    <title>Moto Helplist</title>

    <link rel="stylesheet" href="sass/templates.css"/>
    <!--link rel="shortcut icon" type="image/x-icon" href="favicon.ico"-->    
    <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico" /> 
    
    <script src="js/jquery.min.js"></script>
    <script src="js/moment.min.js"></script>        
    <script src="js/foundation.min.js"></script>
    <script src="js/foundation.offcanvas.js"></script>    
    <script src="js/cookie.js"></script>        



{*
    
// вычисляет ссылки и картинки в постах юзера
// формирует для них html-окружение
// вызывается везде, где есть юзер текст
linkify

*}
{literal}
    <script>
        $('#loader').show();
        var animation = parseInt(localStorage.getItem('animation'));
        

function linkify(inputText) 
{
    if (!inputText) return false;
    var patternhref = /(href=)/gi;
    if (patternhref.test(inputText)) return inputText;

    var patternhttp = /([-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\b(\/?[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?)/gi;
    var patterngps = /\[([-0-9].+)\,([-0-9].+)\]/g;
    
    var pattern = /(http:\/\/|https:\/\/)|([a-zA-Z0-9-\_]+\.[a-zA-Z]+|\.[a-zA-Z]+)/i;
    var parts, ext = ( parts = inputText.split("/").pop().split(".") ).length > 1 ? parts.pop() : "";
    var pics = /jpg|jpeg|gif|png/ig;

    if (pics.test(ext))
    {
        return inputText.replace(patternhttp, '<div class="posted_pic"><a href="$1" target="_blank"><img src="$1"></a></div>');
    }
    else if (pattern.test(inputText))
    {
        return inputText.replace(patternhttp, '<a href="$1" target="_blank">$1</a>');    
    }
    else if (patterngps.test(inputText)) {
        return inputText.replace(patterngps, '<br><a href="map.php?lat=$1&lng=$2" role="external">GPS-координаты</a>');
    }
    else
    return inputText;
}


    </script>
{/literal}    
</head>
<body class="large-4">
    <div id="loader"><img src="img/loader.gif"></div>
    <div class="off-canvas-wrap" data-offcanvas>
        <div class="inner-wrap">
            <div id="offcanvas_fixer" class="small-12 large-4">
                <nav class="tab-bar">
                    <section class="right-small">
                        <!--a class="left-off-canvas-toggle menu-icon" href="#"><span></span></a-->
                        <a class="hide-for-large-up left-off-canvas-toggle menu-icon" href="#"><span></span></a>                    
                    </section>
                    <section class="left-small">
                        <a onclick="history.back();" class=""><p class="back_arrow">&#8249;</p></a>
                    </section>
                    <section class="tab-bar-section middle">
                        <h1 class="title"></h1>
                    </section>
                </nav>
            </div>        
            <aside class="left-off-canvas-menu">
                <ul class="off-canvas-list">
                    <li><label>Меню</label></li> 
                    <li class="has-submenu"><a href="#">Статус</a>
                        <ul class="left-submenu">
                            <li class="back"><a href="#">Назад</a></li>
                            <li class="" style="list-style-type: none; padding: 1em 0.3rem">
                                <textarea name="status" id="currentstatus" rows="3"></textarea>
                                <button class="tiny success round expand button" onclick="setStatus()">Обновить</button>
                            </li>
                        </ul>
                    </li>                    
                    <li class="has-submenu"><a class="private_link" href="#">Личка<div class="hide unread_counter" id="private-all"></div></a>
                        <ul class="left-submenu">
                            <li class="back"><a href="#">Назад</a></li>
                                {if $chats}
                                    {foreach from=$chats item=chat}
                                        <li class="private_chat_user"><a class="private_link" href="chat.php?channel=0&id_to={$chat->id}&" role="external" onclick="$('div#private-{$chat->id}').hide();return false;" >{$chat->name}<div class="hide unread_counter" id="private-{$chat->id}"></div></a></li>
                                    {/foreach}
                                {else}
                                    <li><label class="empty">Тебе еще никто не написал. <br>Почему бы не сделать это первым?</label></li>
                                {/if}
                        </ul>
                    </li>                    
                    <li class=""><a href="help.php" role="external">Помощь</a></li>                    
                    <li class=""><a class="air_link" id="air_link" href="onair.php" role="external">Эфир<div class="hide unread_counter" id="air-all"></div></a></li>
                        <li class="" id="add_to_channel"></li>

                    <li class=""><a href="map.php" role="external">Карта</a></li>
                        <li class="has-submenu" id="add_to_map"></li>                    

                    <li class=""><a href="feed.php" role="external">Новости</a></li>                    

                    <li class=""><a href="subscribe.php" role="external">Подписки</a></li>
                    <li class=""><a href="helplist.html?online" role="external">Хелп-лист</a></li>                    
                    <li class=""><a href="friends.php" role="external">Друзья</a></li>          
                    
                    <li class=""><a href="trip.php" role="external">В путь!</a></li>
                    <li class=""><a href="profile.php" role="external">Профиль</a></li>
                                        
                    <li class=""><a href="donate.php" role="external">Поддержи проект</a></li>                                        
                    <li class=""><a href="bugreport.php" role="external">Прием багов</a></li>
                    <li class=""><a href="settings.php" role="external">Настройки</a></li>                    
                    <li class=""><a href="index.php?logout=1&" role="external" onclick="localStorage.removeItem('sessionid');">Выход</a></li>
                    
                    <!--hr>          
                    <hr>                  
                    <li class=""><a href="#">События (CalDAV)</a></li>            
                    <li class=""><a href="#">Первая медпомощь</a></li>
                    <li class=""><a href="#">Маршруты</a></li>
                    <li class=""><a href="#">SOS!</a></li-->

                </ul>
            </aside>
            <!------- content ---------->
            <div id="content">