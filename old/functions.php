<?php
   
/*************
/*
/* FRIENDS    
/*            
************/

function unfriend($user, $friend)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM `friends` WHERE id_user = :user AND id_friend = :friend ');
    $stmt->execute(array(':user' => $user->id_user, ':friend' => $friend));
    plusOne('unfriend');    
    return $stmt;
}

function tofriend($user, $friend)
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO `friends` (id_user, id_friend) values(:user, :friend)');
    $stmt->execute(array(':user' => $user->id_user, ':friend' => $friend));
    plusOne('tofriend');    
    return $stmt;
}

function getFriends($id)
{
    global $pdo;
    $stmt = $pdo->query('SELECT id_friend FROM friends WHERE id_user = '.$id.' order by date_add desc');
    while ($row = $stmt->fetchColumn() )
    {
        $friend = getUser($row);
        $result[] = $friend;
    }
    return $result;
}    


function getFriendsId($id)
{
    global $pdo;
    $stmt = $pdo->query('SELECT id_friend FROM friends WHERE id_user = '.$id);
    while ($row = $stmt->fetchColumn() )
    {
        $result[] = $row;
    }
    return $result;
}    


function searchToFriends($string)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE name like :name OR phone like :phone /*LIMIT 0,30*/");
    $stmt->execute(array(':name' => '%'.$string.'%', ':phone' => '%'.$string.'%'));
    while ($row = $stmt->fetchColumn() )
    {
        if ($row == $_SESSION['id_user']) continue;
        $result[] = getUser($row);
    }
    plusOne('searchToFriends');
    return $result;
}    

/*  END OF FRIENDS */   


function getUser($id)
{
    global $pdo;
    //($_COOKIE['language'] ? $lang = $_COOKIE['language'] : $lang = 'en');
    $stmt = $pdo->query('SELECT users.id_user, users.name, users.status, users.gender, cities.name as city, users.id_city, users.phone, users.help_repair, users.help_garage, users.help_food, users.help_bed, users.help_beer, users.help_strong, users.help_party, users.help_excursion, users.description, UNIX_TIMESTAMP(users.date_last_login) as ts, friends.id_friend as is_friend, motorcycles.name as motorcycle, users.motorcycle_more, motorcycles.id_motorcycle
    FROM users 
    LEFT JOIN cities ON users.id_city = cities.id_city
    LEFT JOIN motorcycles ON motorcycles.id_motorcycle = users.id_motorcycle
    LEFT JOIN friends ON (users.id_user = friends.id_friend AND friends.id_user = '.$id.')
    WHERE users.id_user = '.$id
    );
    $user = $stmt->fetch();
    return $user;
}  


function getUserName($id)
{
    global $pdo;  
    $stmt = $pdo->prepare("SELECT name FROM users WHERE id_user = :id_user");
    $stmt->execute(array(':id_user' => $id));
    $name = $stmt->fetchColumn();
    return $name;    
}    

function getCityId($city, $region)
{
    //($_COOKIE['language'] ? $lang = $_COOKIE['language'] : $lang = 'en');
    global $pdo;  
    $stmt = $pdo->prepare("SELECT id_city FROM cities WHERE name like :city AND region like :region");
    $stmt->execute(array(':city' => $city, ':region' => $region.'%'));
    $id_city = $stmt->fetchColumn();
    
    // если ничего не найдено, отрежем регион и поищем по одному городу (должно помочь с другими странами)
    if ($id_city == NULL or $id_city == '')
    {
        $stmt = $pdo->prepare("SELECT id_city FROM cities WHERE name like :city");
        $stmt->execute(array(':city' => $city));
        $id_city = $stmt->fetchColumn();
    }
    
    // если опять ничего не найдено, поищем по одному городу И по языку 'en'
    if ($id_city == NULL or $id_city == '')
    {
        $stmt = $pdo->prepare("SELECT id_city FROM cities WHERE name like :city");
        $stmt->execute(array(':city' => $city));
        $id_city = $stmt->fetchColumn();
    }
    return $id_city;
} 

  
  
// отправляет мне мыло при ошибке
function sendreport($inputmessage)
{
    $message = '<pre>';
    $message .= print_r($inputmessage,true);

    $headers .= 'MIME-Version: 1.0' . "\r\n";
//    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
//    $headers .= ('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd"><html><body>');
    $to = 'info@motohelplist.com';
    $subject = 'Проблемы у юзера в Moto Helplist';
    mail($to, $subject, $message, $headers);
}    
    
// пишет в click_stat "+1" заданной кнопке
function plusOne($param)
{
    global $pdo;      
    $stmt = $pdo->prepare('INSERT INTO `click_stat` (`url`, `count`) VALUES (:url, `count`+1) ON DUPLICATE KEY UPDATE `count` = `count`+1');
    $stmt->execute(array(':url' => $param));
    return false;        
}
     

    
?>