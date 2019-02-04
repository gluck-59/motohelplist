{if !$error}
    <div class="hide" id="append_menu_newchannel">
        <a href="add_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Новый канал</a>
    </div>
    <div class="hide" id="append_menu_findchannel">
        <a href="search_channel.php" role="external">&nbsp;&nbsp;>&nbsp;Найти канал</a>
    </div>    

    {if $channels}
        {foreach from=$channels item=channel}
            <div class="message_block" id="{$channel->id_channel}">
                <!------  userinfo ------->
                {include file='userinfo.tpl' user=$channel} 
                <!------  блок чата -------->        
                {include file='blockchat.tpl' channel=$channel}                    

                <span class="blockchat autofeed">
                    {if $channel->type_channel}
                        {if $channel->type_channel == 1}{assign var=icon value=city}
                        {elseif $channel->type_channel == 2}{assign var=icon value=cycle}
                        {elseif $channel->type_channel == 3}{assign var=icon value=private}
                        {elseif $channel->type_channel == 4}{assign var=icon value=group}
                        {/if}
                        <img  class="type_channel_icon" src="img/icons/{$icon}.png">
                    {/if}
                </span>
    
                <span class="blockchat autofeed">
                    {$channel->channel_name} 
                    <circle class="hide unread_counter_air" id="air-{$channel->id_channel}"></circle>
                </span>                

                <span class="channel_answer hide small-12 columns" id="block_answer_{$channel->id_channel}">
                    <textarea name="answer_{$channel->id_channel}" id="answer_{$channel->id_channel}" placeholder="Ответ в канал {$channel->channel_name}"></textarea>
                    <button class="right tiny radius success button">Ответить</button>
                </span>
                <div class="row"><hr><br></div>
            </div>
        {/foreach}
        <div id="loadmore_bottom"><img src="../old/img/loader.gif"></div>
        <script>nomorechannels = 0</script>        
    {else}
        <script>nomorechannels = 1</script>
        <div id="nomorechannels" class="">
            <p class="grey_text">Создай свой чат-канал — нажми в меню&nbsp;"Новый канал"</p>
            <p class="grey_text">Найди другие чат-каналы — нажми в меню&nbsp;"Найти канал"</p>        
        </div>
    {/if}
{else}
    <pre>{$error|@print_r}</pre>
{/if}    

{literal}
<script>
    $('h1').text('Эфир');
    $('li#add_to_channel').html($('div#append_menu_newchannel').html()+$('div#append_menu_findchannel').html() );
    
    var timer;    
    	$(window).scroll(function()
    	{
       		if ($(document).height() - $(window).height() <= $(window).scrollTop() + 100 && nomorechannels == 0) 
    		{
    			this.scrollPosition = $(window).scrollTop();
    			$('div#loadmore_bottom').show();
    
                if ( timer ) clearTimeout(timer);
    
                timer = setTimeout(function()
                {
                    loadMoreChannels();
                }, 50);
    		}
        });
</script>    
{/literal}
