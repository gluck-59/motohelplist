<?php
include_once('../../config/config.inc.php');

//echo '<pre>';
//print_r($_POST); 

if ($_POST['phone'] AND $_POST['password']  AND $_POST['id_city'])
{
    $name = ( $_POST['name'] ? $_POST['name'] : '0' );
    $gender = intval($_POST['gender']);
    $phone = preg_replace("/[^0-9]/", '', trim($_POST['phone']) );
    $password = $_POST['password'];
    $id_city = intval($_POST['id_city']);
    $ip = $_SERVER['HTTP_X_REAL_IP'];
    $code = $_POST['code'];
    $id_sms = $_POST['id_sms'];
//echo "SELECT id_sms FROM sms WHERE code = \"$code\" AND phone = \"$phone\" "; 
    $stmt = $pdo->query("SELECT id_sms FROM sms WHERE code = \"$code\" AND phone = \"$phone\" ");
    $passed = $stmt->fetchColumn(); 
    
    if ($passed)
    {
        //! пишем юзера в базу
        $stmt = $pdo->prepare('INSERT INTO `users`(`name`, `id_city`, `phone`, `password`, `gender`, `ip_last_login`, `date_last_login`, `date_add`, `date_upd`) VALUES (:name, :id_city, :phone, :password, :gender, :ip_last_login ,NOW(), NOW(), NOW() )');
        $stmt->execute(array(
        ':name' => $name, 
        ':id_city' => $id_city, 
        ':phone' => $phone, 
        ':password' => $password, 
        ':gender' => $gender,  
        ':ip_last_login' => $ip
        ));
        
        // создали юзера — запроcим его id (который auto increment и заранее его не узнать)
        $stmt = $pdo->prepare("SELECT id_user FROM users WHERE phone = ?");
        $stmt->execute(array($phone));
        $id_user = $stmt->fetchColumn();
        
        // узнаем есть ли канал его города 
        $stmt = $pdo->query("SELECT id_channel FROM webchat_channels WHERE type_channel = 1 AND id_object = $id_city ");
        $id_channel = $stmt->fetchColumn();
        if (!$id_channel)
        {
            $stmt = $pdo->query("INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object) values (1, $id_city)");
            $id_channel = $pdo->lastInsertId();
        }
        // добавим подписку на канал его города
        $stmt = $pdo->query("INSERT LOW_PRIORITY INTO webchat_subscribe (id_user, id_channel, date_add) values ($id_user, $id_channel, NOW())");
        
        // подсунем куку с id юзера и авторизуем его
        session_start();
        $_SESSION['id_user'] = $id_user;
        header('Location: /profile.php', true, 303); 
    }
    else
    {
        header( 'Location: /index.php', true, 303 ); 
    }
//    else header('Location: /profile.php', true, 303); 
}

else
{
//    echo '<br>registration.php ELSE стр 48';
    header( 'Location: /index.php', true, 303 ); 
    exit('Что-то пошло не так...');
}   


?>