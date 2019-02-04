<?php
    session_start();
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    if (!isset($smarty))
	exit('No Smarty');

if ($_SESSION['id_user'])
{
    
    //! поиск канала по городу
    if ($_GET['task'] == 'find_channel')
    {
        if ($_GET['id_city'])
        {
            $stmt = $pdo->query('SELECT wc.type_channel, wc.id_channel, cities.name AS channel_name,  IF(ws.id_user, 1, 0) as is_subscribe 
            FROM webchat_channels wc
            join cities on wc.id_object = cities.id_city
            left join webchat_subscribe ws on ws.id_channel = wc.id_channel AND ws.id_user = '.$_SESSION['id_user'].'
            WHERE wc.type_channel = 1
            AND wc.id_object = '.$_GET['id_city'].'
            ');
            $channels = $stmt->fetchAll();
            if (!$channels) exit ('Такого канала пока нет. Возможно он появится в будущем.');
            
            $smarty->assign('channels', $channels);
            $smarty->display('subscribe.tpl');
        die;
        }



        //! поиск канала по мотоциклу
        if ($_GET['id_motorcycle'])
        {
            $stmt = $pdo->query('SELECT wc.type_channel, wc.id_channel, mc.name as channel_name,  
            IF(ws.id_user, 1, 0) as is_subscribe 
            FROM webchat_channels wc
            join motorcycles mc on wc.id_object = mc.id_motorcycle
            left join webchat_subscribe ws on ws.id_channel = wc.id_channel AND ws.id_user = '.$_SESSION['id_user'].'
            WHERE wc.type_channel = 2
            AND wc.id_object = '.$_GET['id_motorcycle'].'
            ');
            $channels = $stmt->fetchAll();
            if (!$channels) exit ('Такого канала пока нет');
                        
            $smarty->assign('channels', $channels);
            $smarty->display('subscribe.tpl');
        die;
        }
    
    
        //! поиск канала по имени канала
        if ($_GET['id_channel'])
        {
            $stmt = $pdo->query('SELECT wc.type_channel, wc.id_channel, wc.name as channel_name,  IF(ws.id_user, 1, 0) as is_subscribe 
            FROM webchat_channels wc
            left join webchat_subscribe ws on ws.id_channel = wc.id_channel AND ws.id_user = '.$_SESSION['id_user'].'
            WHERE wc.type_channel != 3 AND wc.type_channel != 0
            AND wc.id_channel = '.$_GET['id_channel'].'
            ');
            $channels = $stmt->fetchAll();
            if (!$channels) exit ('Такого канала пока нет');
                        
            $smarty->assign('channels', $channels);
            $smarty->display('subscribe.tpl');
        die;
        }
    }
    
        


    //! создание приватного чат-канала
    if ($_POST['task'] == 'add_channel' AND $_POST['type_channel'] == 'private')
    {
        // создаем новый канал тип 3 PRIVATE
        $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object, name) values (3, :id_object, :name)');
        $stmt->execute(array(':id_object' => $_SESSION['id_user'], ':name' => $_POST['name_channel']));
        $id_channel = $pdo->lastInsertId();
        
        // пишем там приветствие
        $text = getUserName($_SESSION['id_user']).' создал приватный канал';
        
        // добавим координаты если разрешено
        if (intval($_POST['allowgps']) == 1)
            $text .= '['.$_POST['lat'].','.$_POST['lng'].']';

        $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO webchat_lines (id_channel, id_from, text) values (:id_channel, :id_from, :text)');
        $stmt->execute(array(
        ':id_channel' => $id_channel,
        ':id_from' => $_SESSION['id_user'],
        ':text' => $text
        ));
        
        // подписываем всех участников на этот канал
        $invites = explode(',', $_POST['invitefriends']);
        $invites[] = $_SESSION['id_user'];
        
        $query_subscribe = 'INSERT LOW_PRIORITY INTO `webchat_subscribe`
        (`id_user`, `id_channel`, date_add) VALUES ';
        
        for ($i = 0; $i < count($invites); $i++) 
        { 
            $query_subscribe .= "($invites[$i],$id_channel,NOW())";
            if ($i < count($invites)-1) $query_subscribe .= ',';
        }
        $stmt = $pdo->query($query_subscribe);
        
        header( 'Location: /chat.php?channel='.$id_channel.'&', true, 303 );     
        exit();
    }


    //! создание группового чат-канала с параметром "urgency"
    if ($_POST['task'] == 'add_channel' AND $_POST['type_channel'] == 'group')
    {
        // создаем новый канал тип 4 GROUP
        $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object, name, id_urgency) values (4, :id_object, :name, :id_urgency)');
        $stmt->execute(array(':id_object' => $_SESSION['id_user'], ':name' => $_POST['name_channel'], ':id_urgency' => intval($_POST['urgency'])));
        $id_channel = $pdo->lastInsertId();        

        // пишем там приветствие
        if (intval($_POST['urgency']) == 0)
            $text = getUserName($_SESSION['id_user']).' создал чат-канал';
        else            
            $text = getUserName($_SESSION['id_user']).' запрашивает помощь.';
            
        // добавим координаты если разрешено
        if (intval($_POST['allowgps']) == 1)
        {
            if ($_POST['lat'] && $_POST['lng'])
                $text .= '['.$_POST['lat'].','.$_POST['lng'].']';
                
            else
                $text .= '<br>(координаты недоступны)';
        }

        $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO webchat_lines (id_channel, id_from, text) values (:id_channel, :id_from, :text)');
        $stmt->execute(array(
        ':id_channel' => $id_channel,
        ':id_from' => $_SESSION['id_user'],
        ':text' => $text
        ));

        header( 'Location: /chat.php?channel='.$id_channel.'&', true, 303 );     
        exit();        
    }        



    //! вывод дайджеста каналов
    // под конец ищем всех, кто последним отписался в публичные каналы
    // и выводим все последние мессаги всех нужных каналов на страницу
    ( $_COOKIE['language']  ?   $lang = $_COOKIE['language']    :   $lang = 'en' );
    ( $_POST['limit']       ?   $limit = $_POST['limit']        :   $limit = 0 );    

    $stmt = $pdo->query('
    SELECT DISTINCT wc.type_channel, wl.id_channel, wl.text as text, UNIX_TIMESTAMP(wl.ts) as ts, users.name, users.id_user, users.phone, users.gender, cities.name as city, mc.name as motorcycle, IF(ws.id_channel, 1, 0) as is_subscribe, wc.id_urgency, wu.name_'.$lang.' as urgency, wl.id as lineid,
/*wcu.unread_count,*/
    CASE
    	WHEN wc.type_channel = 1 THEN (SELECT name from cities WHERE id_city = wc.id_object)
    	WHEN wc.type_channel = 2 THEN (SELECT mc.name FROM motorcycles mc WHERE mc.id_motorcycle = wc.id_object)
    	WHEN wc.type_channel = 3 THEN (SELECT wc.name) 
    	WHEN wc.type_channel = 4 THEN (SELECT wc.name)     	
    	END
    	as channel_name
        
        FROM webchat_lines wl
        INNER JOIN webchat_channels wc on wc.id_channel = wl.id_channel
        INNER JOIN users on users.id_user = wl.id_from
        INNER JOIN cities on users.id_city = cities.id_city
        INNER JOIN motorcycles mc on users.id_motorcycle = mc.id_motorcycle 
        INNER JOIN webchat_urgency wu on wc.id_urgency = wu.id_urgency
        LEFT JOIN webchat_subscribe ws on (ws.id_channel =  wl.id_channel AND ws.id_user = '.$_SESSION['id_user'].')
/*
LEFT JOIN (SELECT count(wcu.id_lines) as unread_count, wcl.id_channel FROM webchat_unreaded wcu join webchat_lines wcl on wcu.id_lines = wcl.id
where wcu.id_user = '.$_SESSION['id_user'].' group by wcl.id_channel) wcu on wcu.id_channel=wc.id_channel  
  */      
        
        WHERE wl.id IN
        	(SELECT MAX(id)
        	FROM webchat_lines wl
    
    /* здесь все каналы кроме чужих GROUP 
        	WHERE wc.type_channel != 3 */
    
    /* а здесь только "подписанные" каналы
            WHERE wl.id_channel IN
        		(SELECT id_channel
        		FROM webchat_subscribe ws
        		WHERE ws.id_user = '.$_SESSION['id_user'].') 
    */
    
            GROUP BY wl.id_channel)
    and (IF(wc.type_channel=3, 1, 0) *IF(ws.id_channel, 0, 1))!=1
    '.($_GET['sosAirOnly'] == 1 ? ' AND wc.id_urgency > 0' : '').'  
    order by wc.id_urgency DESC, wl.ts DESC
    limit '.$limit.','.($limit == 0 ? 10 : $limit).'
    ');
    $channels = $stmt->fetchAll();

    if ($_POST['limit']) 
    {
        $smarty->assign('channels', $channels);
        $smarty->display('onair.tpl');
        exit;
    }

    $smarty->display('header.tpl');
    $smarty->assign('channels', $channels);
    $smarty->display('onair.tpl');
    $smarty->display('footer.tpl');    
}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}

?>




