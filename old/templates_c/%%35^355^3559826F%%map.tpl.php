<?php /* Smarty version 2.6.20, created on 2016-05-11 01:40:17
         compiled from map.tpl */ ?>
<div class="hide" id="append_menu">
    <a href="#">&nbsp;&nbsp;+&nbsp;Добавить</a>
    <ul class="left-submenu">
        <li class="back"><a href="#">Назад</a></li>
        <li class=""><a href="" onclick="add_poi('place');return false">Место тусовки</a></li>
        <li class=""><a href="" onclick="add_poi('hotel');return false">Отель</a></li>
        <li class=""><a href="" onclick="add_poi('parking');return false">Мото-паркинг</a></li>        
        <li class=""><a href="" onclick="add_poi('service');return false">Мото-сервис</a></li>                
        <li class=""><a href="" onclick="add_poi('tireservice');return false">Шиномонтаж</a></li>                        
        <li><label class="empty">Добавь новое место на карту<br><br>Проверь данные перед добавлением</label></li>
    </ul>    
</div>


<div class="hide small-12 large-7" id="add_hotel">
    <form name="hotel" id="hotel" action="//app.motohelplist.com/old/map.php" method="get" onsubmit="formsubmit('hotel'); return false"> 
        <div class="row">
            <div class="large-4 small-4 columns">
                <span class="prefix">Координаты</span>
            </div>
            <div class="large-8 small-8 columns">
                <input type="text" name="latlng" placeholder="перемести указатель на карте" onclick="add_poi('hotel')">
            </div>
            <div class="large-4 small-4 columns">
                <span class="prefix">Название</span>
            </div>
            <div class="large-8 small-8 columns">
                <input required type="text" name="name" placeholder="обязательно">
            </div>
            <div class="large-4 small-4 columns">
                <span class="prefix">Цена</span>
            </div>
            <div class="large-8 small-8 columns">        
                <input type="text" name="price" placeholder="за номер в сутки: от-до">
            </div>        
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone1" placeholder="с кодом страны">
            </div>        
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone2" placeholder="…еще телефон?">
            </div>        

            <div class="tablediv2 columns">        
                <textarea rows="4" class="bottom-textarea" name="description" placeholder="Подробнее (255 знаков макс., необязательно)"></textarea>
            </div>
        </div>
        <div class="row">
           <div class="text-center ">
                <div class="small-6 columns">
                    <div class="switch round">
                      <p class="text-center">Внутр. паркинг</p>
                      <input id="parking" name="parking" value="1" type="checkbox">
                        <label for="parking">
                      </label>
                    </div>
                </div>
            </div>
            <div class="text-center ">
                <div class="small-6 columns">                            
                    <div class="switch round">
                      <p class="text-center">Wi-Fi</p>
                      <input id="wifi" name="wifi" value="1" type="checkbox">
                        <label for="wifi">
                      </label>
                    </div>
                </div>
            </div>
            <div class="text-center ">
                <div class="small-6 columns">
                    <div class="switch round">
                      <p class="text-center">Кондиционер</p>
                      <input id="ac" name="ac" value="1" type="checkbox">
                        <label for="ac">
                      </label>
                    </div>
                </div>
            </div>
            <div class="text-center ">
                <div class="small-6 columns">                            
                    <div class="switch round">
                      <p class="text-center">Баня / сауна</p>
                      <input id="sauna" name="sauna" value="1" type="checkbox">
                        <label for="sauna">
                      </label>
                    </div>
                </div>
            </div>
            <div class="small-4 columns">
                <input type="reset" value="Отмена" class="small expand round button" onclick="$('div#add_hotel').hide();/*$('div#map').css('margin', '2em -0.4em')*/">
            </div>        
            <div class="small-7 right">
                <input type="submit" value="Сохранить" class="small expand success round button">
            </div>        
            <input type="hidden" name="task" value="" placeholder="task">
            <input type="hidden" name="lat" placeholder="lat">
            <input type="hidden" name="lng" placeholder="lng">
            <input type="hidden" name="photo" value="" placeholder="photo">
            <input type="hidden" name="city" value="" placeholder="city">
            <input type="hidden" name="region" value="" placeholder="region">
            <input type="hidden" name="map_icon" value="hotel" placeholder="map_icon">
            <input type="hidden" name="id_poi" value="" placeholder="id_poi">
        </div>
    </form>
<p class="grey_text">Когда ты добавишь отель на карту, он станет "твоим" и в будущем ты сможешь его редактировать.</p>
<p class="grey_text">Пожалуйста убедись что все данные корректны: усталый путник будет надеяться на тебя.</p>
<p>&nbsp;</p>
</div>


<div class="hide small-12 large-7" id="add_parking">
    <form name="parking" id="parking" action="//app.motohelplist.com/old/map.php" method="get" onsubmit="formsubmit('parking'); return false"> 
        <div class="row">
            <div class="large-4 small-4 columns">
                <span class="prefix">Координаты</span>
            </div>
            <div class="large-8 small-8 columns">
                <input type="text" name="latlng" placeholder="Координаты" onclick="add_poi('parking')">
            </div>
            <div class="large-4 small-4 columns">
                <span class="prefix">Цена в сутки</span>
            </div>
            <div class="large-8 small-8  columns">                    
                <input type="text" name="price" placeholder="от-до">
            </div>        
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone1" placeholder="с кодом страны">
            </div>        
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone2" placeholder="…еще телефон?">
            </div>        

            <div class="tablediv2 columns">        
                <textarea rows="4" class="bottom-textarea" name="description" placeholder="Подробнее (255 знаков макс., необязательно)"></textarea>
            </div>
        </div>
        <div class="row">
           <div class="text-center ">
                <div class="small-6 columns">
                    <div class="switch round">
                      <p class="text-center">Доступ 24/7</p>
                      <input id="access" name="access" value="1" type="checkbox">
                        <label for="access"></label>
                    </div>
                </div>
            </div>
            <div class="text-center ">
                <div class="small-6 columns">                            
                    <div class="switch round">
                      <p class="text-center">Камеры</p>
                      <input id="camera" name="camera" value="1" type="checkbox">
                        <label for="camera">
                      </label>
                    </div>
                </div>
            </div>
            <div class="text-center ">
                <div class="small-6 columns">
                    <div class="switch round">
                      <p class="text-center">Трезвая охрана</p>
                      <input id="security" name="security" value="1" type="checkbox">
                        <label for="security">
                      </label>
                    </div>
                </div>
            </div>
            <div class="text-center ">
                <div class="small-6 columns">                            
                    <div class="switch round">
                      <p class="text-center">Много мотомест</p>
                      <input id="big" name="big" value="1" type="checkbox">
                        <label for="big">
                      </label>
                    </div>
                </div>
            </div>
            <div class="small-4 columns">
                <input type="reset" value="Отмена" class="small expand round button" onclick="$('div#add_parking').hide();/*$('div#map').css('margin', '2em -0.4em')*/">
            </div>        
            <div class="small-7 right">
                <input type="submit" value="Сохранить" class="small expand success round button">
            </div>        
            <input type="hidden" name="task" placeholder="task">
            <input type="hidden" name="lat" placeholder="lat">
            <input type="hidden" name="lng" placeholder="lng">
            <input type="hidden" name="photo" value="" placeholder="photo">
            <input type="hidden" name="city" value="" placeholder="city">     
            <input type="hidden" name="region" value="" placeholder="region">                                           
            <input type="hidden" name="map_icon" value="parking" placeholder="map_icon">      
            <input type="hidden" name="id_poi" value="" placeholder="id_poi">              
        </div>
    </form>
<p class="grey_text">Когда ты добавишь паркинг на карту, он станет "твоим" и в будущем ты сможешь его редактировать.</p>
<p class="grey_text">Пожалуйста убедись что все данные корректны: чтобы путнику не пришлось плутать в незнакомом городе.</p>
<p>&nbsp;</p>
</div>


<div class="hide small-12 large-7" id="add_place">
    <form name="place" id="place" action="//app.motohelplist.com/old/map.php" method="get" onsubmit="formsubmit('place'); return false"> 
        <div class="row">
            <div class="large-4 small-4 columns">
                <span class="prefix">Координаты</span>
            </div>
            <div class="large-8 small-8 columns">
                <input type="text" name="latlng" placeholder="Координаты" onclick="add_poi('place')">
            </div>
            <div class="large-4 small-4 columns">
                <span class="prefix">Название</span>
            </div>
            <div class="large-8 small-8 columns">
                <input required type="text" name="name" placeholder="обязательно">
            </div>        
            <div class="tablediv2 columns">        
                <textarea rows="4" class="bottom-textarea" name="description" placeholder="Подробнее (255 знаков макс., необязательно)"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="small-4 columns">
                <input type="reset" value="Отмена" class="small expand round button" onclick="$('div#add_place').hide();/*$('div#map').css('margin', '2em -0.4em')*/">
            </div>        
            <div class="small-7 right">
                <input type="submit" value="Сохранить" class="small expand success round button">
            </div>     
            <input type="hidden" name="task" value="" placeholder="task">               
            <input type="hidden" name="lat" placeholder="lat">
            <input type="hidden" name="lng" placeholder="lng">
            <input type="hidden" name="photo" value="" placeholder="photo">
            <input type="hidden" name="city" value="" placeholder="city">      
            <input type="hidden" name="region" value="" placeholder="region">
            <input type="hidden" name="map_icon" value="place" placeholder="map_icon">        
            <input type="hidden" name="id_poi" value="" placeholder="id_poi">            
        </div>
    </form>
<p class="grey_text">Когда ты добавишь место тусовки на карту, оно станет "твоим" и в будущем ты сможешь его редактировать.</p>
<p class="grey_text">Пожалуйста убедись что координаты корректны: приезжему коллеге и тусануться хочется, и дальше ехать надо.</p>
<p>&nbsp;</p>
</div>


<div class="hide small-12 large-7" id="add_service">
    <form name="service" id="service" action="//app.motohelplist.com/old/map.php" method="get" onsubmit="formsubmit('service'); return false"> 
        <div class="row">
            <div class="large-4 small-4 columns">
                <span class="prefix">Координаты</span>
            </div>
            <div class="large-8 small-8 columns">
                <input type="text" name="latlng" placeholder="Координаты" onclick="add_poi('service')">
            </div>
            <div class="large-4 small-4 columns">
                <span class="prefix">Название</span>
            </div>
            <div class="large-8 small-8 columns">
                <input required type="text" name="name" placeholder="обязательно">
            </div>
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone1" placeholder="с кодом страны">
            </div>        
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone2" placeholder="…еще телефон?">
            </div>        
            <div class="tablediv2 columns">        
                <textarea rows="4" class="bottom-textarea" name="description" placeholder="Подробнее (255 знаков макс., необязательно)"></textarea>
            </div>
        </div>
        <div class="row ">
                        <div class="small-12 columns column-2">
                            <div class="switch round">
                              <p class="text-center">Электрика</p>
                              <input id="electric" name="electric" value="1" type="checkbox"  checked>
                              <label for="electric"></label>                              
                            </div>
                            
                            <div class="switch round">
                              <p class="text-center">Сварка</p>
                              <input id="weld" name="weld" value="1" type="checkbox" checked>
                              <label for="weld"></label>                              
                            </div>
                            <div class="switch round">
                              <p class="text-center">Расходники</p>
                              <input id="stock" name="stock" value="1" type="checkbox">
                              <label for="stock"></label>                              
                            </div>
                            <div class="switch round">
                              <p class="text-center">Глуб. тюнинг</p>
                              <input id="tuning" name="tuning" value="1" type="checkbox">
                              <label for="tuning"></label>                              
                            </div>
                            
                            <div class="switch round">
                              <p class="text-center">Немцы</p>
                              <input id="germans" name="germans" value="1" type="checkbox" checked>
                              <label for="germans"></label>                                                            
                            </div>                            
                            <div class="switch round">
                              <p class="text-center">Японцы</p>
                              <input id="japanese" name="japanese" value="1" type="checkbox" checked>
                              <label for="japanese"></label>                                                            
                            </div>                            
                            <div class="switch round">
                              <p class="text-center">Китайцы</p>
                              <input id="chinese" name="chinese" value="1" type="checkbox">
                              <label for="chinese"></label>                                                            
                            </div>                                                                                    
                            <div class="switch round">
                              <p class="text-center">Капиталка</p>
                              <input id="renewal" name="renewal" value="1" type="checkbox">
                              <label for="renewal"></label>                                                            
                            </div>
                        </div>
                    </div>
        <div class="row">
            <div class="small-4 columns">
                <input type="reset" value="Отмена" class="small expand round button" onclick="$('div#add_service').hide();/*$('div#map').css('margin', '2em -0.4em')*/">
            </div>        
            <div class="small-7 right">
                <input type="submit" value="Сохранить" class="small expand success round button">
            </div>        
            <input type="hidden" name="task" value="" placeholder="task">            
            <input type="hidden" name="lat" placeholder="lat">
            <input type="hidden" name="lng" placeholder="lng">
            <input type="hidden" name="photo" value="" placeholder="photo">
            <input type="hidden" name="city" value="" placeholder="city">
            <input type="hidden" name="region" value="" placeholder="region">
            <input type="hidden" name="map_icon" value="service" placeholder="map_icon">
            <input type="hidden" name="id_poi" value="" placeholder="id_poi">            
        </div>
                    
    </form>
<p class="grey_text">Когда ты добавишь сервис на карту, он станет "твоим" и в будущем ты сможешь его редактировать.</p>
<p class="grey_text">Пожалуйста убедись что все данные корректны: чтобы путнику не пришлось плутать в незнакомом городе.</p>
<p>&nbsp;</p>
</div>


<div class="hide small-12 large-7" id="add_tireservice">
    <form name="tireservice" id="tireservice" action="//app.motohelplist.com/old/map.php" method="get" onsubmit="formsubmit('tireservice'); return false">
        <div class="row">
            <div class="large-4 small-4 columns">
                <span class="prefix">Координаты</span>
            </div>
            <div class="large-8 small-8 columns">
                <input type="text" name="latlng" placeholder="Координаты" onclick="add_poi('tireservice')">
            </div>
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone1" placeholder="с кодом страны">
            </div>        
            <div class="large-2 small-3 columns">
                <span class="prefix">Тел.</span>
            </div>
            <div class="large-1 small-1 columns">
                <span class="prefix"><strong>+</strong></span>
            </div>
            <div class="large-3 small-8 columns">        
                <input type="tel" name="phone2" placeholder="…еще телефон?">
            </div>        
            <div class="tablediv2 columns">        
                <textarea rows="4" class="bottom-textarea" name="description" placeholder="Подробнее (255 знаков макс., необязательно)"></textarea>
            </div>
        </div>
        <div class="row ">
            <div class="small-12 columns column-2">
                <div class="switch round">
                  <p class="text-center">Подкат</p>
                  <input id="podkat" name="podkat" value="1" type="checkbox"  checked>
                    <label for="podkat"></label>
                </div>
                
                <div class="switch round">
                  <p class="text-center">Правка/варка дисков</p>
                  <input id="rims" name="rims" value="1" type="checkbox">
                    <label for="rims"></label>
                </div>
                <div class="switch round">
                  <p class="text-center">Балансировка</p>
                  <input id="balancer" name="balancer" value="1" type="checkbox"  checked>
                    <label for="balancer"></label>
                </div>
                <div class="switch round">
                  <p class="text-center">Варка покрышек</p>
                  <input id="tire_repair" name="tire_repair" value="1" type="checkbox">
                    <label for="tire_repair"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="small-4 columns">
                <input type="reset" value="Отмена" class="small expand round button" onclick="$('div#add_tireservice').hide();/*$('div#map').css('margin', '2em -0.4em')*/">
            </div>        
            <div class="small-7 right">
                <input type="submit" value="Сохранить" class="small expand success round button">
            </div>        
            <input type="hidden" name="task" value="" placeholder="task">            
            <input type="hidden" name="lat" placeholder="lat">
            <input type="hidden" name="lng" placeholder="lng">
            <input type="hidden" name="photo" value="" placeholder="photo">
            <input type="hidden" name="city" value="" placeholder="city">
            <input type="hidden" name="region" value="" placeholder="region">
            <input type="hidden" name="map_icon" value="tireservice" placeholder="map_icon">    
            <input type="hidden" name="id_poi" value="" placeholder="id_poi">            
        </div>
    </form>
<p class="grey_text">Когда ты добавишь шиномонтаж на карту, он станет "твоим" и в будущем ты сможешь его редактировать.</p>
<p class="grey_text">Пожалуйста убедись что все данные корректны: чтобы человеку не пришлось таскаться по городу с колесом на плечах.</p>
<p>&nbsp;</p>
</div>



<div class="small-5 submitgeocoder">
    <div class="row collapse postfix-round">
        <div class="small-11 columns">
          <input type="text" id="geocoderaddress" placeholder="Город или координаты" value="">
        </div>
        <div class="small-1 columns">
            <button class="button postfix round" id="submitgeocoder">Go</button>
        </div>
    </div>
</div>
<div id="map"></div>

<!--div class="mapfilter">
    <div class="markerBtn" data-category="trip">Путники</div>                
    <div class="markerBtn" data-category="place">Места тусовки</div>
    <div class="markerBtn" data-category="hotel">Отели</div>    
    <div class="markerBtn" data-category="parking">Паркинги</div>        
    <div class="markerBtn" data-category="service">Мото-сервисы</div>    
    <div class="markerBtn" data-category="tireservice">Шиномонтажи</div>        
</div-->




<div class="hide"><span id="getlat"><?php echo $this->_tpl_vars['lat']; ?>
</span><span id="getlng"><?php echo $this->_tpl_vars['lng']; ?>
</span>

<?php echo '    
<script type="text/javascript">
    $(window).load(function()
    {
        $(\'h1\').text(\'Карта\');
        $(\'li#add_to_map\').html($(\'div#append_menu\').html());
console.log(\'initialise from map.tpl\');
initialise(); // google maps        
//        prepareGeolocation(); 
//        doGeolocation(); 
        if (Foundation.utils.is_large_up()) // только для больших экранов
        {
            $(\'body\').toggleClass(\'large-4\');
            $(\'div#offcanvas_fixer\').removeClass(\'large-4\');

            // покажем сообщение 3 раза (string)111 и больше не будем
            if ( parseInt(localStorage.getItem(\'mapLargeScreen\')) != 111 )
            {
                localStorage.setItem(\'mapLargeScreen\', localStorage.getItem(\'mapLargeScreen\') + 1)
                ohSnap(\'Когда ты работаешь на компьютере или планшете, мы будем расширять карту на весь экран. Ты не против?\');
            }
        }
    })


// редактирует POI
function updatePOI(poi)
{
//console.log(markers[poi]);
//markers[poi].hide();
    
    var div = $(\'div#add_\'+markers[poi].map_icon).show();
    var form =  $(\'div#add_\'+markers[poi].map_icon).children("form");
        
    $(\'input[name=task]\').val(\'updatePOI\');
    $(\'input[name=latlng]\').val(\'[Изменить] \'+markers[poi].lat+\' \'+markers[poi].lng);
    $(\'input[name=name]\').val(markers[poi].name);
    $(\'input[name=price]\').val(markers[poi].price);
    $(\'input[name=phone1]\').val(markers[poi].phone1);
    $(\'input[name=phone2]\').val(markers[poi].phone2);
    $(\'input[name=phone2]\').val(markers[poi].phone2);
    $(\'textarea[name=description]\').val(markers[poi].description);
    $(\'input[name=lat]\').val(markers[poi].lat);
    $(\'input[name=lng]\').val(markers[poi].lng);
    $(\'input[name=map_icon]\').val(markers[poi].map_icon);
    $(\'input[name=id_poi]\').val(markers[poi].id);
    
    // hotel
    if ( markers[poi].parking == "1" ) $(\'input[name=parking]\').prop({"checked":true}); else $(\'input[name=parking]\').prop({"checked":false});
    if ( markers[poi].wifi == "1" ) $(\'input[name=wifi]\').prop({"checked":true}); else $(\'input[name=wifi]\').prop({"checked":false});
    if ( markers[poi].ac == "1" ) $(\'input[name=ac]\').prop({"checked":true}); else $(\'input[name=ac]\').prop({"checked":false});
    if ( markers[poi].sauna == "1" ) $(\'input[name=sauna]\').prop({"checked":true}); else $(\'input[name=sauna]\').prop({"checked":false});
    // parking
    if ( markers[poi].access == "1" ) $(\'input[name=access]\').prop({"checked":true}); else $(\'input[name=access]\').prop({"checked":false});
    if ( markers[poi].camera == "1" ) $(\'input[name=camera]\').prop({"checked":true}); else $(\'input[name=camera]\').prop({"checked":false});
    if ( markers[poi].security == "1" ) $(\'input[name=security]\').prop({"checked":true}); else $(\'input[name=security]\').prop({"checked":false});
    if ( markers[poi].big == "1" ) $(\'input[name=big]\').prop({"checked":true}); else $(\'input[name=big]\').prop({"checked":false});
    // service
    if ( markers[poi].electric == "1" ) $(\'input[name=electric]\').prop({"checked":true}); else $(\'input[name=electric]\').prop({"checked":false});
    if ( markers[poi].weld == "1" ) $(\'input[name=weld]\').prop({"checked":true}); else $(\'input[name=weld]\').prop({"checked":false});
    if ( markers[poi].stock == "1" ) $(\'input[name=stock]\').prop({"checked":true}); else $(\'input[name=stock]\').prop({"checked":false});
    if ( markers[poi].tuning == "1" ) $(\'input[name=tuning]\').prop({"checked":true}); else $(\'input[name=tuning]\').prop({"checked":false});
    if ( markers[poi].renewal == "1" ) $(\'input[name=renewal]\').prop({"checked":true}); else $(\'input[name=renewal]\').prop({"checked":false});
    if ( markers[poi].germans == "1" ) $(\'input[name=germans]\').prop({"checked":true}); else $(\'input[name=germans]\').prop({"checked":false});
    if ( markers[poi].japanese == "1" ) $(\'input[name=japanese]\').prop({"checked":true}); else $(\'input[name=japanese]\').prop({"checked":false});
    if ( markers[poi].chinese == "1" ) $(\'input[name=chinese]\').prop({"checked":true}); else $(\'input[name=chinese]\').prop({"checked":false});
    //tireservice
    if ( markers[poi].podkat == "1" ) $(\'input[name=podkat]\').prop({"checked":true}); else $(\'input[name=podkat]\').prop({"checked":false});
    if ( markers[poi].balancer == "1" ) $(\'input[name=balancer]\').prop({"checked":true}); else $(\'input[name=balancer]\').prop({"checked":false});
    if ( markers[poi].rims == "1" ) $(\'input[name=rims]\').prop({"checked":true}); else $(\'input[name=rims]\').prop({"checked":false});
    if ( markers[poi].tire_repair == "1" ) $(\'input[name=tire_repair]\').prop({"checked":true}); else $(\'input[name=tire_repair]\').prop({"checked":false});
}



// добавляет новый маркер который потом можно будет перетащить
// вставляет код страны в поле phone1
function add_poi(type)
{
    $(\'div#add_\'+type).hide();

    var baseMarker = new google.maps.Marker({
      position: map.getCenter(),
      animation: google.maps.Animation.BOUNCE,
      map: map,
      icon: \'img/icons/\'+type+\'.png\',
      draggable: true
    });
        // запросим у Гугла город-область по координатам
        reverseGeocode(baseMarker);
       


    // добавим маркер и попросим юзера его подвинуть
    google.maps.event.addListener(baseMarker, \'dragend\', function (a,b,c,d) 
    {
        lat = baseMarker.getPosition().lat();
        lng = baseMarker.getPosition().lng();
        $(\'div#add_\'+type).show();
        $(\'input[name=latlng]\').val(\'[Изменить] \'+lat.toFixed(6)+\', \'+lng.toFixed(6));
        $(\'input[name=lat]\').val(lat);
        $(\'input[name=lng]\').val(lng);        

        $(\'input[name=city]\').val(city);
        $(\'input[name=region]\').val(region);        

        baseMarker.setMap(null);   
        baseMarker = [];     
    });
    
    

    // вставим код страны в placeholder телефона
    // ИНОГДА глючит, разобраться    
/*    
    var currentCountry = getCookie(\'currentCountry\'); 
    var jqxhr = $.get("search.php?data=phoneCode&term="+currentCountry) 
    .success(function(data) 
    {
        var phoneCode = JSON.parse(data)[0][\'phone_code\'];
        $(\'input[name=phone1]\').attr( \'placeholder\', phoneCode);
    });
    */
}

    function formsubmit(type)
    {
        $(\'div#loader\').show();
        $(\'form#\'+type).submit();
    }

</script>


'; ?>
