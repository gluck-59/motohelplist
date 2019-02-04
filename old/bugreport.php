<?php
    session_start();
    include_once('../../config/config.inc.php');
    if (!isset($smarty))
	exit('No Smarty');
    error_reporting(E_ERROR);


if ( isset($_POST['bug_description']) )
{
    // готовим мыло
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd"><html><body>');
    $to = 'info@motohelplist.com';
    $subject = 'Баг в Motohelplist';
    
    // отправим мне мыло
    $message = $_POST['bug_description']."\r\n".$_POST['debug']."\r\n".$_POST['screenshot'];

    if ($_FILES['screenshot'])
    {
        $uploaddir = 'upload/';
        $uploadfile = $uploaddir . basename($_FILES['screenshot']['name']);
        
        if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadfile)) 
        {
            $message .='<p><img style="height:600px" src="//app.motohelplist.com/old/'.$uploadfile.'"></p>';
        } else {
// выведется в морду юзеру 
        }
    }
    
    mail($to, $subject, $message, $headers);

    $smarty->display('header.tpl');
    $smarty->display('bugreport.tpl');            
    $smarty->display('footer.tpl');        
}


if ($_SESSION['id_user'])
{
    $bugreport[] = $_SERVER;
    $bugreport[] = $_SESSION;
//    $bugreport = print_r($bugreport, true);
    
    $smarty->display('header.tpl');
    $smarty->assign('bugreport', $bugreport);
    $smarty->display('bugreport.tpl');            
    $smarty->display('footer.tpl');    
}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();    
}



?>