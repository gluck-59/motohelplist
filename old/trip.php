<?php
    session_start();

    error_reporting(E_ALL);
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    include_once(dirname(__FILE__).'/../classes/Hotels.php');        
    if (!isset($smarty))
	exit('Smarty умер');
	
	
	
if ($_SESSION['id_user'])
{
	if ($_POST['task'] == 'updateTrip')
	{
        $stmt = $pdo->prepare('INSERT LOW_PRIORITY INTO trips 
        (id_user, lat, lng, map_icon, date_upd, ts) VALUES 
        (:id_user, :lat, :lng, "trip", FROM_UNIXTIME(:date_upd/1000), :date_upd) 
        ON DUPLICATE KEY UPDATE lat=:lat, lng=:lng, date_upd=FROM_UNIXTIME(:date_upd/1000), ts=:date_upd
        ');

        // echo возвращает 1 если все заебись и 0 если ошибка
        echo $stmt->execute(array(':id_user' => $_POST['id_user'], ':lat' => $_POST['lat'], ':lng' => $_POST['lng'], ':date_upd' => $_POST['ts']));
        die();
	}
	

	if ($_POST['task'] == 'deleteTrip')
	{
        $stmt = $pdo->prepare('DELETE LOW_PRIORITY FROM trips WHERE id_user = :id_user');

        // echo возвращает 1 если все заебись и 0 если ошибка        
        echo $stmt->execute(array(':id_user' => $_SESSION['id_user']));
        die;
    }	
	


    $smarty->display('header.tpl');
    $smarty->display('trip.tpl');
    $smarty->display('footer.tpl');    
}