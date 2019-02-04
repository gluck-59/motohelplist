<?php /* Smarty version 2.6.20, created on 2016-04-17 12:40:19
         compiled from feed.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'feed.tpl', 23, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <?php if (! $this->_tpl_vars['loadmore']): ?>
        <div class="channel_answer small-12 columns" id="postevent">
        <h4>Что новенького?</h4>        
            <textarea required style="resize: auto" rows="1" name="event" id="event" placeholder="...у людей посмотреть и свой показать"></textarea>
            <button class="right tiny radius success button" onclick="postEvent()">Поделиться</button>
        </div>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['feeds']): ?>
        <?php $_from = $this->_tpl_vars['feeds']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feed']):
?>
                <!-----  block feeds ------>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'blockfeed.tpl', 'smarty_include_vars' => array('user' => $this->_tpl_vars['feed'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endforeach; endif; unset($_from); ?>
        <div id="loadmore_bottom"><img src="../img/loader.gif"></div>
        <script>nomorefeeds = 0</script>        
    <?php else: ?>
        <!------  no any feeds ------>
        <p class="grey_text">Когда твои друзья что-нибудь напишут в своей ленте, ты увидишь это здесь.</p>
        <script>nomorefeeds = 1</script>        
    <?php endif; ?>
        
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>    


<?php echo '
<script>
    $(\'h1\').text(\'Новости\');
    
    function postEvent()
    {
        if ( $(\'textarea#event\').val() != \'\')
        {
            var text = $(\'textarea#event\').val();
            
            // отловим ссылки и картинки
//            var newTxt;
//            newTxt = linkify(text);

            // отправим пост
            $.post(
                "feed.php",
                {
                    postEvent: text
                },
                onAjaxSuccess
            ); 
            function onAjaxSuccess(data)
            {
                if (data == \'nonick\')
                {
                    ohSnap(\'Человек Без Ника, мы не смогли тебя опознать\');
                    return false;
                }

                if (data != \'\')
                {
                    ohSnap(\'Готово! Это увидят твои друзья у себя в ленте\');
                    //$(\'div#postevent\').hide(animation);
                    $(\'textarea#event\').val(\'\');
                    //console.log(data);
                    $(\'#postevent\').after(data);
                }
                    
                else
                {   
                    ohSnap(\'Что-то пошло не так: \'+data);
                    console.log(data);
                }
            }
        }
        else
        {
            ohSnap(\'Напиши что-нибудь позитивное для друзей\');
            $(\'textarea#event\').css(\'background\',\'honeydew\');
        }
    }
    
    // при скролле вниз экрана подгрузим новые фиды в список
    var timer;    
	$(window).scroll(function(){
		if ($(document).height() - $(window).height() <= $(window).scrollTop() + 50 && nomorefeeds == 0) 
		{
			this.scrollPosition = $(window).scrollTop();
			$(\'div#loadmore_bottom\').show();

            if ( timer ) clearTimeout(timer);

            timer = setTimeout(function()
            {
//                console.log(\'ку\');
                loadMorefeeds();                
            }, 500);
		}
	});
	
</script>    
'; ?>