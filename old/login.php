<?php
session_start();
include_once('../../config/config.inc.php');

// Extend cookie life time by an amount of your liking
$cookieLifetime = 365 * 24 * 60 * 60; // A year in seconds
setcookie(session_name(),session_id(),time()+$cookieLifetime);
//echo 'SESSION id_user = '.$_SESSION['id_user'].'<br>';
//print_r($_POST);
//print_r($_GET);
//print_r($_COOKIE);



if ($_POST['phone'] AND $_POST['password'])
{
    $phone = preg_replace("/[^0-9]/", '', trim($_POST['phone']) );
    $password = $_POST['password'];
   
    // если это цифры то...
    if (is_numeric($phone) /*AND strlen($phone) == 11 */)
    {
        // ...ищем такой телефон в базе
        $stmt = $pdo->query("select id_user, phone, password, deleted from users where phone = \"$phone\" ");    
        $user = $stmt->fetch();
    }
    else // иначе нахуй... к терапевту
    {
        header( 'Location: https://app.motohelplist.com/old/login.php', true, 303 ); 
    }
        
    
    if ($user) // если юзер существует в базе и не забанен, проверяем пароль 
    {
        if ($password == $user->password AND $user->deleted == 0 )
        {
            // порядок, пропускаем
            $_SESSION['id_user'] = $user->id_user;
            setcookie('id_user',$user->id_user,time()+$cookieLifetime);
            header( 'Location: https://app.motohelplist.com/old/onair.php?token='.time(), true, 303 ); // оригинал
//            header( 'Location: https://app.motohelplist.com/old/onair.php', true, 303 );

            
            // ну и в блокнотик запишем
            $stmt = $pdo->prepare('UPDATE `users` SET `ip_last_login` = :ip_last_login, login_count = login_count + 1, `date_last_login`= NOW() WHERE `id_user` = '.$user->id_user);
            $stmt->execute(array(':ip_last_login' => $_SERVER['HTTP_X_REAL_IP']));
        }
        else // тел есть, пароль неправильный
        {
            include('login.html');
            echo '<script>$("#nopassword").html(\'Забыл пароль? <a href="lostpassword.php?phone='.$user->phone.'">Восстанови</a>  (придется ждать СМС).\')</script>';
            exit('<script>$("#passwd").addClass("error");$("[name=phone]").val("'.$user->phone.'")</script>');
        }
        
    }
    else // такого телефона в базе нет = новый юзер
    {
        include('registration.html');
        
        
        
        // подключим смс-шлюз и отправим юзеру смску
        $code = rand(1000, 9999);            
$delivered = $pdo->query("SELECT status from `sms` where `phone` = \"$phone\" ");            
$delivered = $delivered->fetchColumn();
if ($delivered != 'delivered')
{
    include('sms.php'); //  отправка смс тут  ////////////////////////////////////////////////////////////////////// 
}



//$send = 'accepted;A133541BC';
        $sms_data = explode(';', trim($send));
        if ($send != false)      
        {
            $id_sms = $pdo->query("SELECT `id_sms` from `sms` where `phone` = \"$phone\" ");    
            $id_sms = $id_sms->fetchColumn();
//echo '$id_sms = '.$id_sms;            
    
            if ($id_sms) 
            {
                $stmt = $pdo->prepare("UPDATE `sms` SET `id_sms_gate`=:id_sms_gate, `status`=:status, `code`=:code, `phone`=:phone,  `ip`=:ip, `date_sms`= NOW() WHERE `id_sms`=\"$id_sms\"");
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
            ':ip' => $_SERVER['HTTP_X_REAL_IP']
            ));
        }
        else
        {
            echo 'Не удалось отправить СМС, ошибка связи. Попробуй еще раз.';
        }
            
        exit('<script>
        $("#unregistered").removeClass("hide"); 
        $("[name=phone]").val("'.$_POST['phone'].'"); 
        $("[name=password]").val("'.$_POST['password'].'");
        $("[name=id_sms]").val("'.$sms_data[1].'");        
        $("#usersession").val("'.$code.'");

        </script>');
        
    }
}
else
{
    header( 'Location: https://app.motohelplist.com/old/registration.php', true, 303 ); 
}
