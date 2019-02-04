<?php /* Smarty version 2.6.20, created on 2016-04-21 18:38:42
         compiled from trip.tpl */ ?>
<div class="row" role="triphint">
    <h4>Я на карте мира</h4>
    <p class="grey_text">Включи, если собрался в дорогу. Друзья смогут видеть где ты едешь сейчас.</p> 
</div>

<div class="row">
    <h4 class="text-center">Поехали</h4>
    <p id="trip_instruction" class="grey_text hide">Если геолокация включена, твоя метка появится на карте.</p>    
    <div class="switch round small-12 text-center">
      <input id="trip" name="trip" type="checkbox" onchange="checktrip()">
        <label for="trip"></label>
    </div>
</div>
<div class="row" role="triphint">
    <p class="orange_text">Для экономии заряда батареи твоя геопозиция будет сохраняться только когда телефон включен.</p> 
    <p class="orange_text">На остановках запускай приложение на минутку-две.</p>
</div>


<div class="row1 hide" id="tripdiv">
    <!--div class="small-12">
        <p>Ссылка для копирования</p>
        <input readonly type="text" id="triplink">
    </div-->
    <br>    
    <div id="map"></div>
    <br>
    <p class="text-center"><a href="map.php" role="external">Развернуть на весь экран</a></p>
</div>
    
    
    
<?php echo '
<script>
$(window).load(function()
{
    $(\'h1\').text(\'В путь!\');
initialise(); // google maps    
console.log(\'initialise from trip.tpl\');
    var id_trip = parseInt(localStorage.getItem(\'id_trip\'));
    if (id_trip && id_trip != 0)
    {
        $(\'input#trip\').prop("checked", "checked");
    }
    checktrip();



});

function checktrip() 
{
    if ($(\'input#trip\').prop("checked"))
    {
        localStorage.setItem(\'id_trip\', Date.now());
        $(\'div#tripdiv\').show(animation);
        $(\'div[role=triphint]\').hide(animation);
        $(\'p#trip_instruction\').show(animation);
        id_trip = parseInt(localStorage.getItem(\'id_trip\'));
        prepareGeolocation();
        doGeolocation(); 
    }
    else
    {
        localStorage.setItem(\'id_trip\', 0);        
        $(\'div#tripdiv\').hide();
        $(\'div[role=triphint]\').show(animation);   
        $(\'p#trip_instruction\').hide(animation);        

openDb();
        if (db)
        {
            db.transaction(function (tx) {
            tx.executeSql(\'delete FROM trips\',[], function(tx, result) { 
                }, null);});             
        }
        
        $.post(
            "trip.php",
            {
                task: \'deleteTrip\'
            },
            onAjaxSuccess
        );
        function onAjaxSuccess(data)
        {
            // должно вернуться 1
            if (data == 0)
                ohSnap(\'Не удалось удалить координаты с сервера. Включи и выключи "В путь" еще раз.\');
        }

    }

}
        

    
</script>    
'; ?>
    