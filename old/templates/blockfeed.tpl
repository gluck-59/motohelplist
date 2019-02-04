<div class="message_block small-12 columns" id="">
    {if $feed->id_user > 0} {* юзер №0 это сообщение сервера, ему userinfo не положено *}
        <!------  userinfo -------->
        {include file='userinfo.tpl' user=$feed}
    {/if}
    
    <div class="small-12">
        <p class="blockchat {if $feed->icon}autofeed{/if}">
            {if $feed->icon == 'cycle'}…теперь на {/if}
            {if $feed->icon == 'city'}…переехал{if $feed->gender == 1}а{/if} в {/if}
    
            {if $feed->id_user == 0 && $feed->icon}
                <img class="left" style="margin-right: 10px;" src="img/icons/{$feed->icon}.png">
            {/if}
            
            <span id="{$feed->id_feed}"></span>
        </p>
    </div>
</div>
<hr>
<script>
    $('span#{$feed->id_feed}').html(linkify('{$feed->text|replace:"\n":"<br>"|escape:quotes}'));

</script>

