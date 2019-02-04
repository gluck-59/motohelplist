<?php
    session_start();
    include_once('../../config/config.inc.php');
    if (!isset($smarty))
	exit('No Smarty');

if ($_SESSION['id_user'])
{
  
    $smarty->assign('id_channel', $_GET['channel']);
    if ($_GET['id_to'])
      $smarty->assign('id_to', $_GET['id_to']);
    else $smarty->assign('id_to', 0); 
    $smarty->display('header.tpl');

//include_once('onair-ajax.php');

    $smarty->display('chat.tpl');
    $smarty->display('footer.tpl');    
}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}

?>




