<div class="user_info">
    <a href="profile.php?userprofile={$user->id_user}&" role="external">
        <div class="avatar left">
            <img class="{if $user->gender}fe{/if}male" id="avatar" class="" src="../old/avatar_make.php?id_user={$user->id_user}&name={$user->name}" alt="avatar"/>
        </div>
        <div class="userline">
            <span class="{if $user->gender}fe{/if}male username">{if $user->name == '0'}(. ) ( .){else}{$user->name}{/if}</span>
            <span class="right online"></span>
            <span class="transparent label grey_text time_ago" time="{$user->ts}"></span>{*$user->ts|date_format:"%d %b, %H:%M"*}
            {if $user->id_urgency && $user->id_urgency > 0}
                <label class="urgency label radius {if $user->id_urgency == 1}warning{elseif $user->id_urgency == 2}error{elseif $user->id_urgency == 3}success{/if}">{$user->urgency}</label>
            {/if}
        </div>

        <div class="grey_text">
            <div class="small-8 left city">{$user->city}</div>
        </div>
    </a>

    <div class="callto">
{if $smarty.session.id_user != $user->id_user}
        <a href="chat.php?channel=0&id_to={$user->id_user}&" role="external" class="callto radius label">В ЛИЧКУ</a>&nbsp;<noindex><nofollow><a href="tel:+{$user->phone}" class="callto radius label">ЗВОНИТЬ</a>
{/if}    

        {if !$user->is_subscribe|is_null}
        <!------  block actions -------->                        
        <span class="actions">
            {*<a href="#" id="{$channel->id_channel}" onclick="$('div#block_answer_{$channel->id_channel}').toggle();return false;"><span class="success radius label">Ответить</span></a>*}
            <a href="#" {if $user->is_subscribe}class="hide"{/if} id="subscribe_{$user->id_channel}" onclick="subscribe({$user->id_channel});return false"><span class="radius callto label">Подписаться</span></a>
            <a href="#" {if !$user->is_subscribe}class="hide"{/if} id="unsubscribe_{$user->id_channel}" onclick="unsubscribe({$user->id_channel});return false"><span class="radius callto label">Отписаться</span></a>
            <img src="../old/img/ui-anim_basic_16x16.gif" class="hide" id="subscribe_loader_{$user->id_channel}">
        </span>
        </nofollow></noindex>
        {/if}
    </div>
{*else}
    <p>&nbsp;</p><p>&nbsp;</p>    
{/if*}    

</div>