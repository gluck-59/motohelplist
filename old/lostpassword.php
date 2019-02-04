<?php
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    if (!isset($smarty))
	exit('Smarty умер');

//print_r($_GET);
//print_r($_POST);


    if ($_GET['phone'])
    {
        plusOne('lostpassword');
        $phone = preg_replace("/[^0-9]/", '', trim($_GET['phone']) );
        // подключим смс-шлюз и отправим юзеру смску
        $code = rand(1000, 9999);
        include('sms.php'); //////////////////////////////// отправка смс тут
//$send = 'accepted;A133541BC';
        $sms_data = explode(';', trim($send));
        if ($send != false)      
        {
            $stmt = $pdo->query("select `id_sms` from `sms` where `phone` = \"$phone\" ");
            $id_sms = $stmt->fetchColumn();
//echo '$id_sms = '.$id_sms;            
    
            if ($id_sms) 
            {
                $stmt = $pdo->prepare("UPDATE `sms` SET `id_sms_gate`=:id_sms_gate, `status`=:status, `code`=:code, `phone`=:phone, `ip`=:ip, `date_sms`= NOW() WHERE `id_sms`=\"$id_sms\"");
            }
            else 
            {
                $stmt = $pdo->prepare('INSERT INTO `sms`(`id_sms_gate`, `phone`, `status`, `code`, `IP`, `date_sms`) VALUES (:id_sms_gate, :phone, :status, :code, :ip, NOW() )');
            }
            $stmt->execute(array(
            ':id_sms_gate' => $sms_data[1], 
            ':phone' => $phone, 
            ':status' => $sms_data[0], 
            ':code' => $code,
            ':ip' => $_SERVER['REMOTE_ADDR']
            ));
        }
        else
        {
            echo '$send == false';
        }
        
    

    include('lostpassword.html');
        exit('<script>
        $("[name=phone]").val("'.$_GET['phone'].'"); 
        $("[name=id_sms]").val("'.$sms_data[1].'");        
        $("#usersession").val("'.$code.'");
        </script>');
        
}

// юзер прислал новый пароль
if ($_POST['phone'] and $_POST['password'] and $_POST['code'])
{
    $phone = preg_replace("/[^0-9]/", '', trim($_POST['phone']) );
    $code = $_POST['code'];
    $password = $_POST['password'];
   
        // ...ищем такой телефон в базе
        $stmt = $pdo->query("select users.id_user, users.phone, sms.code from users 
        join sms on (users.phone = sms.phone)
        where users.phone = \"$phone\" ");    
        $user = $stmt->fetch();
        
        if ($code == $user->code and $phone == $user->phone)
        {
            // пишем новый пароль, ну и в блокнотик запишем
            global $pdo;
            $stmt = $pdo->prepare('UPDATE `users` SET `password` = :password, `ip_last_login` = :ip_last_login, `date_last_login`= NOW() WHERE `id_user` = '.$user->id_user);
            $stmt->execute(array(
            ':ip_last_login' => $_SERVER['REMOTE_ADDR'],
            ':password' => $password
            ));
            
            session_start();

            include('onair.php');
            // и перед пропуском запишем пароль в LocalStorage устройства для автономной работы
            exit("<script>localStorage.setItem('usersessionhelplistid', '$password')</script>");                        
//            header( 'Location: /onair.php', true, 303 ); низзя header
            
                        
// заходим с новыми параметрами            
// какого-то хуя POST не отправляется
/*
$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => '/index.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(array('phone' => $phone, 'password' => $password))
));
$response = curl_exec($myCurl);
curl_close($myCurl);

print_r($response);
*/

        }
        else
        {
            header( 'Location: /login.php', true, 303 ); 
        }
        

}
    
?>