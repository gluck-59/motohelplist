<?php
    session_start();

    error_reporting(E_ALL);
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    if (!isset($smarty))
	exit('Smarty умер');

if ($_SESSION['id_user'])
{
    if ($_POST['unfriend'])
    {
        $friend = preg_replace("/[^0-9]/", '', trim($_POST['unfriend']) );
        // определим юзера
        $user = getUser($_SESSION['id_user']);
        // узнаем есть ли у него такой френд в принципе
        $friends = getFriendsId($user->id_user);
        $friend_exist = in_array($friend, $friends);
        // если нету, вернем шиш
        if (!$friend_exist)
            exit('Этого человека уже нет среди твоих друзей');
        // если есть, отфрендим
        else
        {
//            $user_name = getUserName($user->id_user);
            $friend_name = getUserName($friend);

// запись в новую таблицу            
$stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed_new` (id_user, type_feed, id_object) VALUES (:id_user, 7, :id_friend)');
$stmt->execute(array(':id_friend' => $friend, ':id_user' => $user->id_user));
                        
            $text = '…удалил из друзей <a href="profile.php?userprofile='.$friend.'&" role="external">'.addslashes($friend_name).'</a>';
            $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, text, icon) VALUES (:id_user, :text, "status")');
            $stmt->execute(array(':text' => $text, ':id_user' => $user->id_user));
    
            unfriend($user, $friend);
            echo $friend;
        }
    exit;
    }



    
    if ($_POST['tofriend'])
    {
        $friend = preg_replace("/[^0-9]/", '', trim($_POST['tofriend']) );
        // определим юзера
        $user = getUser($_SESSION['id_user']);
        // узнаем есть ли у него такой френд в принципе
        $friends = getFriendsId($user->id_user);
        $friend_exist = in_array($friend, $friends);
        // если есть, вернем алерт
        if ($friend_exist)
            exit('Этот человек уже среди твоих друзей');
        // есои нет, добавим            
        else
        {
            

// запись в новую таблицу            
$stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed_new` (id_user, type_feed, id_object) VALUES (:id_user, 6, :id_friend)');
$stmt->execute(array(':id_friend' => $friend, ':id_user' => $user->id_user));
            
//            $user_name = getUserName($user->id_user);
            $friend_name = getUserName($friend);
            $text = '…добавил в друзья <a href="profile.php?userprofile='.$friend.'&" role="external">'.addslashes($friend_name).'</a>';
            $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, text, icon) VALUES (:id_user, :text, "status")');
            $stmt->execute(array(':text' => $text, ':id_user' => $user->id_user));
    
            tofriend($user, $friend);
            echo $friend;
        }
    exit;
    }
    
    
    if ($_POST['searchToFriends'])
    {
        // найдем людей по запросу юзера
        $string = $_POST['searchToFriends'];
        $friends = searchToFriends($string);
        
        // выведем найденных аяксом
        if (!$friends) exit('notfound'); // не изменять "notfound"
        foreach ($friends as $friend)
        {
            $smarty->assign('friend', $friend);    
            $smarty->display('searchtofriends.tpl');
        }
        exit;
    }
    
    if ($_POST['addFriendsToChannel'])
    {
        // найдем всех френдов юзера
        $friends = getFriends(intval($_POST['addFriendsToChannel']));
        foreach ($friends as $friend)
        {
            $friend->task = 'addFriendsToChannel';
            $smarty->assign('friend', $friend);    
            $smarty->display('searchtofriends.tpl');
        }
        exit;
    }



    // если нет POST или GET, покажем список друзей
    $friends = getFriends($_SESSION['id_user']);
    $smarty->assign('friends', $friends);    
}

else
{
    //    $error = array('error' => 'нет session', 'session' => $_SESSION);
    //    $smarty->assign('error', $error);
    header( 'Location: /login.html', true, 303 );     
    exit();    
}


// выведем стр полностью и покажем все что нашлось
$smarty->display('header.tpl');
$smarty->display('friends.tpl');    
$smarty->display('footer.tpl');    
    






?>