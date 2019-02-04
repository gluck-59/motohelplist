<?php
    session_start();
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    if (!isset($smarty))
	exit('No Smarty');

if (!$_SESSION['id_user'])
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}

    $smarty->display('header.tpl');
    $smarty->display('settings.tpl');
    $smarty->display('footer.tpl');    

?>




