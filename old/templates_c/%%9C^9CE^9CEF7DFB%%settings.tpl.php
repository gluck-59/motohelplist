<?php /* Smarty version 2.6.20, created on 2016-04-17 17:05:16
         compiled from settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'settings.tpl', 31, false),)), $this); ?>
<br>
<div class="row>">
    <!--div class="small-6 columns">
        Language
        <br>
        <small class="grey_text">Currently is autoselect only</small>
    </div>
    <div class="small-6 columns">
        <select disabled><option selected="1">Russian</option></select>
    </div>

    <hr><br-->

    <div class="small-9 columns">
        Анимация интерфейса
        <br>
        <small class="grey_text">Отключайте на слабых телефонах</small>
    </div>
    <div class="small-3 columns">
        <div class="switch round right">
            <input id="animation" value="" type="checkbox" onchange="animation()">
            <label for="animation"></label>
        </div>
    </div>

    <hr><br>

    <div class="small-9 columns">
        Относительное время
        <br>        
        <small class="grey_text">"Сегодня в <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M") : smarty_modifier_date_format($_tmp, "%H:%M")); ?>
" или "5 минут назад"</small>        
    </div>
    <div class="small-3 columns">
        <div class="switch round right">
            <input id="relativeTime" value="" type="checkbox" onchange="useRelativeTime()">
            <label for="relativeTime"></label>
        </div>
    </div>


    <hr><br>

    <div class="small-9 columns">
        В Эфире — только SOS
        <br>        
        <small class="grey_text">Показывать только запросы о помощи</small>        
    </div>
    <div class="small-3 columns">
        <div class="switch round right">
            <input id="sosAirOnly" value="" type="checkbox" onchange="sosaironly()">
            <label for="sosAirOnly"></label>
        </div>
    </div>


    <hr><br>

    <div class="small-9 columns">
        Адреса в метках карты
        <br>        
        <small class="grey_text">Подтормаживает при плохой связи</small>        
    </div>
    <div class="small-3 columns">
        <div class="switch round right">
            <input id="geocodeAddr" value="" type="checkbox" onchange="showGeocodeAddr()">
            <label for="geocodeAddr"></label>
        </div>
    </div>
    

    <hr><br>

    <div class="small-9 columns">
        Объединять метки карты
        <br>        
        <small class="grey_text">Иначе тормозит когда много меток</small>
    </div>
    <div class="small-3 columns">
        <div class="switch round right">
            <input id="minimumClusterSize" value="" type="checkbox" onchange="setMinimumClusterSize()">
            <label for="minimumClusterSize"></label>
        </div>
    </div>


    <hr><br>

    <div class="small-9 columns">
        Показывать "Пробки"
        <br>        
        <small class="grey_text">Состояние дорожного трафика</small>
    </div>
    <div class="small-3 columns">
        <div class="switch round right">
            <input id="showTrafficLayer" value="" type="checkbox" onchange="trafficLayer()">
            <label for="showTrafficLayer"></label>
        </div>
    </div>    
        

    <hr><br>

    <div class="small-10 columns">
        Начальный масштаб карты
        <br>        
        <small class="grey_text">От дома до континента</small>        
    </div>
    <div class="small-2 columns">
        <div class="">
            <!--input id="initZoom" type="number" min="3" max="21" step="1" value="1" onchange="initZoom()">
            <label for="initZoom"></label-->
            <select id="initZoom" onchange="initZoom()">
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>                
                <option value="7">7</option>                
                <option value="8">8</option>                
                <option value="9">9</option>                
                <option value="10">10</option>                
                <option value="11">11</option>                
                <option value="12">12</option>                
                <option value="13">13</option>                
                <option value="14">14</option>                
                <option value="15">15</option>                
                <option value="16">16</option>                
                <option value="17">17</option>                
                <option value="18">18</option>                
                <option value="19">19</option>                
                <option value="20">20</option>                
                <option value="21">21</option>                
            </select>
        </div>
    </div>

    <hr><br>

    <div class="small-9 columns">
        Обновить хелплист
        <br>        
        <small class="grey_text" id="updateHelplistDate"></small>        
    </div>
    <div class="small-3 columns">
        <div class="right">
            <input type="button" class="tiny radius button" onclick="updateHelplistManually()" value="Update">
        </div>
    </div>


</div>

<?php echo '
<script>
$(window).load(function()
{
    (parseInt(localStorage.getItem(\'animation\')) > 0 ? $(\'input#animation\').prop("checked", "checked") : \'\');
    (parseInt(localStorage.getItem(\'sosAirOnly\')) == 1 ? $(\'input#sosAirOnly\').prop("checked", "checked") : \'\');    
    (parseInt(localStorage.getItem(\'geocodeAddr\')) == 1 ? $(\'input#geocodeAddr\').prop("checked", "checked") : \'\');        
    (parseInt(localStorage.getItem(\'relativeTime\')) == 1 ? $(\'input#relativeTime\').prop("checked", "checked") : \'\');            
    $(\'select#initZoom\').val(parseInt(localStorage.getItem(\'initZoom\')));
    (parseInt(localStorage.getItem(\'minimumClusterSize\')) == 2 ? $(\'input#minimumClusterSize\').prop("checked", "checked") : \'\');                
    (parseInt(localStorage.getItem(\'showTrafficLayer\')) == 1 ? $(\'input#showTrafficLayer\').prop("checked", "checked") : \'\');                

if (relativeTime) $(\'#updateHelplistDate\').text(\'Обновлен \'+moment(localStorage.getItem(\'helplist_updated\'), "YYYY-MM-DD H:mm:ss.Z").fromNow());
else $(\'#updateHelplistDate\').text(\'Обновлен \'+moment(localStorage.getItem(\'helplist_updated\'), "YYYY-MM-DD H:mm:ss.Z").calendar());
    
});

    $(\'h1\').text(\'Настройки\');

    function trafficLayer()
    {
        if ($(\'input#showTrafficLayer\').prop("checked"))
            localStorage.setItem(\'showTrafficLayer\', 1);
        else
            localStorage.setItem(\'showTrafficLayer\', 0);
        ohSnap(\'Готово\');        
    }


    function setMinimumClusterSize()
    {
        if ($(\'input#minimumClusterSize\').prop("checked"))
            localStorage.setItem(\'minimumClusterSize\', 2);
        else
            localStorage.setItem(\'minimumClusterSize\', 1000);
        ohSnap(\'Готово\');        
    }
    
    function animation()
    {
        if ($(\'input#animation\').prop("checked"))
            localStorage.setItem(\'animation\', 300);
        else
            localStorage.setItem(\'animation\', 0);            
        ohSnap(\'Готово\');
    }



    function sosaironly()
    {
        if ($(\'input#sosAirOnly\').prop("checked"))
        {
            localStorage.setItem(\'sosAirOnly\', 1);
            $(\'a#air_link\').prop(\'href\', \'onair.php?sosAirOnly=1&\');
        }
        else
        {
            localStorage.setItem(\'sosAirOnly\', 0);            
            $(\'a#air_link\').prop(\'href\', \'onair.php\');
        }
        ohSnap(\'Готово\');
    }


    function showGeocodeAddr()
    {
        if ($(\'input#geocodeAddr\').prop("checked"))
            localStorage.setItem(\'geocodeAddr\', 1);
        else
            localStorage.setItem(\'geocodeAddr\', 0);
        ohSnap(\'Готово\');
    }

    
    function initZoom()
    {
        var initZoom = $(\'select#initZoom\').val();
        localStorage.setItem(\'initZoom\', initZoom);
        ohSnapX();
        ohSnap(\'Готово\');        
    }
    
    
    function useRelativeTime()
    {
        if ($(\'input#relativeTime\').prop("checked"))
        {
            localStorage.setItem(\'relativeTime\', 1);
$(\'#updateHelplistDate\').text(\'Обновлен \'+moment(localStorage.getItem(\'helplist_updated\'), "YYYY-MM-DD H:mm:ss.Z").fromNow());
        }
        else
        {
            localStorage.setItem(\'relativeTime\', 0);
$(\'#updateHelplistDate\').text(\'Обновлен \'+moment(localStorage.getItem(\'helplist_updated\'), "YYYY-MM-DD H:mm:ss.Z").calendar());            
        }
        ohSnap(\'Готово\');
    }    
    
    

</script>
'; ?>
        