<?php /* Smarty version 2.6.20, created on 2016-02-09 15:08:08
         compiled from onair1.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'onair1.tpl', 49, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <div class="hide" id="append_menu_newchannel">
        <a href="add_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Новый канал</a>
    </div>
    <div class="hide" id="append_menu_findchannel">
        <a href="search_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Найти канал</a>
    </div>    

    <?php if ($this->_tpl_vars['channels']): ?>
        <?php $_from = $this->_tpl_vars['channels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['channel']):
?>
            <div class="message_block exp" id="<?php echo $this->_tpl_vars['channel']->id_channel; ?>
">
                <!------  userinfo ------->
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'userinfo.tpl', 'smarty_include_vars' => array('user' => $this->_tpl_vars['channel'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
                <!------  блок чата -------->        
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'blockchat.tpl', 'smarty_include_vars' => array('channel' => $this->_tpl_vars['channel'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>                    

                <span class="blockchat autofeed">
                    <?php if ($this->_tpl_vars['channel']->type_channel): ?>
                        <?php if ($this->_tpl_vars['channel']->type_channel == 1): ?><?php $this->assign('icon', 'city'); ?>
                        <?php elseif ($this->_tpl_vars['channel']->type_channel == 2): ?><?php $this->assign('icon', 'cycle'); ?>
                        <?php elseif ($this->_tpl_vars['channel']->type_channel == 3): ?><?php $this->assign('icon', 'private'); ?>
                        <?php elseif ($this->_tpl_vars['channel']->type_channel == 4): ?><?php $this->assign('icon', 'group'); ?>
                        <?php endif; ?>
                        <img  class="type_channel_icon" src="/img/icons/<?php echo $this->_tpl_vars['icon']; ?>
.png">
                    <?php endif; ?>
                </span>
    
                <span class="blockchat autofeed">
                    <?php echo $this->_tpl_vars['channel']->channel_name; ?>
 
                    <circle class="hide unread_counter_air" id="air-<?php echo $this->_tpl_vars['channel']->id_channel; ?>
"></circle>
                </span>                

                <span class="channel_answer hide small-12 columns" id="block_answer_<?php echo $this->_tpl_vars['channel']->id_channel; ?>
">
                    <textarea name="answer_<?php echo $this->_tpl_vars['channel']->id_channel; ?>
" id="answer_<?php echo $this->_tpl_vars['channel']->id_channel; ?>
" placeholder="Ответ в канал <?php echo $this->_tpl_vars['channel']->channel_name; ?>
"></textarea>
                    <button class="right tiny radius success button">Ответить</button>
                </span>
            </div>
        <?php endforeach; endif; unset($_from); ?>
        <div id="loadmore_bottom"><img src="../img/loader.gif"></div>
        <script>nomorechannels = 0</script>        
    <?php else: ?>
        <script>nomorechannels = 1</script>
        <div id="nomorechannels" class="">
            <p class="grey_text">Создай свой чат-канал — нажми в меню&nbsp;"Новый канал"</p>
            <p class="grey_text">Найди другие чат-каналы — нажми в меню&nbsp;"Найти канал"</p>        
        </div>
    <?php endif; ?>
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>    

<?php echo '
<script>
    $(\'h1\').text(\'Эфир\');
    $(\'li#add_to_channel\').html($(\'div#append_menu_newchannel\').html()+$(\'div#append_menu_findchannel\').html() );
    
    var timer;    
    	$(window).scroll(function()
    	{
       		if ($(document).height() - $(window).height() <= $(window).scrollTop() + 50 && nomorechannels == 0) 
    		{
    			this.scrollPosition = $(window).scrollTop();
    			$(\'div#loadmore_bottom\').show();
    
                if ( timer ) clearTimeout(timer);
    
                timer = setTimeout(function()
                {
                    loadMoreChannels();
                }, 50);
    		}
        });
</script>    
'; ?>
