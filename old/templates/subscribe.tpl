{if !$error}
    {if $channels}
        {foreach from=$channels item=channel}
            <div class="subscribe_block" id="{$channel->id_channel}">
                <div class="small-11 columns">
                    <p class="blockchat autofeed1">
                        {if $channel->is_subscribe > 0}
                            <a href="#" id="unsubscribe_{$channel->id_channel}" onclick="unsubscribe({$channel->id_channel});return false"><span class="radius label">Отписаться</span></a>
                        {else}
                            <a href="#" id="subscribe_{$channel->id_channel}" onclick="subscribe({$channel->id_channel});return false"><span class="radius label">Подписаться</span></a>
                        {/if}
                        {if $channel->type_channel}
                            {if $channel->type_channel == 1}{assign var=icon value=city}
                            {elseif $channel->type_channel == 2}{assign var=icon value=cycle}
                            {elseif $channel->type_channel == 3}{assign var=icon value=private}
                            {elseif $channel->type_channel == 4}{assign var=icon value=group}
                            {/if}
                            &nbsp;<img class="type_channel_icon" width="" src="img/icons/{$icon}.png">
                        {/if}
                        
                        <a class="plain" href="chat.php?channel={$channel->id_channel}&amp;" role="external">                                        
                            <span>{$channel->channel_name}</span>
                        </a>
                        
                        <img src="../img/ui-anim_basic_16x16.gif" class="hide" id="subscribe_loader_{$channel->id_channel}">
                        <circle class="hide unread_counter_subscribe" id="air-{$channel->id_channel}"></circle>
                    </p>
                </div>                    
                <div class="small-1 columns blockchat_arrow text-right">
                    <a class="plain" href="chat.php?channel={$channel->id_channel}&amp;" role="external">        
                        <img class="" src="img/icons/arrow_right.png">    
                    </a>
                </div>
                <hr>
                
                {*<!------  userinfo -------->
                {include file='userinfo.tpl' user=$channel}
                <!------  блок чата -------->        
                {include file='blockchat.tpl' channel=$channel*}
            </div>
        {/foreach}
    {/if}
    <div class="{if $channels}hide {/if}small-12 columns" id="empty">
        <p class="grey_text">Ты не подписан ни на один чат-канал.</p>
        <p class="grey_text">Найди интересную тему в Эфире и подпишись на нее. Она появится здесь и тебе не придется искать ее снова.</p>
        <br>
        <p><a href="onair.php" role="external" class="expand small round button">Посмотреть кто в Эфире</a></p>
    </div>
{else}
    <pre>{$error|@print_r}</pre>
{/if}    

{literal}
<script>
    $('h1').text('Подписки');
</script>
{/literal}
