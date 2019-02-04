<?php
    session_start();
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    if (!isset($smarty))
	exit('No Smarty');

if ($_SESSION['id_user'])
{
    if ($_POST['postEvent']) 
    {
        if (getUserName($_SESSION['id_user']) == '0')
        {
            exit('nonick');
        }
        else
        {
            // запись в новую таблицу
            $stmt = $pdo->prepare('INSERT INTO `feed_new` (id_user, type_feed, text) values(:id_user, 5, :text)');
            $stmt->execute(array(':id_user' => $_SESSION['id_user'], ':text' => $_POST['postEvent']));

            // запись в старую таблицу
            $stmt = $pdo->prepare('INSERT INTO `feed` (id_user, text) values(:id_user, :text)');
            $stmt->execute(array(':id_user' => $_SESSION['id_user'], ':text' => $_POST['postEvent']));
            $newPostEvent = $pdo->lastInsertId();
            $stmt = $pdo->query('SELECT * FROM `feed` WHERE id_feed = '.$newPostEvent);
            
            $feed2 = $stmt->fetch();

            $friend = getUser($feed2->id_user);
            $friend->text = $feed2->text;
            $friend->icon = $feed2->icon;    
            $friend->id_feed = $feed2->id_feed;    

            $friend->ts = $feed2->date;    
       
 

/*
echo '<pre>';
print_r($feed2);die;
echo '</pre>';
*/

            $smarty->assign('feed', $friend);

            $html = $smarty->fetch('blockfeed.tpl');
            exit($html);
            //exit('success');
        }
    }

    // подтянем профиль юзера чтобы узнать город и мотоцикл
    $user = getUser($_SESSION['id_user']);

    // найдем всех его френдов
    $friends = getFriendsId($_SESSION['id_user']);
    $friends = implode(",", $friends);
//    ($friends == NULL ? $friends = 0 : '');
    ( $_POST['limit'] ? $limit = $_POST['limit'] : $limit = 0 );    

    // юзер номер 0 - это сообщение сервера
    try {
        $stmt = $pdo->query('SELECT *, UNIX_TIMESTAMP(date) as date FROM `feed` 
        WHERE `to_city` = '.$user->id_city.'
        '.($friends == NULL ? '' : ' OR `id_user` in('.$friends.')').'
        OR `to_motorcycle` = '.$user->id_motorcycle.'
        OR (`id_user` = 0 AND `to_city` = 0 AND `to_motorcycle` = 0)
        OR `id_user` = '.$_SESSION['id_user'].'
        ORDER BY `date` DESC
        limit '.$limit.','.($limit == 0 ? 10 : $limit).'
        ');
        $feeds1 = $stmt->fetchAll();
    }
    catch(PDOException $e) 
    {  
        // соберем информацию и отправим мне мыло
        $message = array(
            'PDOerror' => $e,
            'user' => $user,
            'friends' => $friends,
            'limit' => $limit
        );
       sendreport($message);

        // обработаем ошибку и перенаправим юзера
        file_put_contents('-PDOErrors.txt', $e->getMessage(), FILE_APPEND);  
        header( 'Location: /onair.php', true, 303 );     
        exit();        
//        exit ("Хьюстон, у нас проблемы...");
    }

    
    $feeds = array();    
    foreach ($feeds1 as $feed)
    {
        $friend = getUser($feed->id_user);
        $friend->text = str_replace("\r\n","<br>",$feed->text);
        $friend->icon = $feed->icon;    
        $friend->ts = $feed->date;
        $friend->id_feed = $feed->id_feed;
        $feeds[] = $friend;
    }


    if ($_POST['limit']) 
    {
        $smarty->assign('feeds', $feeds);
        $smarty->assign('loadmore', 1);
        $smarty->display('feed.tpl');
        exit;
    }
    



    $smarty->display('header.tpl');

    $smarty->assign('feeds', $feeds);
    $smarty->display('feed.tpl');

    $smarty->display('footer.tpl');    
}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}


?>




