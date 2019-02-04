<?php /* Smarty version 2.6.20, created on 2016-04-20 17:44:25
         compiled from profile.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'profile.tpl', 139, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <?php $this->assign('id', $this->_tpl_vars['profile']->id_user); ?>
    <?php $this->assign('avatar', "img/avatar/".($this->_tpl_vars['id']).".jpg"); ?>
    <?php if (! file_exists ( $this->_tpl_vars['avatar'] )): ?> 
        <?php $this->assign('avatar', "img/avatar/no_avatar.png"); ?>
    <?php endif; ?>
          
    <link rel="stylesheet" type="text/css" href="sass/croppie.css" /> 
    <script src="js/croppie.js"></script>
    
    <div class="small-12 columns" id="avatar_upload">
        <input hidden type="file" id="upload" value="" onclick="avatar_change()">    
        <p>&nbsp;</p>    
        <div id="upload-demo"></div>
        <p>&nbsp;</p>
        <button class="upload-result success round small expand button" onclick="avatar_change()">Готово</button>
    </div>
    
    <form class="group_check" name="profile" action="profile.php?<?php echo time(); ?>
" method="post" autocomplete="off">
        <h4>О себе</h4>
        <div class="row">
            <div class="small-12 columns">
                <div class="small-3 columns">
                <img class="<?php if ($this->_tpl_vars['profile']->gender): ?>fe<?php endif; ?>male" id="avatar" src="<?php echo $this->_tpl_vars['avatar']; ?>
" alt="avatar"/>
                </div>

                <div class="small-8 columns right">
                    <!--p>Статус</p>
                    <input placeholder="Все что ты думаешь об этой жизни" type="text" name="status" class="name" value="<?php echo $this->_tpl_vars['profile']->status; ?>
"-->
                    <p>Ник в чате</p>
                    <input type="text" name="name" class="/*editable*/ name" value="<?php if ($this->_tpl_vars['profile']->name != '0'): ?><?php echo $this->_tpl_vars['profile']->name; ?>
<?php endif; ?>" placeholder="Общайся с мотобратьями">
                </div>
            <hr>
            <div class="small-4 columns">
                <span class="prefix">Телефон</span>
            </div>
            <div class="small-8 columns"><noindex><nofollow>
                <input readonly type="tel" name="phone" class="/*editable*/ name" value="<?php echo $this->_tpl_vars['profile']->phone; ?>
"></nofollow></noindex>
            </div>                                       

            <div class="small-4 columns">
                <span class="prefix">Пароль</span>
            </div>
            <div class="small-8 columns">
                <input type="password" id="passwd" class="/*editable*/ motorcycle" value="" placeholder="Оставить старый">
            </div>                                       
    
            <div class="small-4 columns">
                <span class="prefix">Город</span>
            </div>
            <div class="small-8 columns">
                <input type="text" name="city" class="name ui-autocomplete-input" value="<?php echo $this->_tpl_vars['profile']->city; ?>
" placeholder="латинскими буквами" autocomplete="off">
            </div>                           
            
            <div class="small-4 columns">
                <span class="prefix">Марка-модель мотоцикла</span>
            </div>
            <div class="small-8 columns">
                <input type="text" name="select_motorcycle" class="name ui-autocomplete-input" value="<?php echo $this->_tpl_vars['profile']->motorcycle; ?>
" placeholder="Выбери из списка"  autocomplete="off" >
            </div>                       
            <div class="small-4 columns">
                <span class="prefix">...объем и т.д.</span>
            </div>
            <div class="small-8 columns">
                <input type="text" name="motorcycle_more" class="" value="<?php echo $this->_tpl_vars['profile']->motorcycle_more; ?>
" placeholder="модель, год (необязательно)">
            </div>                       
        </div>
    
        <br><hr>
        
        <div class="small-12 columns">
            <h4>Могу помочь</h4>                    
            <p class="grey_text">Отметь, чем ты действительно сможешь помочь страннику. Он будет надеяться на тебя.</p>
            <br>
            <div class="text-center">
                <div class="switch round left small-6">
                  <p class="text-center">Ремонт</p>
                  <input id="repair" name="help_repair" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_repair == 1): ?>checked<?php endif; ?>>
                    <label for="repair"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">Поесть</p>
                  <input id="food" name="help_food" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_food == 1): ?>checked<?php endif; ?>>
                    <label for="food"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">По пиву</p>
                  <input id="beer" name="help_beer" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_beer == 1): ?>checked<?php endif; ?>>
                    <label for="beer"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">Гараж</p>
                  <input id="garage" name="help_garage" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_garage == 1): ?>checked<?php endif; ?>>
                    <label for="garage"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">Ночлег</p>
                  <input id="bed" name="help_bed" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_bed == 1): ?>checked<?php endif; ?>>
                    <label for="bed"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">Покрепче</p>
                  <input id="strong" name="help_strong" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_strong == 1): ?>checked<?php endif; ?>>
                    <label for="strong"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">Составить компанию</p>
                  <input id="party" name="help_party" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_party == 1): ?>checked<?php endif; ?>>
                    <label for="party"></label>
                </div>
                
                <div class="switch round left small-6">
                  <p class="text-center">Экскурсия по городу</p>
                  <input id="excursion" name="help_excursion" type="checkbox" <?php if ($this->_tpl_vars['profile']->help_excursion == 1): ?>checked<?php endif; ?>>
                    <label for="excursion"></label>
                </div>
            </div>
        </div>
        <hr>
        <div class="small-12 columns">
            <h4>Что-то еще?</h4>                    
            <textarea rows="4" class="bottom-textarea" name="description" placeholder="Необязательно"><?php if ($this->_tpl_vars['profile']->description): ?><?php echo $this->_tpl_vars['profile']->description; ?>
<?php endif; ?></textarea>
        </div>
        <br>            
        <input type="hidden" name="id_city" value="">
        <input type="hidden" name="id_motorcycle" value="">        
        <input type="hidden" name="password" value="">            
        <input type="submit" class="small success expand round button" value="Сохранить" onclick="">
    </form>
    <br>
    
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>
  
<script src="js/jquery-ui.min.js"></script> 
<script src="js/sha1.js"></script>



<script>
    var currentstatus = '<?php echo $this->_tpl_vars['profile']->status; ?>
';
    localStorage.setItem('currentstatus',currentstatus);    
</script>

<?php echo '
<script>
// город    
$(function() {
    $( "input[name=city]" ).autocomplete({
        source: "search.php?data=city",
        minLength: 3,
        select: function( event, ui ) {
        
        $( "input[name=id_city]" ).val(ui.item.id);
        
        /*        console.log(ui.item ?
          "Selected: " + ui.item.value + " aka " + ui.item.id :
          "Nothing selected, input was " + this.value );
        */          
        },
        messages: 
        {
            noResults: \'\',
            results: function() 
            {
            }
        }
    });
});


// мотоцикл
$(function() {
    $( "input[name=select_motorcycle]" ).autocomplete(
    {
        source: "search.php?data=motorcycle",
        minLength: 2,
        select: function( event, ui ) 
        {
            $( "input[name=id_motorcycle]" ).val(ui.item.id);
            
            /*        console.log(ui.item ?
              "Selected: " + ui.item.value + " aka " + ui.item.id :
              "Nothing selected, input was " + this.value );
            */          
        },
        messages: 
        {
            noResults: \'\',
            results: function() 
            {
            }
        }
    });
});
  
  
  
// смена пароля
$(\'#passwd\').change(function(){
    localStorage.setItem(\'sessionid\',$(\'#passwd\').val());
    $( "[name=password]" ).val(sha1($(\'#passwd\').val()));
});



// смена аватара    
function avatar_change()
{
    $(\'#avatar_upload\').toggle(300);
}

document.getElementById(\'avatar\').onclick = function(){
    document.getElementById(\'upload\').click();
}

var $uploadCrop;
function readFile(input) 
{
	if (input.files && input.files[0]) 
	{
        var reader = new FileReader();
        reader.onload = function (e) 
        {
        	$uploadCrop.croppie(\'bind\', {
        		url: e.target.result
        	});
        	$(\'.upload-demo\').addClass(\'ready\');
        }
        reader.readAsDataURL(input.files[0]);
    }
    else 
    {
        alert("Sorry - you need to update your OS");
    }
}

$uploadCrop = $(\'#upload-demo\').croppie({
	viewport: {
		width: 100,
		height: 100//,
//		type: \'circle\'
	},
	boundary: {
		width: 300,
		height: 300
	}
});

$(\'#upload\').on(\'change\', function () { readFile(this); });
$(\'.upload-result\').on(\'click\', function (ev) 
{
	$uploadCrop.croppie(\'result\', \'canvas\').then(function (resp) 
	{
        resp = resp.toDataURL("image/jpeg", 0.8);
        
        $(\'#avatar\').attr(\'src\', resp);

        // Creating object of FormData class
        var form_data = new FormData();                  
    	form_data.append("file", resp)
    				
        var jqxhr = $.ajax(
        {
            url: "/avatar_upload.php",
            type: "POST",
            dataType: \'json\', 
            data: form_data,
            processData: false,
            contentType: false
        })
        jqxhr.complete(function(res){ 
            ohSnap(res.responseText)
        });        
    });
});    
 

// скроллит экран вверх чтобы выпадающий список не перекрывался клавой
$("[name=select_motorcycle]").click(function() {
    $(\'html, body\').animate({
        scrollTop: $("[name=phone]").offset().top
    }, animation);
});

$("[name=city]").click(function() {
    $(\'html, body\').animate({
        scrollTop: $("[name=name]").offset().top
    }, animation);
});


$(\'h1\').text(\'Редактирование профиля\');

</script>
'; ?>
