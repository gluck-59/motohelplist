{if !$error}
    <div class="">
        <div class="small-12">        
            <div class="columns avatar">
                <img id="avatar" class="{if $profile->gender}fe{/if}male" src="../avatar_make.php?id_user={$profile->id_user}&name={$profile->name}" alt="avatar"/>
            </div>
            <div class="small-9 columns">
                <h5 class="username {if $profile->gender}fe{/if}male" id="name_{$profile->id_user}">{if $profile->name != '0'}{$profile->name}{else}Нет ника{/if}</h5>
                <p  class="last_seen grey_text">Заходил{if $profile->gender}а{/if} <span time="{$profile->date_last_login}"></span></p>
                <p class="grey_text">{$profile->status}</p>
            </div>                           
        </div>
        {if $smarty.session.id_user != $profile->id_user}
        <div class="small-12 columns">
            <div class="small-5 columns">
                <a role="external" href="chat.php?channel=0&id_to={$profile->id_user}&" class="tiny expand secondary radius button">В личку</a>
            </div>                        
            <div class="small-1 columns">&nbsp;</div>
            <div class="small-5 columns"><noindex><nofollow>
                <a href="tel:+{$profile->phone}" class="tiny expand secondary radius button">Позвонить</a></nofollow></noindex>
            </div>
        </div>
        {/if}

        <div class="small-12 ">
            <input readonly type="text" name="city" class="readonly ui-autocomplete-input" value="{$profile->city}" >
        </div>                           

        {if $profile->motorcycle}                
            <div class="small-12">
                <input readonly type="text" name="motorcycle" class="readonly" value="{$profile->motorcycle} {$profile->motorcycle_more}">
            </div>
        {/if}
    </div>

    <div class="small-12 columns">
        <h4>Может помочь</h4>
        {if $profile->help_repair || $profile->help_garage || $profile->help_food || $profile->help_bed || $profile->help_beer || $profile->help_strong || $profile->help_party || $profile->help_excursion}
            {if $profile->help_repair}  <label class="radius error label">Ремонт</label>{/if}
            {if $profile->help_garage}  <label class="radius warning label">Гараж</label>{/if}            
            {if $profile->help_food}    <label class="radius warning label">Поесть</label>{/if}                        
            {if $profile->help_bed}     <label class="radius error label">Ночлег</label>{/if}                                    
            {if $profile->help_beer}    <label class="radius success label">По пиву</label>{/if}
            {if $profile->help_strong}  <label class="radius success label">Покрепче</label>{/if}            
            {if $profile->help_party}   <label class="radius label">Составить компанию</label>{/if}                        
            {if $profile->help_excursion}<label class="radius label">Экскурсия по городу</label>{/if}                                    
        {else}
            <p class="grey_text">{if $profile->name != '0'}{$profile->name}{else}Увы, он{if $profile->gender}а{/if}{/if} ничем не может помочь тебе.</p>
        {/if}
    </div>

    <div class="small-12 columns"><p>&nbsp;</p></div>

    {if $profile->description}
        <div class="small-12 ">                        
            <p class="grey_text">{$profile->description|nl2br}</p>
        </div>                            
        <div class="small-12 columns"><p>&nbsp;</p></div>    
    {/if}            

    <div class="small-12 columns" id="tofriend_unfriend">
        <input type="{if $profile->is_friend == 0}hidden{else}button{/if}" id="unfriend_{$profile->id_user}" class="expand button small round alert" onclick="unfriend({$profile->id_user});return false" value="Удалить из друзей">  
        <input type="{if $profile->is_friend == 0}button{else}hidden{/if}" id="tofriend_{$profile->id_user}" class="expand button small round success" onclick="tofriend({$profile->id_user});return false" value="Добавить в друзья">
    </div>
{else}
    <pre>{$error|@print_r}</pre>
{/if}    



<script>
    $('h1').text($('h5').text());
    if (getCookie('id_user') == {$profile->id_user}) 
        $('div#tofriend_unfriend').hide();
</script>    
