<div class="row">
    <div class="small-11 columns">
        <div id="{$channel->lineid}" class="blockchat"></div>
    </div>
    <div class="small-1 columns blockchat_arrow text-right">
        <a class="plain" href="chat.php?channel={$channel->id_channel}&" role="external">        
            <img class="" src="img/icons/arrow_right.png">    
        </a>
    </div>
</div>
<div class="row">&nbsp;</div>

<script>
    $('div#{$channel->lineid}').html(linkify('{$channel->text|trim|replace:"\r":""|nl2br|escape:javascript}'));
</script>

