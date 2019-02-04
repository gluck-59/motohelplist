<?php
    session_start();
    include_once('../../config/config.inc.php');
    if (!isset($smarty))
	exit('Smarty умер');

if ($_SESSION['id_user'])
{
    $user = $_SESSION['id_user'];
    $profile = getUser($user);

    // если телефон из POST равен телефону этого юзера, то это верный запрос на редактирование профиля
    if ($_POST && $_POST['phone'] == $profile->phone) 
    {
        // для отображения смены мотоцикла в ленте
        ($_POST['id_motorcycle'] == NULL ? $new_id_motorcycle = $profile->id_motorcycle : $new_id_motorcycle = intval($_POST['id_motorcycle']) ); 
//        ($_POST['motorcycle_more'] == $profile->motorcycle_more ? $new_motorcycle_more = NULL : $new_motorcycle_more = $_POST['motorcycle_more'] );

        // для отображения смены города в ленте
        ($_POST['id_city'] == $profile->id_city ? $new_id_city = NULL : $new_id_city = $_POST['id_city']);

        // доделать смену пола и телефона
        $name = ( $_POST['name'] ? $_POST['name'] : '0' );
        $password = ( $_POST['password'] != '' ? $_POST['password'] : $profile->password );
        $id_city = ( $_POST['id_city'] ? intval($_POST['id_city']) : $profile->id_city );
        $id_motorcycle = ( $_POST['id_motorcycle'] ? intval($_POST['id_motorcycle']) : ($profile->id_motorcycle ? $profile->id_motorcycle : 1) );
        ($_POST['select_motorcycle'] == '' ? $id_motorcycle = 1 : $id_motorcycle = $id_motorcycle); // если поле "мотоцикл" придет пустое - сбрасываем мотоцикл на "пешком"
        $motorcycle_more = ( $_POST['motorcycle_more'] ? $_POST['motorcycle_more'] : '' );
        $help_repair = ( $_POST['help_repair'] == 'on' ? 1 : 0);
        $help_food = ( $_POST['help_food'] == 'on' ? 1 : 0);    
        $help_beer = ( $_POST['help_beer'] == 'on' ? 1 : 0);    
        $help_party = ( $_POST['help_party'] == 'on' ? 1 : 0);
        $help_garage = ( $_POST['help_garage'] == 'on' ? 1 : 0);    
        $help_bed = ( $_POST['help_bed'] == 'on' ? 1 : 0);        
        $help_strong = ( $_POST['help_strong'] == 'on' ? 1 : 0);            
        $help_excursion = ( $_POST['help_excursion'] == 'on' ? 1 : 0);               
        $description = $_POST['description'];
        $ip = $_SERVER['REMOTE_ADDR'];
    
        // обновляем юзера в базе
        $stmt = $pdo->prepare('UPDATE `users` SET
        `name`= :name,
        `password` = :password,
        `id_city` = :id_city,
        `id_motorcycle` = :id_motorcycle,        
        `motorcycle_more` = :motorcycle_more,
        `help_repair` = :help_repair,
        `help_food` = :help_food,
        `help_beer` = :help_beer,
        `help_party` = :help_party,
        `help_garage` = :help_garage,
        `help_bed` = :help_bed,
        `help_strong` = :help_strong,
        `help_excursion` = :help_excursion,
        `description` = :description,
        `ip_last_login` = :ip_last_login,
        `date_upd` = NOW() 
        WHERE `id_user` = '.$profile->id_user);
        $stmt->execute(array(
        ':name' => $name,
        ':password' => $password,        
        ':id_city' => $id_city,            
        ':id_motorcycle' => $id_motorcycle,                
        ':motorcycle_more' => $motorcycle_more,                        
        ':help_repair' => $help_repair,
        ':help_food' => $help_food,
        ':help_beer' => $help_beer,    
        ':help_party' => $help_party,        
        ':help_garage' => $help_garage,
        ':help_bed' => $help_bed,
        ':help_strong' => $help_strong,    
        ':help_excursion' => $help_excursion,        
        ':description' => $description,            
        ':ip_last_login' => $ip
        ));
       
        //! событие ленты "смена мотоцикла"
//        if (!$new_id_motorcycle == NULL OR !$new_motorcycle_more == NULL)
        {
            
            // если поменялось только описание мотоцикла
            ($new_id_motorcycle == NULL ? $new_id_motorcycle = $profile->id_motorcycle : '');

            $stmt = $pdo->prepare('SELECT motorcycles.name as cycle FROM motorcycles WHERE id_motorcycle = :id_motorcycle');
            $stmt->execute(array(':id_motorcycle' => $new_id_motorcycle));
            $new_motorcycle = $stmt->fetchColumn();
//            $feed = $new_motorcycle.' '.$motorcycle_more;
            $feed = $new_motorcycle;
            
            $stmt = $pdo->query("SELECT id_channel FROM webchat_channels WHERE type_channel = 2 AND id_object = $new_id_motorcycle ");
            $id_channel = $stmt->fetchColumn();
            if (!$id_channel)
            {
                $stmt = $pdo->query("INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object) values (2, $new_id_motorcycle)");
                $id_channel = $pdo->lastInsertId();
            }
            // добавим подписку на канал его мотоцикла
            $stmt = $pdo->query("INSERT IGNORE INTO webchat_subscribe (id_user, id_channel, date_add) values ($user, $id_channel, NOW())");
       

            // добавим само событие
            if ($_POST['id_motorcycle'] != $profile->id_motorcycle AND $_POST['id_motorcycle'] != NULL)
            {
                // для френдов
                $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, text, icon) VALUES (:id_user, :cycle, "cycle")');
                $stmt->execute(array(':id_user' => $user, ':cycle' => $feed));

// Запись в новую таблицу
$stmt = $pdo->prepare('INSERT INTO feed_new (id_user, type_feed, id_object) values(:id_user, 2, :cycle)');
$stmt->execute(array(':id_user' => $user, ':cycle' => $new_id_motorcycle));

                // для всех
                $text = '<a class="'.($profile->gender ? 'fe' :'').'male username" href="profile.php?userprofile='.$profile->id_user.'&" role="external">'.$profile->name.'</a> продал мотоцикл';                

                $text = 'Владельцы <a href="/chat.php?channel='.$id_channel.'&" role="external">'.$new_motorcycle.'</a> поздравляют <a class="'.($profile->gender ? 'fe' :'').'male username" href="profile.php?userprofile='.$profile->id_user.'&" role="external">'.$profile->name.'</a> с удачным приобретением';

                $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, to_motorcycle, text, icon) VALUES (0, :to_motorcycle, :text, "server")');
                $stmt->execute(array(':to_motorcycle' => $new_id_motorcycle, ':text' => $text));
            }
        }


        //! событие ленты "смена города"
        if (!$new_id_city == NULL)
        {
            //if ($_COOKIE['language']) $lang = $_COOKIE['language'];
            //else $lang = 'en';            
            $stmt = $pdo->prepare("SELECT CONCAT_WS(', ', cities.name, cities.region, countries.name_en) as city, cities.name as city_short
            FROM cities
            LEFT JOIN countries ON (cities.id_country = countries.id_country)
            WHERE cities.id_city = :id_city");
            $stmt->execute(array(':id_city' => $new_id_city));            
            $feed = $stmt->fetch();

            $stmt = $pdo->query("SELECT id_channel FROM webchat_channels WHERE type_channel = 1 AND id_object = $new_id_city ");
            $id_channel = $stmt->fetchColumn();
            if (!$id_channel)
            {
                $stmt = $pdo->query("INSERT LOW_PRIORITY INTO webchat_channels  (type_channel, id_object) values (1, $new_id_city)");
                $id_channel = $pdo->lastInsertId();
            }
            // добавим подписку на канал его города
            $stmt = $pdo->query("INSERT IGNORE INTO webchat_subscribe (id_user, id_channel, date_add) values ($user, $id_channel, NOW())");
            
            // для френдов
            $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, text, icon) VALUES (:id_user, :city, "city")');
            $stmt->execute(array(':id_user' => $user, ':city' => $feed->city));

// Запись в новую таблицу
$stmt = $pdo->prepare('INSERT INTO feed_new (id_user, type_feed, id_object) values(:id_user, 1, :city)');
$stmt->execute(array(':id_user' => $user, ':city' => $new_id_city));

            // для всех
            $text = '<a href="/chat.php?channel='.$id_channel.'&" role="external">'.$feed->city_short.'</a> приветствует земляка <a href="profile.php?userprofile='.$profile->id_user.'&" role="external">'.$profile->name.'</a>';
            $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, to_city, text, icon) VALUES (0, :to_city, :text, "server")');
            $stmt->execute(array(':to_city' => $new_id_city, ':text' => $text));
            
        }

        
        // тянем из базы обновленный профиль
//        $smarty->display('header.tpl');
//        $smarty->assign('profile', $profile);   
//        $smarty->display('profile.tpl');    
    
        // или перебрасываем юзера в чат-канал, если показвать обновленный профиль не нужно
        header( 'Location: /onair.php', true, 303 ); 
    
    }
    elseif ($_POST['setStatus']) //! событие ленты "обновление статуса"
    {
        if ($profile->name == '0')
        {
            exit('nonick');
        }
        else
        {
            $status = $_POST['setStatus'];
            $stmt = $pdo->prepare('UPDATE `users` SET `status` = :status WHERE `id_user` = :id_user');
            $stmt->execute(array(':status' => $status, ':id_user' => $user));
            
// запись в новую таблицу
$stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed_new` (id_user, type_feed, text) VALUES (:id_user, 8, :status)');
$stmt->execute(array(':status' => $status, ':id_user' => $user));

            $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO `feed` (id_user, text, icon) VALUES (:id_user, :status, "status")');
            $stmt->execute(array(':status' => $status, ':id_user' => $user));
            exit($status);
        }
    }
    else
    {
        // если нет POST, а есть GET, то это запрос на просмотр чужого профиля
        if ( $_GET['userprofile'] )
        {
            $about = $_GET['userprofile'];
            $profile = getUser($about);
            $smarty->display('header.tpl');
            $smarty->assign('profile', $profile);
            $smarty->display('profile_view.tpl');            
        }
        else
        {    
            // а если нет вообще нихуя, то это запрос на просмотр своего профиля
            $smarty->display('header.tpl');
            $smarty->assign('profile', $profile);
            $smarty->display('profile.tpl');    
        }        
    }


}
else
{
    header( 'Location: /login.html', true, 303 );     
    exit();    
}

$smarty->display('footer.tpl');    



function getUser($id)
{
    global $pdo;
    //if ($_COOKIE['language']) $lang = $_COOKIE['language'];
    //else $lang = 'en';
    $stmt = $pdo->query("SELECT users.id_user, users.name, users.status, users.gender, users.password, CONCAT_WS(', ',cities.name, cities.region, countries.name_en) as city, users.id_city, motorcycles.id_motorcycle, motorcycles.name as motorcycle, users.motorcycle_more, users.phone, users.help_repair, users.help_garage, users.help_food, users.help_bed, users.help_beer, users.help_strong, users.help_party, users.help_excursion, users.description, UNIX_TIMESTAMP(users.date_last_login) as date_last_login
    FROM users 
    LEFT JOIN cities ON users.id_city = cities.id_city
    LEFT JOIN countries ON (cities.id_country = countries.id_country)
    LEFT JOIN motorcycles ON motorcycles.id_motorcycle = users.id_motorcycle
    WHERE users.id_user = ".$id
    );
    $about = $stmt->fetch();
    $about->is_friend = isFriend($id);
    return $about;
}




function isFriend($id)
{
    global $pdo;
    $stmt = $pdo->query("select id_friend from friends where id_user = ".$_SESSION['id_user']);
    while ($row = $stmt->fetchColumn() )
    {
        if ($row == $id) 
        {
            $is_friend = 1;
            return $is_friend;
        }
    }
    return $is_friend = 0;
}




?>