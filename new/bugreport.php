<?php
//    include_once('../../config/config.inc.php');
    error_reporting(E_ERROR);

/*
echo('<pre>');
print_r($_POST);
print_r($_FILES);
die;
*/

if ( isset($_POST['bug_description']) OR isset($_POST['screenshot']))
{
    // готовим мыло
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd"><html><body>');
    $to = 'info@motohelplist.com';
    $subject = 'Баг в Motohelplist';
    
    // отправим мне мыло
    $message = urldecode($_POST['bug_description'])."\r\n<br>".urldecode($_POST['debug']).urldecode($_POST['dbinfo'])."\r\n<br>";

    if ($_FILES['screenshot'])
    {
        $uploaddir = 'upload/';
        $uploadfile = $uploaddir . rawurlencode(basename($_FILES['screenshot']['name']));
       
        if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadfile)) 
        {
            $message .= $_POST['screenshot'];
            $message .='<p><img style="height:400px" src="//app.motohelplist.com/'.$uploadfile.'"></p>';
            $message .= 'Скриншот: app.motohelplist.com/'.$uploadfile;            
        } else {
// выведется в морду юзеру 
        }
    }
    
    if (mail($to, $subject, $message, $headers))
        echo 'ok';

}

//    $bugreport[] = $_SERVER;
//    $bugreport[] = $_SESSION;
//    $bugreport = print_r($bugreport, true);
    

else
{
    header( 'Location: /index.html', true, 303 );
    exit();    
}



?>