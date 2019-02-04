<?php /* Smarty version 2.6.20, created on 2016-04-17 17:09:08
         compiled from search_channel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'search_channel.tpl', 56, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <div class="hide" id="append_menu_newchannel">
        <a href="add_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Новый канал</a>
    </div>
    <div class="hide" id="append_menu_findchannel">
        <a href="search_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Найти канал</a>
    </div>    
    
    <script src="js/jquery-ui.min.js"></script>     
    <script src="js/jquery.ui.autocomplete.min.js"></script>    
        
        <form id="find_channel_form" class="small-12"  autocomplete="off" onsubmit="find_channel_formsubmit(); return false;">
    <h4>Найди существующие каналы</h4>
        <div id="city" class="row select">
            <div class="large-4 small-4 columns">
                <span class="prefix">По городу</span>
            </div>
            <div class="large-8 small-8 columns">
                <input id="" name="city" class="ui-autocomplete-input" placeholder="Латинскими буквами" type="text" autocomplete="off"></input>
            </div>
        </div>

        <div id="select_motorcycle" class="row select">
            <div class="large-4 small-4 columns">
                <span class="prefix">По мотоциклу</span>
            </div>
            <div class="large-8 small-8 columns">
                <input id="" type="text" name="select_motorcycle" class="ui-autocomplete-input" placeholder="VTX, Dragstar, CBR, Ninja, Boulevard..."  autocomplete="off">
            </div>                
        </div>

        <div id="name_channel" class="row select">
            <div class="large-4 small-4 columns">
                <span class="prefix">По названию</span>
            </div>
            <div class="large-8 small-8 columns">
                <input id="" type="text" name="name_channel" class="ui-autocomplete-input" placeholder="Если знаешь название канала">
            </div>                                
        </div>

        <div class="row">
            <p class="grey_text">Начни вводить первые буквы города, мотоцикла или названия канала.
            <br>Выбери нужный канал из списка.</p>
            <input type="submit" class="small expand success round button" value="Найти">
            <input type="hidden" name="task" value="find_channel">
            <input type="hidden" name="id_city" value="" placeholder="id_city">
            <input type="hidden" name="id_motorcycle" value="" placeholder="id_motorcycle">
            <input type="hidden" name="id_channel" value="" placeholder="id_channel">            
        </div>

    </form>
    <br>
    <div class="row" id="found"></div>
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>    



<?php echo '
<script>
    $(\'h1\').text(\'Поиск канала\');
    $(\'li#add_to_channel\').html($(\'div#append_menu_newchannel\').html()+$(\'div#append_menu_findchannel\').html() );
    


	
function find_channel_formsubmit()
{
request = $(\'form#find_channel_form\').serialize();


    $(\'div#loader\').show();
    var jqxhr = $.get("onair.php?"+request)
    .success(function(data) 
    {
        $(\'div#found\').html(data);
        $(\'div#loader\').hide();
    });

}

	
	
// город    
$(function() {
    $( "input[name=city]" ).autocomplete({
        source: "search.php?data=city",
        minLength: 3,
        select: function( event, ui ) {
        
        $( "input[name=id_city]" ).val(ui.item.id);
        select_input(this);
                console.log(ui.item ?
          "Selected: " + ui.item.value + " aka " + ui.item.id :
          "Nothing selected, input was " + this.value );
                  
        },
        messages: 
        {
            noResults: function()
            {
//                console.log(\'нету\');
            },
            results: function() 
            {
//                console.log(\'есть\');
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
            select_input(this);            
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


// имя канала
$(function() {
    $( "input[name=name_channel]" ).autocomplete(
    {
        source: "search.php?data=name_channel",
        minLength: 2,
        select: function( event, ui ) 
        {
            $( "input[name=id_channel]" ).val(ui.item.id);
            select_input(this);            
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
  
  
function select_input(inp)
{
//    inpid = inp.name;
    $(\'div.select\').not($(\'#\'+inp.name)).hide();    
}

	
</script>    
'; ?>

