<?php /* Smarty version 2.6.20, created on 2016-05-09 18:39:41
         compiled from userinfo.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'is_null', 'userinfo.tpl', 25, false),)), $this); ?>
<div class="user_info">
    <a href="profile.php?userprofile=<?php echo $this->_tpl_vars['user']->id_user; ?>
&" role="external">
        <div class="avatar left">
            <img class="<?php if ($this->_tpl_vars['user']->gender): ?>fe<?php endif; ?>male" id="avatar" class="" src="../old/avatar_make.php?id_user=<?php echo $this->_tpl_vars['user']->id_user; ?>
&name=<?php echo $this->_tpl_vars['user']->name; ?>
" alt="avatar"/>
        </div>
        <div class="userline">
            <span class="<?php if ($this->_tpl_vars['user']->gender): ?>fe<?php endif; ?>male username"><?php if ($this->_tpl_vars['user']->name == '0'): ?>(. ) ( .)<?php else: ?><?php echo $this->_tpl_vars['user']->name; ?>
<?php endif; ?></span>
            <span class="right online"></span>
            <span class="transparent label grey_text time_ago" time="<?php echo $this->_tpl_vars['user']->ts; ?>
"></span>            <?php if ($this->_tpl_vars['user']->id_urgency && $this->_tpl_vars['user']->id_urgency > 0): ?>
                <label class="urgency label radius <?php if ($this->_tpl_vars['user']->id_urgency == 1): ?>warning<?php elseif ($this->_tpl_vars['user']->id_urgency == 2): ?>error<?php elseif ($this->_tpl_vars['user']->id_urgency == 3): ?>success<?php endif; ?>"><?php echo $this->_tpl_vars['user']->urgency; ?>
</label>
            <?php endif; ?>
        </div>

        <div class="grey_text">
            <div class="small-8 left city"><?php echo $this->_tpl_vars['user']->city; ?>
</div>
        </div>
    </a>

    <div class="callto">
<?php if ($_SESSION['id_user'] != $this->_tpl_vars['user']->id_user): ?>
        <a href="chat.php?channel=0&id_to=<?php echo $this->_tpl_vars['user']->id_user; ?>
&" role="external" class="callto radius label">В ЛИЧКУ</a>&nbsp;<noindex><nofollow><a href="tel:+<?php echo $this->_tpl_vars['user']->phone; ?>
" class="callto radius label">ЗВОНИТЬ</a>
<?php endif; ?>    

        <?php if (! ((is_array($_tmp=$this->_tpl_vars['user']->is_subscribe)) ? $this->_run_mod_handler('is_null', true, $_tmp) : is_null($_tmp))): ?>
        <!------  block actions -------->                        
        <span class="actions">
                        <a href="#" <?php if ($this->_tpl_vars['user']->is_subscribe): ?>class="hide"<?php endif; ?> id="subscribe_<?php echo $this->_tpl_vars['user']->id_channel; ?>
" onclick="subscribe(<?php echo $this->_tpl_vars['user']->id_channel; ?>
);return false"><span class="radius callto label">Подписаться</span></a>
            <a href="#" <?php if (! $this->_tpl_vars['user']->is_subscribe): ?>class="hide"<?php endif; ?> id="unsubscribe_<?php echo $this->_tpl_vars['user']->id_channel; ?>
" onclick="unsubscribe(<?php echo $this->_tpl_vars['user']->id_channel; ?>
);return false"><span class="radius callto label">Отписаться</span></a>
            <img src="../old/img/ui-anim_basic_16x16.gif" class="hide" id="subscribe_loader_<?php echo $this->_tpl_vars['user']->id_channel; ?>
">
        </span>
        </nofollow></noindex>
        <?php endif; ?>
    </div>
    

</div>