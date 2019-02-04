<?php /* Smarty version 2.6.20, created on 2016-04-17 07:01:40
         compiled from friends.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'friends.tpl', 28, false),)), $this); ?>
<?php if (! $this->_tpl_vars['error']): ?>
    <div id="searchtofriends">
        <p class="grey_text">Найди знакомых по реалу</p>
        <div class="small-12">
            <div class="row collapse postfix-round">
                <div class="small-8 columns">
                  <input type="text" id="searchstring" placeholder="Ник или телефон" value="">
                </div>
                <div class="small-4 columns">
                    <button class="button postfix round" onclick="searchToFriends()">Go</button>
                </div>
            </div>
        </div>
    </div>
    <div id="friends" class="small-12">
    <?php if ($this->_tpl_vars['friends']): ?>
            <?php $_from = $this->_tpl_vars['friends']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'userinfo.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <hr>
            <?php endforeach; endif; unset($_from); ?>
    <?php else: ?>
    <p class="grey_text">Найди новых друзей в Эфире</p>
    <br>
    <p><a href="onair.php" role="external" class="expand small round button">Посмотреть кто в Эфире</a></p>
    <?php endif; ?>
    </div>    
<?php else: ?>
    <pre><?php echo print_r($this->_tpl_vars['error']); ?>
</pre>
<?php endif; ?>

<?php echo '
<script>
    $(\'h1\').text(\'Друзья\');

    
</script>
'; ?>
