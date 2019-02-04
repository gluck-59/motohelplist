<?php /* Smarty version 2.6.20, created on 2015-12-15 13:31:51
         compiled from onair-old.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'onair-old.tpl', 109, false),)), $this); ?>
<div id="content" class="">
    <?php if (! $this->_tpl_vars['error']): ?>
        <div class="message_block">
            
            <!------  userinfo -------->
            <div class="user_info">
                <div class="avatar left">
                    <img class="" src="img/avatar.jpg" alt="avatar"/>
                </div>
                <div class="">
                    <span class="name">Курилл Нутычоблин блин нафик</span>
                    <span class="right request_type talk"></span>
                </div>
                <div class="grey_text">
                    <span class="city">Петропавловск-Камчатский</span>
                    <span class="time_ago">28 мин назад</span>
                </div>
                <div class="callto">
                    <a href="#" class="callto pm">В личку</a>                                
                    <a href="#" class="callto hide">&middot; Позвонить</a>
                    <a href="#" class="callto">&middot; Карта</a>
                </div>
            </div>

            <!------  блок чата -------->
            <div class="chat">
                <p>Здесь список первых мессаг всех тредов. Под каждым — счетчик ответов и автоматом переход внутрь треда.</p>
                <!------  block actions -------->                        
                <div class="action">
                  <a href="#" class="">5 ответов</a> 
                  <a href="#" class="">&middot; Следить</a> 
                  <a href="#" class="">&middot; Только SOS</a>
                </div>                                                
            </div>

        </div>

        <div class="message_block">
            
            <!------  userinfo -------->
            <div class="user_info">
                <div class="avatar left">
                    <img class="" src="img/avatar.jpg" alt="avatar"/>
                </div>
                <div class="">
                    <span class="name">Вася</span>
                    <span class="right request_type "></span>
                </div>
                <div class="grey_text">
                    <span class="city">Тверь</span>
                    <span class="time_ago">6 мин назад</span>
                </div>
                <div class="callto">
                    <a href="#" class="callto pm">В личку</a>                                
                    <a href="#" class="callto ">&middot; Позвонить</a>
                    <a href="#" class="callto" data-reveal-id="qModal">&middot; Карта</a>
                </div>
            </div>
            <!------  /userinfo -------->                        
            
            <div class="chat">
                <p>Здесь должен быть ответ другого юзера</p>
                <!------  block actions -------->                        
                <div class="action">
                  <a href="#" class="">5 ответов</a> 
                  <a href="#" class="">&middot; Следить</a> 
                </div>                                                
            </div>
        </div>
        
        <div class="message_block">
            
            <!------  userinfo -------->
            <div class="user_info">
                <div class="avatar left">
                    <img class="" src="img/avatar.jpg" alt="avatar"/>
                </div>
                <div class="">
                    <span class="name">Анонимный Алкоголик</span>
                    <span class="right request_type dtp"></span>
                </div>
                <div class="grey_text">
                    <span class="city">Сызрань</span>
                    <span class="time_ago">8 мин назад</span>
                </div>
                <div class="callto">
                    <a href="#" class="callto pm">В личку</a>                                
                    <a href="#" class="callto hide">&middot; Позвонить</a>
                    <a href="#" class="callto hide" data-reveal-id="qModal">&middot; Карта</a>
                </div>
            </div>
            <!------  /userinfo -------->                        
            
            <div class="chat">
                <p>Это ответ в одну строку для тестинга.</p>
                <!------  block actions -------->                        
                <div class="action">
                  <a href="#" class="">5 ответов</a> 
                  <a href="#" class="">&middot; Следить</a> 
                </div>
                
            </div>
        </div>                    


        <a class="secondary tiny round button expand" data-tab="queries">Загрузить ещё</a>

    <?php else: ?>
        <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
    <?php endif; ?>    
</div>

<pre><?php echo print_r($this->_tpl_vars['queries']); ?>
</pre>