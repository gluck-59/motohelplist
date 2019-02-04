<?php /* Smarty version 2.6.20, created on 2016-04-11 08:50:18
         compiled from searchtofriends.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'upper', 'searchtofriends.tpl', 13, false),)), $this); ?>
<div class="user_info">
    <div class="small-9 large-10 columns">
        <a href="profile.php?userprofile=<?php echo $this->_tpl_vars['friend']->id_user; ?>
&" role="external">
            <div class="avatar left">
                    <img id="avatar" class="<?php if ($this->_tpl_vars['friend']->gender): ?>fe<?php endif; ?>male" src="../avatar_make.php?id_user=<?php echo $this->_tpl_vars['friend']->id_user; ?>
&name=<?php echo $this->_tpl_vars['friend']->name; ?>
" alt="avatar"/>
            </div>
            <div class="" style="line-height: 120%">
                <div class="username <?php if ($this->_tpl_vars['friend']->gender): ?>fe<?php endif; ?>male"><?php if ($this->_tpl_vars['friend']->name == '0'): ?>(. ) ( .)<?php else: ?><?php echo $this->_tpl_vars['friend']->name; ?>
<?php endif; ?></div>
                <?php if ($this->_tpl_vars['friend']->city): ?><div class="city grey_text"><?php echo $this->_tpl_vars['friend']->city; ?>
</div><?php endif; ?>                
                <!--div class=""><noindex><nofollow><?php echo $this->_tpl_vars['friend']->phone; ?>
</noindex></nofollow></div-->

                <?php if ($this->_tpl_vars['friend']->id_urgency && $this->_tpl_vars['friend']->id_urgency < 2): ?>
                    <span class="right label radius error"><?php echo ((is_array($_tmp=$this->_tpl_vars['friend']->urgency)) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</span> <!-- labels: [success error transparent warning] [round radius] --> 
                <?php endif; ?>
                
                <span class="right online"></span>
                <span class="time_ago grey_text"></span>                        
            </div>
            <div class="">
                <?php if ($this->_tpl_vars['friend']->motorcycle): ?><div class="city grey_text"><?php echo $this->_tpl_vars['friend']->motorcycle; ?>
 <?php echo $this->_tpl_vars['friend']->motorcycle_more; ?>
</div><?php endif; ?>                
                            </div>
        </a>
    </div>

    <?php if ($this->_tpl_vars['friend']->task == 'addFriendsToChannel'): ?>
        <br>
        <div class="switch round small-3 large-2 columns">    
            <input id="add_<?php echo $this->_tpl_vars['friend']->id_user; ?>
" role="invitefriend" onclick="checktype()" value="<?php echo $this->_tpl_vars['friend']->id_user; ?>
" type="checkbox">
            <label for="add_<?php echo $this->_tpl_vars['friend']->id_user; ?>
"></label>
        </div>
    <?php else: ?>
        <div class="small-3 large-2 columns">        
            <input type="<?php if ($this->_tpl_vars['friend']->is_friend == 0): ?>hidden<?php else: ?>button<?php endif; ?>" id="unfriend_<?php echo $this->_tpl_vars['friend']->id_user; ?>
" class="expand radius button small  alert" onclick="unfriend(<?php echo $this->_tpl_vars['friend']->id_user; ?>
);return false" value="—">  
            <input type="<?php if ($this->_tpl_vars['friend']->is_friend == 0): ?>button<?php else: ?>hidden<?php endif; ?>" id="tofriend_<?php echo $this->_tpl_vars['friend']->id_user; ?>
" class="expand radius button small  success" onclick="tofriend(<?php echo $this->_tpl_vars['friend']->id_user; ?>
);return false" value="+">
        </div>
    <?php endif; ?>

    <br>
</div>
<hr>