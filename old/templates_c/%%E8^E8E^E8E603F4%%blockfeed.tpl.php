<?php /* Smarty version 2.6.20, created on 2016-04-17 12:39:51
         compiled from blockfeed.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'blockfeed.tpl', 22, false),array('modifier', 'escape', 'blockfeed.tpl', 22, false),)), $this); ?>
<div class="message_block small-12 columns" id="">
    <?php if ($this->_tpl_vars['feed']->id_user > 0): ?>         <!------  userinfo -------->
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'userinfo.tpl', 'smarty_include_vars' => array('user' => $this->_tpl_vars['feed'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endif; ?>
    
    <div class="small-12">
        <p class="blockchat <?php if ($this->_tpl_vars['feed']->icon): ?>autofeed<?php endif; ?>">
            <?php if ($this->_tpl_vars['feed']->icon == 'cycle'): ?>…теперь на <?php endif; ?>
            <?php if ($this->_tpl_vars['feed']->icon == 'city'): ?>…переехал<?php if ($this->_tpl_vars['feed']->gender == 1): ?>а<?php endif; ?> в <?php endif; ?>
    
            <?php if ($this->_tpl_vars['feed']->id_user == 0 && $this->_tpl_vars['feed']->icon): ?>
                <img class="left" style="margin-right: 10px;" src="/img/icons/<?php echo $this->_tpl_vars['feed']->icon; ?>
.png">
            <?php endif; ?>
            
            <span id="<?php echo $this->_tpl_vars['feed']->id_feed; ?>
"></span>
        </p>
    </div>
</div>
<hr>
<script>
    $('span#<?php echo $this->_tpl_vars['feed']->id_feed; ?>
').html(linkify('<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['feed']->text)) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", "<br>") : smarty_modifier_replace($_tmp, "\n", "<br>")))) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
'));

</script>
