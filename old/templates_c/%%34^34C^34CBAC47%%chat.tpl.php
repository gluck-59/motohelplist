<?php /* Smarty version 2.6.20, created on 2016-04-23 21:14:41
         compiled from chat.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'chat.tpl', 44, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <script src="js/chat-personal.min.js"></script>
    <script src="js/jquery.visible.min.js"></script>
    

    
    <?php echo '
    <script>
     
    $(document).ready(function(){
    	chat.init('; ?>
<?php echo $this->_tpl_vars['id_channel']; ?>
,<?php echo $this->_tpl_vars['id_to']; ?>
<?php echo ');

    // textarea autoresize
    jQuery.each(jQuery(\'textarea#chatText\'), function() {
        var offset = this.offsetHeight - this.clientHeight;
     
        var resizeTextarea = function(el) {
            jQuery(el).css(\'height\', \'auto\').css(\'height\', el.scrollHeight + offset);
        };
        jQuery(this).on(\'keyup input\', function() { resizeTextarea(this); }).removeAttr(\'data-autoresize\');
    });

    });
        
    </script>
    '; ?>

    
    <link rel="stylesheet" type="text/css" href="sass/chat.css" />
    <div id="loadmore_top"><img src="../img/loader.gif"></div>            
            <div id="chatLineHolder">
            </div>

            <div class="text-messenger">
                <form id="chatSubmitForm" method="post" action="">
                    <textarea required data-autoresize rows="1" style=" min-height: 0;" id="chatText" name="chatText" class="textarea-messenger" placeholder="Сообщение..."></textarea>
                    <input name="id_to" type="hidden" value="<?php echo $this->_tpl_vars['id_to']; ?>
">
                    <input name="id_reply" type="hidden" value>
                    <input class="send-message" type="submit" value="Send">
                </form>
            </div>

<?php else: ?>
    <div id="content" class="">
        <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
    </div>
<?php endif; ?>    