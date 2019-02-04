<?php
    session_start();
    include_once('../../config/config.inc.php');
    include_once('functions.php');

$update = $_GET['lastupdate'];

if ($_SESSION['id_user'])
{
    // тащим только тех, у кого отмечен хотя бы один пункт помощи в профиле          
    $query = 'SELECT id_user, users.name, cities.name as city_name, phone, help_repair,help_garage, help_food, help_bed, help_beer, help_strong, help_party, help_excursion, description, gender, UNIX_TIMESTAMP(date_add) as date_add, date_upd, cities.lat, cities.lng 
    from users 
    JOIN cities on users.id_city = cities.id_city
    where 1 and (help_repair = 1 or help_garage = 1 or help_food = 1 or help_bed = 1 or help_beer = 1 or help_strong = 1 or help_party = 1 or help_excursion = 1)';
    if ($update) 
    {
        $query .= ' and CONVERT_TZ(date_upd, @@session.time_zone, \'+00:00\') > :lastupdate';
    }
     
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':lastupdate' => $update));
    $helplist = $stmt->fetchAll();
    
 //   echo '<pre>';

    echo json_encode($helplist);
  //  print_r($helplist);
    
}
else 
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}
?>