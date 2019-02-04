<?php
    session_start();
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    if (!isset($smarty))
	exit('No Smarty');

if ($_SESSION['id_user'])
{
    $smarty->display('header.tpl');
//    $smarty->assign('channels', $channels);
    $smarty->display('help.tpl');
    $smarty->display('footer.tpl');    
}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}

?>

