{if !$error}
    <script src="js/chat-personal.min.js"></script>
    <script src="js/jquery.visible.min.js"></script>
    

    
    {literal}
    <script>
     
    $(document).ready(function(){
    	chat.init({/literal}{$id_channel},{$id_to}{literal});

    // textarea autoresize
    jQuery.each(jQuery('textarea#chatText'), function() {
        var offset = this.offsetHeight - this.clientHeight;
     
        var resizeTextarea = function(el) {
            jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
        };
        jQuery(this).on('keyup input', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
    });

    });
        
    </script>
    {/literal}
    
    <link rel="stylesheet" type="text/css" href="sass/chat.css" />
    <div id="loadmore_top"><img src="../img/loader.gif"></div>            
            <div id="chatLineHolder">
            </div>

            <div class="text-messenger">
                <form id="chatSubmitForm" method="post" action="">
                    <textarea required data-autoresize rows="1" style=" min-height: 0;" id="chatText" name="chatText" class="textarea-messenger" placeholder="Сообщение..."></textarea>
                    <input name="id_to" type="hidden" value="{$id_to}">
                    <input name="id_reply" type="hidden" value>
                    <input class="send-message" type="submit" value="Send">
                </form>
            </div>

{else}
    <div id="content" class="">
        <pre>{$error|@print_r}</pre>
    </div>
{/if}    
