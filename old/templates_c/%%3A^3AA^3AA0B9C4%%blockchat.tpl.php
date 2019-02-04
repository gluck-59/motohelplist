<?php /* Smarty version 2.6.20, created on 2016-05-09 18:39:41
         compiled from blockchat.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'trim', 'blockchat.tpl', 14, false),array('modifier', 'replace', 'blockchat.tpl', 14, false),array('modifier', 'nl2br', 'blockchat.tpl', 14, false),array('modifier', 'escape', 'blockchat.tpl', 14, false),)), $this); ?>
<div class="row">
    <div class="small-11 columns">
        <div id="<?php echo $this->_tpl_vars['channel']->lineid; ?>
" class="blockchat"></div>
    </div>
    <div class="small-1 columns blockchat_arrow text-right">
        <a class="plain" href="chat.php?channel=<?php echo $this->_tpl_vars['channel']->id_channel; ?>
&" role="external">        
            <img class="" src="img/icons/arrow_right.png">    
        </a>
    </div>
</div>
<div class="row">&nbsp;</div>

<script>
    $('div#<?php echo $this->_tpl_vars['channel']->lineid; ?>
').html(linkify('<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['channel']->text)) ? $this->_run_mod_handler('trim', true, $_tmp) : trim($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, "\r", "") : smarty_modifier_replace($_tmp, "\r", "")))) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
'));
</script>
