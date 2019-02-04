<?php /* Smarty version 2.6.20, created on 2016-04-21 12:20:44
         compiled from bugreport.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'bugreport.tpl', 12, false),)), $this); ?>
<?php if ($this->_tpl_vars['bugreport']): ?>
    <h4>Если что-то пошло не так</h4>
    <p class="grey_text">
        Твоего города или мотоцикла нет в списке? Программа ведет себя странно? Где-то косяк?
    </p>
    <p class="grey_text">
        Пожалуйся сюда и приложи скриншот с багом. Мы обязательно прочитаем и ответим тебе в личку.
    </p>
        
    <form action="bugreport.php" method="post"  enctype="multipart/form-data" onsubmit="$('div#loader').show()">
        <textarea required rows="8" name="bug_description"></textarea>
        <textarea rows="30" class="hide" name="debug"><pre><?php echo print_r($this->_tpl_vars['bugreport']); ?>
</pre></textarea>
    <div class="small-12">
        <p>&nbsp;</p>
        <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
        <input type="file" name="screenshot">
        <p>&nbsp;</p>    
    </div>
        <input type="submit" class="round small expand button" value="Отправить">
    </form>
    
    <?php echo '
    <script>
        if (useragent = navigator.userAgent)
        {
            $(\'textarea[name=debug]\').append("\\r\\n"+useragent);
        }
        $(\'textarea[name=debug]\').append("\\r\\n"+localStorage.getItem(\'phone\'));
    </script>
    '; ?>

<?php else: ?>
    <h4>Спасибо!</h4>
    <p class="grey_text">
        Мы получили твое письмо и ответим как можно скорее.
    </p>
    <p>&nbsp;</p>        
    <p><a href="onair.php" role="external" class="expand small round button">Продолжить</a></p>    
<?php endif; ?>

<?php echo '
<script>
    $(\'h1\').text(\'Прием багов\');
    $(\'div#loader\').hide();
    /*
        var reformalOptions = {
        project_id: 971355,
        project_host: "motohelplist.reformal.ru",
        tab_orientation: "left",
        tab_indent: "50%",
        tab_bg_color: "#ff7733",
        tab_border_color: "#FFFFFF",
        tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/FFFFFF/2a94cfe6511106e7a48d0af3904e3090/left/1/tab.png",
        tab_border_width: 2
    };
    
    (function() {
        var script = document.createElement(\'script\');
        script.type = \'text/javascript\'; script.async = true;
        script.src = (\'https:\' == document.location.protocol ? \'https://\' : \'http://\') + \'media.reformal.ru/widgets/v3/reformal.js\';
        document.getElementsByTagName(\'head\')[0].appendChild(script);
    })();
    

    setTimeout(function(){
        $(\'a#reformal_tab\').hide();
        $(\'a#reformal_tab\').click();
    }, 500);
    */
</script>
'; ?>