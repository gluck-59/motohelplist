{if !$error}
    <div class="hide" id="append_menu_newchannel">
        <a href="add_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Новый канал</a>
    </div>
    <div class="hide" id="append_menu_findchannel">
        <a href="search_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Найти канал</a>
    </div>    
    
    
    {* добавление канала *}
    <form id="add_channel_form" action="onair.php" method="post" class="small-12"  onsubmit="add_channel_formsubmit(); return false;">
        <h4>Новый чат-канал</h4>            
        <p class="grey_text">Создай общий чат-канал с доступом для всех.
        Отметь, если случились неприятности.</p>
        <div class="row">
            <div class="large-4 small-4 columns">
                <span class="prefix">Название</span>
            </div>
            <div class="large-8 small-8 columns">
                <input required type="text" name="name_channel" placeholder="дай имя своему каналу">
            </div>
        </div>
        
        <div class="row" id="type_channel_row">
            <div class="switch round small-4 columns text-center">
              <div class="secondary radius label">Поболтать</div><br><br>
              <input id="exampleRadioSwitch1" type="radio" checked name="urgency" value="0">
              <label for="exampleRadioSwitch1"></label>
            </div> 
        
            <div class="switch round small-4 columns text-center">
              <div class="warning radius label">Сломался</div><br><br>
              <input id="exampleRadioSwitch2" type="radio" name="urgency" value="1">
              <label for="exampleRadioSwitch2"></label>
            </div> 
        
            <div class="switch round small-4 columns text-center">
              <div class="error radius label">ДТП</div><br><br>
              <input id="exampleRadioSwitch3" type="radio" name="urgency" value="2">
              <label for="exampleRadioSwitch3"></label>
            </div>
        </div>            
        <div class="row">    
            <div class="small-12 text-center" id="">
              <input id="allowgps" class="custom_checkbox" type="checkbox" name="allowgps" value="1">
              <label for="allowgps">Опубликовать GPS-координаты</label>
              <p id="delimiter">&nbsp;</p>
            </div>
            <div id="map" style="width: 0; height: 0;"></div>            

            <input type="submit" class="small expand success round button" id="create_channel" value="Создать общий канал">
            <p class="grey_text">Или пригласи друзей в приватный канал — только для вас.</p>
            <input type="hidden" name="task" value="add_channel">
            <input type="hidden" name="type_channel" value="group">
            <input type="hidden" name="invitefriends" value="" placeholder="invitefriends">
            <input type="hidden" name="lat" value="" placeholder="lat">
            <input type="hidden" name="lng" value="" placeholder="lng">
        </div>
    </form>
{else}
    <pre>{$error|@print_r}</pre>
{/if}    



{literal}
<script>
    $('h1').text('Создание чат-канала');
    $('li#add_to_channel').html($('div#append_menu_newchannel').html()+$('div#append_menu_findchannel').html() );
    
// если отмечено "Опубликовать GPS-координаты" - включаем гео
allowgps.addEventListener("change", function()
{
    if ($('#allowgps').prop("checked") === true)
    {
//initialise(); // google maps
        setTimeout(function()
            {
                $('input[name=lat]').val(map.center.lat());
                $('input[name=lng]').val(map.center.lng());
                if ( map.center.lat() == parseFloat(localStorage.getItem('initLat')) && map.center.lng() == parseFloat(localStorage.getItem('initLng')) )
                {
                    $('p#delimiter').html('<div id="status">Невозможно определить координаты. Разреши отправку геопозиции в настройках и зайди сюда еще раз.</div>');
                    $('#allowgps').prop("checked", false);
                }
            }, 1000);
    }
});

    

//console.log('animation '+animation);

    $('div#loader').show();            

    $.post(
        "friends.php",
        {
            addFriendsToChannel: getCookie('id_user')
        },
        onAjaxSuccess
    );
    function onAjaxSuccess(data)
    {
        $('div#loader').hide();
        $('form#add_channel_form').append(data);
    }

	
	
	

// устанавливает тип канала 
function checktype()
{
    // если хоть один френд отмечен — это private канал
    if ($('input[role=invitefriend]:checked').length > 0)
    {
        $('input[name=type_channel]').val('private');
        $('div#type_channel_row').hide(animation);
        $('input#create_channel').val('Создать приватный канал');
    }
    else
    {
        $('input[name=type_channel]').val('group');
        $('div#type_channel_row').show(animation);
        $('input#create_channel').val('Создать общий канал');
    }

    // соберем всех отмеченных в строку
    var invitefriend = '';
    var array = $('input[role=invitefriend]:checked');
    var i = 1;
    $('input[role=invitefriend]:checked').each(function()
    {
        invitefriend = invitefriend+this.value;
        if (i < array.length) invitefriend = invitefriend+',';
        i++;
    });
    $('input[name=invitefriends]').val(invitefriend);
}
	
	
function add_channel_formsubmit()
{
    var val = $('input[name=name_channel]').val();
    if ( val.length == 0)
    {
        ohSnap('Назови как-нибудь свой канал');
        $('input[name=name_channel]').css('background-color','#dfd');
    }
    
    else
    {
        $('div#loader').show();        
        document.getElementById('add_channel_form').submit();
    }
}
	
</script>    
{/literal}

    <script src="https://maps.google.com/maps/api/js"></script>
    <script type="text/javascript" src="js/gmaps.min.js"></script>    
    <script type="text/javascript" src="js/geometa.min.js"></script>        

