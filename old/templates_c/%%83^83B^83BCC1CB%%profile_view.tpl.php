<?php /* Smarty version 2.6.20, created on 2016-04-17 12:40:39
         compiled from profile_view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'nl2br', 'profile_view.tpl', 56, false),array('modifier', 'print_r', 'profile_view.tpl', 66, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <div class="">
        <div class="small-12">        
            <div class="columns avatar">
                <img id="avatar" class="<?php if ($this->_tpl_vars['profile']->gender): ?>fe<?php endif; ?>male" src="../avatar_make.php?id_user=<?php echo $this->_tpl_vars['profile']->id_user; ?>
&name=<?php echo $this->_tpl_vars['profile']->name; ?>
" alt="avatar"/>
            </div>
            <div class="small-9 columns">
                <h5 class="username <?php if ($this->_tpl_vars['profile']->gender): ?>fe<?php endif; ?>male" id="name_<?php echo $this->_tpl_vars['profile']->id_user; ?>
"><?php if ($this->_tpl_vars['profile']->name != '0'): ?><?php echo $this->_tpl_vars['profile']->name; ?>
<?php else: ?>Нет ника<?php endif; ?></h5>
                <p  class="last_seen grey_text">Заходил<?php if ($this->_tpl_vars['profile']->gender): ?>а<?php endif; ?> <span time="<?php echo $this->_tpl_vars['profile']->date_last_login; ?>
"></span></p>
                <p class="grey_text"><?php echo $this->_tpl_vars['profile']->status; ?>
</p>
            </div>                           
        </div>
        <?php if ($_SESSION['id_user'] != $this->_tpl_vars['profile']->id_user): ?>
        <div class="small-12 columns">
            <div class="small-5 columns">
                <a role="external" href="chat.php?channel=0&id_to=<?php echo $this->_tpl_vars['profile']->id_user; ?>
&" class="tiny expand secondary radius button">В личку</a>
            </div>                        
            <div class="small-1 columns">&nbsp;</div>
            <div class="small-5 columns"><noindex><nofollow>
                <a href="tel:+<?php echo $this->_tpl_vars['profile']->phone; ?>
" class="tiny expand secondary radius button">Позвонить</a></nofollow></noindex>
            </div>
        </div>
        <?php endif; ?>

        <div class="small-12 ">
            <input readonly type="text" name="city" class="readonly ui-autocomplete-input" value="<?php echo $this->_tpl_vars['profile']->city; ?>
" >
        </div>                           

        <?php if ($this->_tpl_vars['profile']->motorcycle): ?>                
            <div class="small-12">
                <input readonly type="text" name="motorcycle" class="readonly" value="<?php echo $this->_tpl_vars['profile']->motorcycle; ?>
 <?php echo $this->_tpl_vars['profile']->motorcycle_more; ?>
">
            </div>
        <?php endif; ?>
    </div>

    <div class="small-12 columns">
        <h4>Может помочь</h4>
        <?php if ($this->_tpl_vars['profile']->help_repair || $this->_tpl_vars['profile']->help_garage || $this->_tpl_vars['profile']->help_food || $this->_tpl_vars['profile']->help_bed || $this->_tpl_vars['profile']->help_beer || $this->_tpl_vars['profile']->help_strong || $this->_tpl_vars['profile']->help_party || $this->_tpl_vars['profile']->help_excursion): ?>
            <?php if ($this->_tpl_vars['profile']->help_repair): ?>  <label class="radius error label">Ремонт</label><?php endif; ?>
            <?php if ($this->_tpl_vars['profile']->help_garage): ?>  <label class="radius warning label">Гараж</label><?php endif; ?>            
            <?php if ($this->_tpl_vars['profile']->help_food): ?>    <label class="radius warning label">Поесть</label><?php endif; ?>                        
            <?php if ($this->_tpl_vars['profile']->help_bed): ?>     <label class="radius error label">Ночлег</label><?php endif; ?>                                    
            <?php if ($this->_tpl_vars['profile']->help_beer): ?>    <label class="radius success label">По пиву</label><?php endif; ?>
            <?php if ($this->_tpl_vars['profile']->help_strong): ?>  <label class="radius success label">Покрепче</label><?php endif; ?>            
            <?php if ($this->_tpl_vars['profile']->help_party): ?>   <label class="radius label">Составить компанию</label><?php endif; ?>                        
            <?php if ($this->_tpl_vars['profile']->help_excursion): ?><label class="radius label">Экскурсия по городу</label><?php endif; ?>                                    
        <?php else: ?>
            <p class="grey_text"><?php if ($this->_tpl_vars['profile']->name != '0'): ?><?php echo $this->_tpl_vars['profile']->name; ?>
<?php else: ?>Увы, он<?php if ($this->_tpl_vars['profile']->gender): ?>а<?php endif; ?><?php endif; ?> ничем не может помочь тебе.</p>
        <?php endif; ?>
    </div>

    <div class="small-12 columns"><p>&nbsp;</p></div>

    <?php if ($this->_tpl_vars['profile']->description): ?>
        <div class="small-12 ">                        
            <p class="grey_text"><?php echo ((is_array($_tmp=$this->_tpl_vars['profile']->description)) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</p>
        </div>                            
        <div class="small-12 columns"><p>&nbsp;</p></div>    
    <?php endif; ?>            

    <div class="small-12 columns" id="tofriend_unfriend">
        <input type="<?php if ($this->_tpl_vars['profile']->is_friend == 0): ?>hidden<?php else: ?>button<?php endif; ?>" id="unfriend_<?php echo $this->_tpl_vars['profile']->id_user; ?>
" class="expand button small round alert" onclick="unfriend(<?php echo $this->_tpl_vars['profile']->id_user; ?>
);return false" value="Удалить из друзей">  
        <input type="<?php if ($this->_tpl_vars['profile']->is_friend == 0): ?>button<?php else: ?>hidden<?php endif; ?>" id="tofriend_<?php echo $this->_tpl_vars['profile']->id_user; ?>
" class="expand button small round success" onclick="tofriend(<?php echo $this->_tpl_vars['profile']->id_user; ?>
);return false" value="Добавить в друзья">
    </div>
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>    



<script>
    $('h1').text($('h5').text());
    if (getCookie('id_user') == <?php echo $this->_tpl_vars['profile']->id_user; ?>
) 
        $('div#tofriend_unfriend').hide();
</script>    