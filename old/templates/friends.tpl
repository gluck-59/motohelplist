{if !$error}
    <div id="searchtofriends">
        <p class="grey_text">Найди знакомых по реалу</p>
        <div class="small-12">
            <div class="row collapse postfix-round">
                <div class="small-8 columns">
                  <input type="text" id="searchstring" placeholder="Ник или телефон" value="">
                </div>
                <div class="small-4 columns">
                    <button class="button postfix round" onclick="searchToFriends()">Go</button>
                </div>
            </div>
        </div>
    </div>
    <div id="friends" class="small-12">
    {if $friends}
            {foreach from=$friends item=user}
                {include file='userinfo.tpl'}
                <hr>
            {/foreach}
    {else}
    <p class="grey_text">Найди новых друзей в Эфире</p>
    <br>
    <p><a href="onair.php" role="external" class="expand small round button">Посмотреть кто в Эфире</a></p>
    {/if}
    </div>    
{else}
    <pre>{$error|@print_r}</pre>
{/if}

{literal}
<script>
    $('h1').text('Друзья');

    
</script>
{/literal}
