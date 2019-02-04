<?php
    session_start();
    include_once('../../config/config.inc.php');
include_once('functions.php');
    if (!isset($smarty))
	exit('No Smarty');


if ($_SESSION['id_user'])
{
        // ищем все, на что подписан этот юзер
        //$id_channel = $_GET['id_channel'];
        //($_COOKIE['language'] ? $lang = $_COOKIE['language'] : $lang = 'en');
        $stmt = $pdo->query('SELECT wc.type_channel, wc.id_channel, COALESCE(cities.name, mc.name, wc.name) AS channel_name, 1 as is_subscribe
        FROM webchat_subscribe ws
        LEFT JOIN webchat_channels wc ON wc.id_channel = ws.id_channel
        left join cities on wc.type_channel=1 AND wc.id_object = cities.id_city 
        left join motorcycles mc on wc.type_channel=2 AND wc.id_object = mc.id_motorcycle
        left join users us on wc.type_channel=3 AND wc.id_object = us.id_user
        WHERE ws.id_user = '.$_SESSION['id_user'].'
        GROUP BY wc.id_channel
        ORDER BY ws.date_add DESC
        ');
        $channels = $stmt->fetchAll();

/*
        while ($row = $stmt->fetchColumn() )
        {
            $result []= $row;
        }
        $subscribes = implode(",", $result);

        if ($subscribes)
        {       
            $stmt = $pdo->query('
')

              $stmt = $pdo->query('select webchat_lines.id_from as id_user, webchat_lines.id_channel, webchat_lines.id_urgency as id_urgency, webchat_urgency.name_en as urgency, webchat_lines.text, webchat_lines.ts, users.name, users.phone, users.gender, cities.name_'.$lang.' as city, 1 as is_subscribe
            from webchat_lines
            join users on webchat_lines.id_from = users.id_user
            join cities on users.id_city = cities.id_city
            join webchat_urgency on webchat_urgency.id_urgency = webchat_lines.id_urgency
            where webchat_lines.id_channel in('.$subscribes.')
            group by webchat_lines.id_channel    
            order by ts DESC
            ');
 
            $stmt = $pdo->query('select webchat_lines.id_from as id_user, webchat_lines.id_channel, webchat_lines.id_urgency as id_urgency, webchat_urgency.name_en as urgency, webchat_lines.text, webchat_lines.ts, users.name, users.phone, users.gender, cities.name_'.$lang.' as city, 1 as is_subscribe
            from webchat_lines
            join users on webchat_lines.id_from = users.id_user
            join cities on users.id_city = cities.id_city
            where webchat_lines.id_channel in('.$subscribes.')
            group by webchat_lines.id_channel    
            order by ts DESC
            ');
            $channels = $stmt->fetchAll();
        }
    }
  */  
  
    if ($_POST['subscribe'] AND !$_POST['unsubscribe'])
    {
        $id_user = $_SESSION['id_user'];
        $id_channel = $_POST['subscribe'];
        subscribe($id_user, $id_channel);
        echo $id_channel;
        exit;
    }

    if ($_POST['unsubscribe'] AND !$_POST['subscribe'])
    {
        $id_user = $_SESSION['id_user'];
        $id_channel = $_POST['unsubscribe'];
        unsubscribe($id_user, $id_channel);
        echo $id_channel;        
        exit;
    }
    
  
    $smarty->display('header.tpl');    
    $smarty->assign('channels', $channels);
    $smarty->display('subscribe.tpl');
    $smarty->display('footer.tpl');    
}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}






function unsubscribe($id_user, $id_channel)
{
    global $pdo;
    // отпишем юзера от "непрочитанных" в этом канале
    $stmt = $pdo->prepare('SELECT id FROM `webchat_lines` where `id_channel` = :id_channel');
    $stmt->execute(array(':id_channel' => $id_channel));    
    while ($row = $stmt->fetch() )
    {
        $rows[] = $row->id;
    }
    if ($rows)
    {
        $lines = implode(",", $rows);
        $stmt = $pdo->prepare('DELETE LOW_PRIORITY FROM `webchat_unreaded` WHERE id_user = :id_user AND id_lines IN('.$lines.')');
        $stmt->execute(array(':id_user' => $id_user));
    }
    
    // отпишем юзера от собственно каналв
    $stmt = $pdo->prepare('DELETE LOW_PRIORITY FROM `webchat_subscribe` WHERE id_user = :id_user AND id_channel = :id_channel');
    $stmt->execute(array(':id_user' => $id_user, ':id_channel' => $id_channel));
    
    // запишем в базу +1
    plusOne('unsubscribe');    
    return $stmt;
}

function subscribe($id_user, $id_channel)
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `webchat_subscribe` (id_user, id_channel, date_add) values(:id_user, :id_channel, NOW()) ');
    $stmt->execute(array(':id_user' => $id_user, ':id_channel' => $id_channel));

    // запишем в базу +1
    plusOne('subscribe');        
    return $stmt;
}


?>




