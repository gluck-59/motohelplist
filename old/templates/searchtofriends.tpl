<div class="user_info">
    <div class="small-9 large-10 columns">
        <a href="profile.php?userprofile={$friend->id_user}&" role="external">
            <div class="avatar left">
                    <img id="avatar" class="{if $friend->gender}fe{/if}male" src="../avatar_make.php?id_user={$friend->id_user}&name={$friend->name}" alt="avatar"/>
            </div>
            <div class="" style="line-height: 120%">
                <div class="username {if $friend->gender}fe{/if}male">{if $friend->name == '0'}(. ) ( .){else}{$friend->name}{/if}</div>
                {if $friend->city}<div class="city grey_text">{$friend->city}</div>{/if}                
                <!--div class=""><noindex><nofollow>{$friend->phone}</noindex></nofollow></div-->

                {if $friend->id_urgency && $friend->id_urgency < 2}
                    <span class="right label radius error">{$friend->urgency|upper}</span> <!-- labels: [success error transparent warning] [round radius] --> 
                {/if}
                
                <span class="right online"></span>
                <span class="time_ago grey_text"></span>                        
            </div>
            <div class="">
                {if $friend->motorcycle}<div class="city grey_text">{$friend->motorcycle} {$friend->motorcycle_more}</div>{/if}                
                {*if $friend->motorcycle}<div class="left radius transparent label">&nbsp;{$friend->motorcycle}</div>{/if*}
            </div>
        </a>
    </div>

    {if $friend->task == 'addFriendsToChannel'}
        <br>
        <div class="switch round small-3 large-2 columns">    
            <input id="add_{$friend->id_user}" role="invitefriend" onclick="checktype()" value="{$friend->id_user}" type="checkbox">
            <label for="add_{$friend->id_user}"></label>
        </div>
    {else}
        <div class="small-3 large-2 columns">        
            <input type="{if $friend->is_friend == 0}hidden{else}button{/if}" id="unfriend_{$friend->id_user}" class="expand radius button small  alert" onclick="unfriend({$friend->id_user});return false" value="—">  
            <input type="{if $friend->is_friend == 0}button{else}hidden{/if}" id="tofriend_{$friend->id_user}" class="expand radius button small  success" onclick="tofriend({$friend->id_user});return false" value="+">
        </div>
    {/if}

    <br>
</div>
<hr>
