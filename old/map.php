<?php
    session_start();

    error_reporting(E_ALL);
    include_once('../../config/config.inc.php');
    include_once('functions.php');
    include_once('../../classes/Hotels.php');        
    if (!isset($smarty))
	exit('Smarty умер');


if ($_GET['lat'] AND $_GET['lng'])
{
    $smarty->assign(array('lat' => $_GET['lat'], 'lng' => $_GET['lng']));
}
 
if ($_POST) // вызывается из gmaps.js
{
   
    if ($_POST['task'] == 'getPOIs')
    {
        // костыль для 2 версии
        if ($_POST['id_user']) $id_user = $_POST['id_user']; 
        else $id_user = $_SESSION['id_user'];
        
        $hotels = getHotels($_POST['bounds_sw_lat'],$_POST['bounds_sw_lng'],$_POST['bounds_ne_lat'],$_POST['bounds_ne_lng']);
        $parkings = getParkings($_POST['bounds_sw_lat'],$_POST['bounds_sw_lng'],$_POST['bounds_ne_lat'],$_POST['bounds_ne_lng']);       
        $places = getPlaces($_POST['bounds_sw_lat'],$_POST['bounds_sw_lng'],$_POST['bounds_ne_lat'],$_POST['bounds_ne_lng']);       
        $services = getServices($_POST['bounds_sw_lat'],$_POST['bounds_sw_lng'],$_POST['bounds_ne_lat'],$_POST['bounds_ne_lng']);       
        $tireservices = getTireservices($_POST['bounds_sw_lat'],$_POST['bounds_sw_lng'],$_POST['bounds_ne_lat'],$_POST['bounds_ne_lng']);
        $trips = getTrips($id_user, $_POST['bounds_sw_lat'],$_POST['bounds_sw_lng'],$_POST['bounds_ne_lat'],$_POST['bounds_ne_lng']);      

//print_r();die;  
        
        if ($hotels == NULL) $hotels = array();
        if ($parkings == NULL) $parkings = array();
        if ($places == NULL) $places = array();
        if ($services == NULL) $services = array();
        if ($tireservices == NULL) $tireservices = array();
        if ($trips == NULL) $trips = array();        
        
        $pois = array_merge($hotels, $parkings, $places, $services, $tireservices, $trips);
//$pois = $trips;        
     
        echo "<?xml version=\"1.0\"?>\r\n<markers>"; 
        foreach ($pois as $poi) {
            echo "<marker>\r\n";
                // общие
                if ($poi->lat) echo "<lat>{$poi->lat}</lat>\r\n";
                if ($poi->lng) echo "<lng>{$poi->lng}</lng>\r\n";
                if ($poi->owner) echo "<owner>{$poi->owner}</owner>\r\n";        
                if ($poi->photo) echo "<photo>{$poi->photo}</photo>\r\n";        
                if ($poi->map_icon) echo "<map_icon>{$poi->map_icon}</map_icon>\r\n";
                if ($poi->id) echo "<id>{$poi->id}</id>\r\n";            
                if ($poi->date_upd) echo "<date_upd>{$poi->date_upd}</date_upd>\r\n";            
                // групповые            
                if ($poi->phone1) echo "<phone1>{$poi->phone1}</phone1>\r\n";    
                if ($poi->phone2) echo "<phone2>{$poi->phone2}</phone2>\r\n";        
                if ($poi->name) echo "<name>{$poi->name}</name>\r\n";
                if ($poi->price) echo "<price>{$poi->price}</price>\r\n";        
                if ($poi->description) echo "<description>{$poi->description}</description>\r\n";                
                // hotels
                if ($poi->parking) echo "<parking>{$poi->parking}</parking>\r\n";
                if ($poi->ac) echo "<ac>{$poi->ac}</ac>\r\n";    
                if ($poi->wifi) echo "<wifi>{$poi->wifi}</wifi>\r\n";    
                if ($poi->sauna) echo "<sauna>{$poi->sauna}</sauna>\r\n";    
                // parking
                if ($poi->access) echo "<access>{$poi->access}</access>\r\n";
                if ($poi->camera) echo "<camera>{$poi->camera}</camera>\r\n";
                if ($poi->security) echo "<security>{$poi->security}</security>\r\n";
                if ($poi->big) echo "<big>{$poi->big}</big>\r\n";
                // services
                if ($poi->electric) echo "<electric>{$poi->electric}</electric>\r\n";                        
                if ($poi->weld) echo "<weld>{$poi->weld}</weld>\r\n";
                if ($poi->stock) echo "<stock>{$poi->stock}</stock>\r\n";                        
                if ($poi->tuning) echo "<tuning>{$poi->tuning}</tuning>\r\n";                                    
                if ($poi->renewal) echo "<renewal>{$poi->renewal}</renewal>\r\n";                                                
                if ($poi->germans) echo "<germans>{$poi->germans}</germans>\r\n";                                                            
                if ($poi->japanese) echo "<japanese>{$poi->japanese}</japanese>\r\n";
                if ($poi->chinese) echo "<chinese>{$poi->chinese}</chinese>\r\n";            
                // tireservices
                if ($poi->podkat) echo "<podkat>{$poi->podkat}</podkat>\r\n";                        
                if ($poi->balancer) echo "<balancer>{$poi->balancer}</balancer>\r\n";                                    
                if ($poi->rims) echo "<rims>{$poi->rims}</rims>\r\n";                                                
                if ($poi->tire_repair) echo "<tire_repair>{$poi->tire_repair}</tire_repair>\r\n";      
                // trips
                if ($poi->profile) echo "<profile>{$poi->profile}</profile>\r\n";                
                if ($poi->motorcycle) echo "<motorcycle>{$poi->motorcycle}</motorcycle>\r\n";                                                                            
                if ($poi->city) echo "<city>{$poi->city}</city>\r\n";
                if ($poi->seen_here) echo "<seen_here>{$poi->seen_here}</seen_here>\r\n";                
                if ($poi->is_friend) echo ("<is_friend>{$poi->is_friend}</is_friend>\r\n");
    
            echo "</marker>\r\n"; 
        }
        echo '</markers>';
        }
        exit;
        
        

}

if ($_SESSION['id_user'])
{
    $smarty->display('header.tpl');
    $smarty->display('map.tpl');
    $smarty->display('footer.tpl');    
}
else 
{
    header( 'Location: /login.html', true, 303 );     
    exit();
}