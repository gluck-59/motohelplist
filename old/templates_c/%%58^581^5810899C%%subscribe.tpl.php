<?php /* Smarty version 2.6.20, created on 2016-04-17 17:10:11
         compiled from subscribe.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'subscribe.tpl', 50, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <?php if ($this->_tpl_vars['channels']): ?>
        <?php $_from = $this->_tpl_vars['channels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['channel']):
?>
            <div class="subscribe_block" id="<?php echo $this->_tpl_vars['channel']->id_channel; ?>
">
                <div class="small-11 columns">
                    <p class="blockchat autofeed1">
                        <?php if ($this->_tpl_vars['channel']->is_subscribe > 0): ?>
                            <a href="#" id="unsubscribe_<?php echo $this->_tpl_vars['channel']->id_channel; ?>
" onclick="unsubscribe(<?php echo $this->_tpl_vars['channel']->id_channel; ?>
);return false"><span class="radius label">Отписаться</span></a>
                        <?php else: ?>
                            <a href="#" id="subscribe_<?php echo $this->_tpl_vars['channel']->id_channel; ?>
" onclick="subscribe(<?php echo $this->_tpl_vars['channel']->id_channel; ?>
);return false"><span class="radius label">Подписаться</span></a>
                        <?php endif; ?>
                        <?php if ($this->_tpl_vars['channel']->type_channel): ?>
                            <?php if ($this->_tpl_vars['channel']->type_channel == 1): ?><?php $this->assign('icon', 'city'); ?>
                            <?php elseif ($this->_tpl_vars['channel']->type_channel == 2): ?><?php $this->assign('icon', 'cycle'); ?>
                            <?php elseif ($this->_tpl_vars['channel']->type_channel == 3): ?><?php $this->assign('icon', 'private'); ?>
                            <?php elseif ($this->_tpl_vars['channel']->type_channel == 4): ?><?php $this->assign('icon', 'group'); ?>
                            <?php endif; ?>
                            &nbsp;<img class="type_channel_icon" width="" src="/img/icons/<?php echo $this->_tpl_vars['icon']; ?>
.png">
                        <?php endif; ?>
                        
                        <a class="plain" href="chat.php?channel=<?php echo $this->_tpl_vars['channel']->id_channel; ?>
&amp;" role="external">                                        
                            <span><?php echo $this->_tpl_vars['channel']->channel_name; ?>
</span>
                        </a>
                        
                        <img src="../img/ui-anim_basic_16x16.gif" class="hide" id="subscribe_loader_<?php echo $this->_tpl_vars['channel']->id_channel; ?>
">
                        <circle class="hide unread_counter_subscribe" id="air-<?php echo $this->_tpl_vars['channel']->id_channel; ?>
"></circle>
                    </p>
                </div>                    
                <div class="small-1 columns blockchat_arrow text-right">
                    <a class="plain" href="chat.php?channel=<?php echo $this->_tpl_vars['channel']->id_channel; ?>
&amp;" role="external">        
                        <img class="" src="/img/icons/arrow_right.png">    
                    </a>
                </div>
                <hr>
                
                            </div>
        <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>
    <div class="<?php if ($this->_tpl_vars['channels']): ?>hide <?php endif; ?>small-12 columns" id="empty">
        <p class="grey_text">Ты не подписан ни на один чат-канал.</p>
        <p class="grey_text">Найди интересную тему в Эфире и подпишись на нее. Она появится здесь и тебе не придется искать ее снова.</p>
        <br>
        <p><a href="onair.php" role="external" class="expand small round button">Посмотреть кто в Эфире</a></p>
    </div>
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>    

<?php echo '
<script>
    $(\'h1\').text(\'Подписки\');
</script>
'; ?>
