{if !$error}
    {if !$loadmore}
        <div class="channel_answer small-12 columns" id="postevent">
        <h4>Что новенького?</h4>        
            <textarea required style="resize: auto" rows="1" name="event" id="event" placeholder="...у людей посмотреть и свой показать"></textarea>
            <button class="right tiny radius success button" onclick="postEvent()">Поделиться</button>
        </div>
    {/if}
    {if $feeds}
        {foreach from=$feeds item=feed}
                <!-----  block feeds ------>
                {include file='blockfeed.tpl' user=$feed}
        {/foreach}
        <div id="loadmore_bottom"><img src="../img/loader.gif"></div>
        <script>nomorefeeds = 0</script>        
    {else}
        <!------  no any feeds ------>
        <p class="grey_text">Когда твои друзья что-нибудь напишут в своей ленте, ты увидишь это здесь.</p>
        <script>nomorefeeds = 1</script>        
    {/if}
        
{else}
    <pre>{$error|@print_r}</pre>
{/if}    


{literal}
<script>
    $('h1').text('Новости');
    
    function postEvent()
    {
        if ( $('textarea#event').val() != '')
        {
            var text = $('textarea#event').val();
            
            // отловим ссылки и картинки
//            var newTxt;
//            newTxt = linkify(text);

            // отправим пост
            $.post(
                "feed.php",
                {
                    postEvent: text
                },
                onAjaxSuccess
            ); 
            function onAjaxSuccess(data)
            {
                if (data == 'nonick')
                {
                    ohSnap('Человек Без Ника, мы не смогли тебя опознать');
                    return false;
                }

                if (data != '')
                {
                    ohSnap('Готово! Это увидят твои друзья у себя в ленте');
                    //$('div#postevent').hide(animation);
                    $('textarea#event').val('');
                    //console.log(data);
                    $('#postevent').after(data);
                }
                    
                else
                {   
                    ohSnap('Что-то пошло не так: '+data);
                    console.log(data);
                }
            }
        }
        else
        {
            ohSnap('Напиши что-нибудь позитивное для друзей');
            $('textarea#event').css('background','honeydew');
        }
    }
    
    // при скролле вниз экрана подгрузим новые фиды в список
    var timer;    
	$(window).scroll(function(){
		if ($(document).height() - $(window).height() <= $(window).scrollTop() + 50 && nomorefeeds == 0) 
		{
			this.scrollPosition = $(window).scrollTop();
			$('div#loadmore_bottom').show();

            if ( timer ) clearTimeout(timer);

            timer = setTimeout(function()
            {
//                console.log('ку');
                loadMorefeeds();                
            }, 500);
		}
	});
	
</script>    
{/literal}